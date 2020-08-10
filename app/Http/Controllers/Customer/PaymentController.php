<?php

namespace App\Http\Controllers\Customer;

use PayPal\Exception\PayPalConnectionException;
use App\Http\Controllers\MasterController;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\Payer;

class PaymentController extends MasterController
{

    private $apiContext;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

        parent::__construct();
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPaymentWithpaypal(Request $request){
		/** setup PayPal api context **/
        $paypalConf = \Config::get('paypal');
        $this->apiContext = new ApiContext(new OAuthTokenCredential($paypalConf['client_id'], $paypalConf['secret']));
        $this->apiContext->setConfig($paypalConf['settings']);
		
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($request->item_name) /** item name **/
            ->setCurrency($this->company->paypal_currency_code)
            ->setQuantity(1)
            ->setPrice($request->item_amount); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems([$item]);

        $details = new Details(); 
        $details->setShipping(0.00)
                ->setTax($request->item_tax)
                ->setSubtotal($request->item_amount);

        $amount = new Amount();
        $amount->setCurrency($this->company->paypal_currency_code)
            ->setTotal($request->total_amount)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($request->item_description)
            ->setInvoiceNumber(rand(88888888, 99999999));

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('paypal.payment.status'))
            ->setCancelUrl(route('paypal.payment.status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try{
            $payment->create($this->apiContext);

        }catch(PayPal\Exception\PayPalConnectionException $ex) {
            \Alert::error($ex->getMessage())->flash();
            return redirect()->route('buy.credit');
        }catch (\Exception $ex) {
            \Alert::error($ex->getMessage())->flash();
            return redirect()->route('buy.credit');
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        /** add payment ID to session **/
        \Session::put([
            'paypal_payment_id'=> $payment->getId(),
            'item_credits'=> $request->item_credits,
            'vat_number'=> $request->vat_number,
            'vat_percentage'=> $request->vat_percentage,
        ]);

        if(isset($redirect_url)) {
            /** redirect to paypal **/
            return redirect()->away($redirect_url);
        }
        \Alert::error('Unknown error occurred')->flash();
        return redirect()->route('buy.credit');
    }

    /**
     * handle payment status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentStatus(Request $request){
        $payment_id = \Session::get('paypal_payment_id');
        $item_credits = \Session::get('item_credits');
        $vat_number = \Session::get('vat_number');
        $vat_percentage = \Session::get('vat_percentage');

        \Session::forget('paypal_payment_id');
        \Session::forget('item_credits');
        \Session::forget('vat_number');
        \Session::forget('vat_percentage');

        if (empty($request->PayerID) || empty($request->token)) {
            \Alert::error('Payment failed')->flash();
            return redirect()->route('buy.credit');
        }
        try{
			/** setup PayPal api context **/
			$paypalConf = \Config::get('paypal');
			$this->apiContext = new ApiContext(new OAuthTokenCredential($paypalConf['client_id'], $paypalConf['secret']));
			$this->apiContext->setConfig($paypalConf['settings']);
			
            $payment = Payment::get($payment_id, $this->apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);
            $result = $payment->execute($execution, $this->apiContext);
            $transactions = $result->getTransactions(); 
            $transaction = $transactions[0]; 
            $relatedResources = $transaction->getRelatedResources(); 
            $relatedResource = $relatedResources[0]; 
            $paypalOrder = $relatedResource->getOrder();
            $user = \Auth::guard('customer')->user();
            $totalCredits = ($user->tuning_credits+$item_credits);
            $user->tuning_credits = $totalCredits;
            $user->save();

            /* save order displayable id */
            $displableId = \App\Models\Order::wherehas('user', function($query) use($user){
                $query->where('company_id', $user->company_id);
            })->max('displayable_id');

            $displableId++;

            /* save order */
            $order = new \App\Models\Order();
            $order->user_id = $user->id;
            $order->transaction_id = $result->getId();
            $order->invoice_id = $transaction->invoice_number;
            $order->vat_number = $vat_number;
            $order->vat_percentage = $vat_percentage;
            $order->tax_amount = $transaction->amount->details->tax;
            $order->amount = $transaction->amount->total;
            $order->description = $transaction->description;
            $order->status = config('site.order_status.completed');
            $order->displayable_id = $displableId;
            $order->save();

            /* save transaction */
            $transaction = new \App\Models\Transaction();
            $transaction->user_id = \Auth::guard('customer')->user()->id;
            $transaction->credits = number_format($item_credits, 2);
            $transaction->description = "Tuning credits purchase";
            $transaction->status = config('site.transaction_status.completed');
            $transaction->save();
            
            if ($result->getState() == 'approved') { 
                \Alert::success(__('customer.payment_success'))->flash();
                return redirect()->route('buy.credit');
            }
            \Alert::error(__('customer.payment_failed'))->flash();
            return redirect()->route('buy.credit');
        }catch(\Exception $ex){
            \Alert::error($ex->getMessage())->flash();
            return redirect()->route('buy.credit');
        }
    }

}
