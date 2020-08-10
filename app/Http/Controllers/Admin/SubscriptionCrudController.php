<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MasterController;
use App\Http\Requests\SubscriptionRequest as StoreRequest;
use App\Http\Requests\SubscriptionRequest as UpdateRequest;

use PayPal\Api\AgreementStateDescriptor;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\MerchantPreferences;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SubscriptionCrudController extends MasterController
{

    private $apiContext;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct(){

        parent::__construct();

        $company = \App\Models\Company::where('is_default', 1)->first();
        if($company){
            \Config::set('paypal.client_id', $company->paypal_client_id);
            \Config::set('paypal.secret', $company->paypal_secret);
        }
        $paypalConf = \Config::get('paypal');
		/*$paypalConf['client_id'] = 'AdCpLIlE528OLfuUBCpMG2ZyXO3Om5EmSKDnsjKZNBoj68r6ElMraX4PeV-ac8WtCvovQUZF_9RIja-x';
		$paypalConf['secret'] = 'EKXQRFQSdB3Mn8958gNR7YejYhW9mEUk6lXx0psc0rWJwQDvDcqxIMJGbUidJ6N8Ta4n1ZKIdDDkVAxf';
		$paypalConf['settings']['mode'] = 'sandbox';*/
        $this->apiContext = new ApiContext(new OAuthTokenCredential($paypalConf['client_id'], $paypalConf['secret']));
        $this->apiContext->setConfig($paypalConf['settings']);
    }

    /**
     * setup crud.
     * @return void
     */
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Subscription');
        $this->crud->setRoute('admin/subscription');
        $this->crud->setEntityNameStrings('subscription', 'subscriptions');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->removeButton('create');
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->addButtonFromView('top', 'back_to_all_companies', 'back_to_all_companies' , 'beginning');
        $this->crud->addButtonFromView('line', 'subscription_payment', 'subscription_payment' , 'end');
        $this->crud->addButtonFromView('line', 'cancel_subscription', 'cancel_subscription' , 'end');
        $this->crud->addButtonFromView('line', 'cancel_subscription_immediate', 'cancel_subscription_immediate' , 'end');
        $this->crud->enableExportButtons();

        $user = \Auth::guard('admin')->user();
        $company = \Request::query('company');
        if($user->is_master){
            if($company){
                $this->crud->query->whereHas('user', function($query) use($company){
                    $query->where('company_id', $company);
                });
            }
        }else{
            $this->crud->query->where('user_id', $user->id);
        }
        $this->crud->query->orderBy('id', 'DESC');

        /*
        |--------------------------------------------------------------------------
        | Basic Crud column Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumn([
            'name' => 'pay_agreement_id',
            'label' => 'Agreement Id',
        ]);

        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Description',
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Started At'
        ]);
        
        $this->crud->addColumn([
            'name' => 'next_billing_date',
            'label' => 'Next Billing Date',
            'type' => 'closure',
            'function' => function($entry) {
                if($entry->is_trial==1){
                    $trailStartDate = \Carbon\Carbon::parse($entry->start_date);
                    return $trailStartDate->addDays($entry->trial_days)->format('d M Y g:i A');
                }
                else{
                    return $entry->next_billing_date;
                }
            }
        ]);
        $this->crud->addColumn([
            'name' => 'is_trial',
            'label' => 'Type',
            'type' => 'boolean',
            'options' => [0 => 'Paid', 1 => 'Trial']
        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            
        ]);

        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    /**
     * Show subscription plans
     * @return \Illuminate\Http\Response
     */
    public function showSubscriptionPackages(){
        if(!$this->user->hasActiveSubscription()){
            $data['title'] = 'Subscription plan';
            $data['packages'] = \App\Models\Package::get();
            return view('vendor.custom.common.subscription.packages', $data);
        }else{
            \Alert::error(__('admin.opps'))->flash();
            return redirect(url('admin/dashboard'));
        }
    }

    /**
     * Subscribe user in a plan.
     * @return \Illuminate\Http\Response
     */
    public function subscribeSubscription(Request $request, \App\Models\Package $package){
        
        $startDate = '';

        switch ($package->billing_interval) {
            case 'Day':
                $startDate = \Carbon\Carbon::now()->addDay()->format('Y-m-d\TH:i:s\Z');
                break;
            case 'Week':
                $startDate = \Carbon\Carbon::now()->addWeek()->format('Y-m-d\TH:i:s\Z');
                break;
            case 'Month':
                $startDate = \Carbon\Carbon::now()->addMonth()->format('Y-m-d\TH:i:s\Z');
                break;
            case 'Year':
                $startDate = \Carbon\Carbon::now()->addYear()->format('Y-m-d\TH:i:s\Z');
                break;
            default:
                $startDate = \Carbon\Carbon::now()->addMinutes(5)->format('Y-m-d\TH:i:s\Z');
                break;
        }
        $agreement = new Agreement();
        if($package->billing_interval)
        $agreement->setName($package->name)
            ->setDescription("Amount: £".$package->amount)
            ->setStartDate($startDate);
            //->setStartDate(\Carbon\Carbon::now()->toIso8601String());
        /* Set agreement Plan */
        
        $plan = new Plan();
        $plan->setId($package->pay_plan_id);
        $agreement->setPlan($plan);

        /* Overwrite merchant prefeerences */
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(route('paypal.subscription.execute').'?success=true')
            ->setCancelUrl(route('paypal.subscription.execute').'?success=false');

        $agreement->setOverrideMerchantPreferences($merchantPreferences);

        /* Add payer type */
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        try {
            /* Create agreement */
            $agreement = $agreement->create($this->apiContext);
            $approvalUrl = $agreement->getApprovalLink();
            if($approvalUrl) {
                return redirect()->away($approvalUrl);
            }
        }catch (PayPal\Exception\PayPalConnectionException $ex) {
            \Alert::error($ex->getMessage())->flash();
        }catch (\Exception $ex) {
            \Alert::error($ex->getMessage())->flash();
        }
        return redirect(url('admin/subscription/packages'));
    }

    /**
     * Execute subscription status.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function executeSubscription(Request $request){
        if ($request->has('success') && $request->query('success') == 'true') {
            $token = $request->query('token');
            $agreement = new \PayPal\Api\Agreement();
            try {
                // Execute agreement
                $agreement = $agreement->execute($token, $this->apiContext);
                //dd($agreement);
                $subscription = new \App\Models\Subscription();
                $subscription->user_id = $this->user->id;
                $subscription->pay_agreement_id = $agreement->id;
                $subscription->description = $agreement->description;
                $subscription->start_date = \Carbon\Carbon::parse($agreement->start_date)->format('Y-m-d H:i:s');
                $subscription->status = $agreement->state;
                $subscription->save();
                \Alert::success(__('admin.company_subscribed'))->flash();
            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                \Alert::error($ex->getMessage())->flash();
            } catch (\Exception $ex) {
                \Alert::error($ex->getMessage())->flash();
            }
        }else {
            \Alert::error(__('admin.company_not_subscribed'))->flash();
        }
        return redirect(url('admin/dashboard'));
    }

    /**
     * Cancel subscription
     * @param \App\Models\Subscription $subscription
     * @return $response
     */
    public function cancelSubscription(\App\Models\Subscription $subscription){
        $user = \App\User::where("id",$subscription->user_id)->first();
        if($subscription->is_trial==1){
            $subscription->status = 'Cancelled';
            if($subscription->save()){
				\Alert::success(__('admin.company_cancelled_subscription'))->flash();
			}else{
				\Alert::error(__('admin.opps'))->flash();
			}
        }
        else{
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote(__("Cancel the agreement"));
            try{
                $agreement = Agreement::get($subscription->pay_agreement_id, $this->apiContext);
                $res = $agreement->cancel($agreementStateDescriptor, $this->apiContext);
                if($res){
                    $subscription->status = 'Cancelled';
                    if($subscription->save()){
                        \Alert::success(__('admin.company_cancelled_subscription'))->flash();
                    }
                }else{
                    \Alert::error(__('admin.opps'))->flash();
                }
            }catch(\Exception $e){
                \Alert::error($e->getMessage())->flash();
            }
        }
        return redirect(url('admin/subscription?company='.$user->company_id));
    }
    
    /**
     * update subscription status.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function immediateCancelSubscription(\App\Models\Subscription $subscription, Request $request){
        $user = \App\User::where("id",$subscription->user_id)->first();
        if($subscription->is_trial == 1){
            $subscription->status = 'Cancelled';
            if($subscription->save()){
				\Alert::success(__('admin.company_cancelled_subscription'))->flash();
			}else{
				\Alert::error(__('admin.opps'))->flash();
			}
        }
        else{
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote(__("Cancel the agreement immediate"));
            try{
                $agreement = Agreement::get($subscription->pay_agreement_id, $this->apiContext);
				//dd($agreement);
                $res = $agreement->cancel($agreementStateDescriptor, $this->apiContext);
                if($res){
                    $subscription->status = 'Cancelled';
                    $subscription->is_immediate = 1;
                    if($subscription->save()){
                        \Alert::success(__('admin.company_cancelled_subscription'))->flash();
                    }
                }else{
                    \Alert::error(__('admin.opps'))->flash();
                }
            }catch(\Exception $e){
                \Alert::error($e->getMessage())->flash();
            }
        }
        $company = $subscription->owner;
        return redirect(url('admin/subscription?company='.$user->company_id));
    }

}
