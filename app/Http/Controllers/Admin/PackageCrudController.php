<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MasterController;
use App\Http\Requests\PackageRequest as StoreRequest;
use App\Http\Requests\PackageRequest as UpdateRequest;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\MerchantPreferences; 
use PayPal\Api\PaymentDefinition; 
use PayPal\Common\PayPalModel;
use PayPal\Api\PatchRequest;
use PayPal\Rest\ApiContext;
use PayPal\Api\ChargeModel; 
use PayPal\Api\Currency; 
use PayPal\Api\Patch; 
use PayPal\Api\Plan;

/**
 * Class PackageCrudController
 * @param App\Http\Controllers\Admin
 * @return CrudPanel $crud
 */
class PackageCrudController extends MasterController
{

    /**
     * Class Setup
     */
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Package');
        $this->crud->setRoute('admin/package');
        $this->crud->setEntityNameStrings('package', 'packages');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->query->orderBy('id', 'DESC');

        /*
        |--------------------------------------------------------------------------
        | Basic Crud column Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);

        $this->crud->addColumn([
            'name' => 'billing_interval',
            'label' => 'Billing Interval',
        ]);

        $this->crud->addColumn([
            'name' => 'amount_with_current_sign',
            'label' => 'Amount'
        ]); 

        /*
        |--------------------------------------------------------------------------
        | Basic Crud Field Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addField([
            'name' => 'name',
            'label' => "Name",
            'type' => 'text',
            'attributes'=>['placeholder'=>'Name'],
            'wrapperAttributes'=>['class'=>'form-group col-md-6 col-xs-12']
        ]);

        $this->crud->addField([
            'name'=> 'blank',
            'type' => 'blank',
        ]);

        $this->crud->addField([
            'name' => 'billing_interval',
            'label' => "Billing Interval",
            'type' => 'select_from_array',
            'options' => config('site.package_billing_interval'),
            'allows_null' => true,
            'wrapperAttributes'=>['class'=>'form-group col-md-6 col-xs-12']
        ]);

        $this->crud->addField([
            'name'=> 'blank1',
            'type' => 'blank',
        ]);

        $this->crud->addField([
            'name' => 'amount',
            'label' => "Amount",
            'type' => 'number',
            'attributes'=>['placeholder'=>'Amount'],
            'wrapperAttributes'=>['class'=>'form-group col-md-6 col-xs-12']
        ]);

        $this->crud->addField([
            'name'=> 'blank2',
            'type' => 'blank',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => "Description",
            'type' => 'wysiwyg',
        ]);

        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    /**
     * Store resource
     * @param App\Http\Request\StoreRequest $request
     * @return $response
     */
    public function store(StoreRequest $request){
        $paypal_conf = config('paypal');
        $apiContext = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $apiContext->setConfig($paypal_conf['settings']);

        $plan = new Plan();
        $plan->setName($request->name)
            ->setDescription($request->name)
            ->setType('INFINITE');

        $paymentDefinition = new PaymentDefinition();

        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency($request->billing_interval)
            ->setFrequencyInterval('1')
            ->setCycles('0')
            ->setAmount(new Currency([
                'value' => $request->amount, 
                'currency' => $this->company->paypal_currency_code
            ]));

        $chargeModel = new ChargeModel();
        $chargeModel->setType('SHIPPING')
            ->setAmount(new Currency([
                'value' => 0, 
                'currency' => $this->company->paypal_currency_code
            ]));
        $paymentDefinition->setChargeModels([$chargeModel]);

        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(route('paypal.subscription.execute').'?success=true')
            ->setCancelUrl(route('paypal.subscription.execute').'?success=false')
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CANCEL")
            ->setMaxFailAttempts('0')
            ->setSetupFee(new Currency([
                'value' => $request->amount, 
                'currency' => $this->company->paypal_currency_code
            ]));

        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);
        
        try {
            $createdPlan = $plan->create($apiContext);
            try{
                $patch = new Patch();
                $value = new PayPalModel('{"state":"ACTIVE"}');

                $patch->setOp('replace')
                  ->setPath('/')
                  ->setValue($value);
                $patchRequest = new PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update($patchRequest, $apiContext);
                $request->request->add(['pay_plan_id'=>$createdPlan->getId()]);
                $redirect_location = parent::storeCrud($request);
                return $redirect_location;

            }catch (PayPal\Exception\PayPalConnectionException $ex) {
                \Alert::error($ex->getMessage())->flash();
            }catch (\Exception $ex) {
                \Alert::error($ex->getMessage())->flash();
            }
        }catch (PayPal\Exception\PayPalConnectionException $ex) {
            \Alert::error($ex->getMessage())->flash();
        }catch (\Exception $ex) {
            \Alert::error($ex->getMessage())->flash();
        }

        return redirect(url('admin/package'));
    }

    /**
     * Update resource
     * @param App\Http\Request\UpdateRequest $request
     * @return $response
     */
    public function update(UpdateRequest $request){
        $redirect_location = parent::updateCrud($request);
        return $redirect_location;
    }
}
