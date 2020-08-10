<?php

namespace App\Http\Controllers;

use App\Mail\BillingSubscriptionCancelled;
use App\Mail\BillingSubscriptionCreated;
use PayPal\Auth\OAuthTokenCredential;
use App\Mail\BillingPaymentCompleted;
use App\Mail\BillingPaymentPending;
use App\Mail\BillingPaymentDenied;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use PayPal\Api\AgreementDetails;
use Illuminate\Http\Request;
use App\Models\Subscription;
use PayPal\Rest\ApiContext;
use PayPal\Api\Agreement;
use App\Models\Company;
use Mail;
use Log;

class PaypalWebhookController extends Controller{

    private $apiContext;
    private $master;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct(){
        $this->master = Company::where('is_default', 1)->first();
        if($this->master){
            \Config::set('mail.driver', $this->master->mail_driver);
            \Config::set('mail.host', $this->master->mail_host);
            \Config::set('mail.port', $this->master->mail_port);
			\Config::set('mail.encryption', $this->master->mail_encryption);
            \Config::set('mail.username', $this->master->mail_username);
            \Config::set('mail.password', $this->master->mail_password);
            \Config::set('mail.from.address', $this->master->main_email_address);
            \Config::set('mail.from.name', $this->master->name);
            \Config::set('app.name', $this->master->name);
            \Config::set('backpack.base.project_name', $this->master->name);

            \Config::set('paypal.client_id', $this->master->paypal_client_id);
            \Config::set('paypal.secret', $this->master->paypal_secret);
            \Config::set('paypal.settings.mode', $this->master->paypal_mode);

        }
        $paypalConf = \Config::get('paypal');
		/*$paypalConf['client_id'] = 'AdCpLIlE528OLfuUBCpMG2ZyXO3Om5EmSKDnsjKZNBoj68r6ElMraX4PeV-ac8WtCvovQUZF_9RIja-x';
		$paypalConf['secret'] = 'EKXQRFQSdB3Mn8958gNR7YejYhW9mEUk6lXx0psc0rWJwQDvDcqxIMJGbUidJ6N8Ta4n1ZKIdDDkVAxf';
		$paypalConf['settings']['mode'] = 'sandbox';*/
        $this->apiContext = new ApiContext(new OAuthTokenCredential($paypalConf['client_id'], $paypalConf['secret']));
        $this->apiContext->setConfig($paypalConf['settings']);
    }

    /**
     * Handle paypal webhook events.
     * @return void
     */
    public function index(Request $request){

        /* Check event type */
		
        switch ($request->event_type) {
			
            /* Subscription created */
            case 'BILLING.SUBSCRIPTION.CREATED':
                $resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', $resource['id'])->first();
                if($subscription){
                    
                    Mail::to($this->master->owner->email)->send(new BillingSubscriptionCreated($subscription));
                    Log::info('BILLING.SUBSCRIPTION.CREATED:: Subscription created.');

                }else{

                    Log::info('BILLING.SUBSCRIPTION.CREATED:: Agreement doesn\'t exists.');
                }
                break;
            /* Subscription cancelled */
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', $resource['id'])->first();
                if($subscription){
                    $subscription->status = $resource['state'];
                    $subscription->save();

                    Mail::to($this->master->owner->email)->send(new BillingSubscriptionCancelled($subscription));

                    Log::info('BILLING.SUBSCRIPTION.CANCELLED:: Subscription cancelled.');

                }else{

                    Log::info('BILLING.SUBSCRIPTION.CANCELLED:: Agreement doesn\'t exists.');
                }
                break;

            /* Subscription suspended */
            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                $resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', $resource['id'])->first();
                if($subscription){
                    $subscription->status = $resource['state'];
                    $subscription->save();

                    Log::info('BILLING.SUBSCRIPTION.SUSPENDED:: Subscription suspended.');

                }else{

                    Log::info('BILLING.SUBSCRIPTION.SUSPENDED:: Agreement doesn\'t exists.');
                }
                break;

            /* Subscription suspended */
            case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
                $resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', $resource['id'])->first();
                if($subscription){
                    $subscription->status = $resource['state'];
                    $subscription->save();

                    Log::info('BILLING.SUBSCRIPTION.RE-ACTIVATED:: Subscription re-activated.');

                }else{

                    Log::info('BILLING.SUBSCRIPTION.RE-ACTIVATED:: Agreement doesn\'t exists.');
                }
                break;
            
            /* Subscription Payment completed */
            case 'PAYMENT.SALE.COMPLETED':
            	$resource = $request->resource;
				//\Log::info(print_r($resource, true));
                $subscription = Subscription::where('pay_agreement_id', @$resource['billing_agreement_id'])->first();

                if($subscription){
                    $agreement = \PayPal\Api\Agreement::get($subscription->pay_agreement_id, $this->apiContext);
                    $agreementDetails = $agreement->getAgreementDetails();
                    
                    $subscriptionPayment = SubscriptionPayment::where('pay_txn_id', $resource['id'])->first();
                    if(!$subscriptionPayment){
                    	$subscriptionPayment = new SubscriptionPayment();
                    }
                    //\Log::info(print_r($agreementDetails, true));
					//\Log::info(print_r($agreementDetails->getLastPaymentAmount(), true));
                    $subscriptionPayment->subscription_id = $subscription->id;
                    $subscriptionPayment->pay_txn_id = $resource['id'];
                    $subscriptionPayment->next_billing_date = \Carbon\Carbon::parse($agreementDetails->getNextBillingDate())->format('Y-m-d H:i:s');
                    $subscriptionPayment->last_payment_date  = \Carbon\Carbon::parse($agreementDetails->getLastPaymentDate())->format('Y-m-d H:i:s');					
                    //$subscriptionPayment->last_payment_amount  = $agreementDetails->getLastPaymentAmount()->value;
					if(isset($agreementDetails->getLastPaymentAmount()->value)) {
						$subscriptionPayment->last_payment_amount  = $agreementDetails->getLastPaymentAmount()->value;
                    }
					
					$subscriptionPayment->failed_payment_count  = $agreementDetails->getFailedPaymentCount();
                    $subscriptionPayment->status = $resource['state'];

                    if($subscriptionPayment->save()){
                        Mail::to($this->master->owner->email)->send(new BillingPaymentCompleted($subscription));
                    }
                    Log::info('PAYMENT.SALE.COMPLETED:: Payment sale completed.');

                }else{

                    Log::info('PAYMENT.SALE.COMPLETED:: Agreement doesn\'t exists.');
                }
                break;

            /* Subscription Payment Denied */
            case 'PAYMENT.SALE.DENIED':
            	$resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', @$resource['billing_agreement_id'])->first();

                if($subscription){
                	$agreement = \PayPal\Api\Agreement::get($subscription->pay_agreement_id, $this->apiContext);
                    $agreementDetails = $agreement->getAgreementDetails();
                    
                    $subscriptionPayment = SubscriptionPayment::where('pay_txn_id', $resource['id'])->first();
                    if(!$subscriptionPayment){
                    	$subscriptionPayment = new SubscriptionPayment();
                    }

                    $subscriptionPayment->subscription_id = $subscription->id;
                    $subscriptionPayment->pay_txn_id = $resource['id'];
                    $subscriptionPayment->next_billing_date = \Carbon\Carbon::parse($agreementDetails->getNextBillingDate())->format('Y-m-d H:i:s');
                    $subscriptionPayment->last_payment_date  = \Carbon\Carbon::parse($agreementDetails->getLastPaymentDate())->format('Y-m-d H:i:s');
                    $subscriptionPayment->last_payment_amount  = $agreementDetails->getLastPaymentAmount()->value;
                    $subscriptionPayment->failed_payment_count  = $agreementDetails->getFailedPaymentCount();
                	$subscriptionPayment->status = $resource['state'];

                	if($subscriptionPayment->save()){
                		Mail::to($this->master->owner->email)->send(new BillingPaymentPending($subscription));
                	}

                    Log::info('PAYMENT.SALE.Denied:: Payment sale denied.');

                }else{

                    Log::info('PAYMENT.SALE.Denied:: Agreement doesn\'t exists.');
                }
            	break;

            /* Subscription payment pending */ 
            case 'PAYMENT.SALE.PENDING':
            	$resource = $request->resource;
                $subscription = Subscription::where('pay_agreement_id', @$resource['billing_agreement_id'])->first();

                if($subscription){
                	$agreement = \PayPal\Api\Agreement::get($subscription->pay_agreement_id, $this->apiContext);
                    $agreementDetails = $agreement->getAgreementDetails();

                    $subscriptionPayment = SubscriptionPayment::where('pay_txn_id', $resource['id'])->first();
                    if(!$subscriptionPayment){
                    	$subscriptionPayment = new SubscriptionPayment();
                    }

                    $subscriptionPayment->subscription_id = $subscription->id;
                    $subscriptionPayment->pay_txn_id = $resource['id'];
                    $subscriptionPayment->next_billing_date = \Carbon\Carbon::parse($agreementDetails->getNextBillingDate())->format('Y-m-d H:i:s');
                    $subscriptionPayment->last_payment_date  = \Carbon\Carbon::parse($agreementDetails->getLastPaymentDate())->format('Y-m-d H:i:s');
                    $subscriptionPayment->last_payment_amount  = $agreementDetails->getLastPaymentAmount()->value;
                    $subscriptionPayment->failed_payment_count  = $agreementDetails->getFailedPaymentCount();
                	$subscriptionPayment->status = $resource['state'];

                	if($subscriptionPayment->save()){
                		Mail::to($this->master->owner->email)->send(new BillingPaymentPending($subscription));
                	}
                    Log::info('PAYMENT.SALE.PENDING::Payment sale pending.');

                }else{

                    Log::info('PAYMENT.SALE.PENDING:: Agreement doesn\'t exists.');
                }
            	break;
            default:
            break;
        } 


    }
}
