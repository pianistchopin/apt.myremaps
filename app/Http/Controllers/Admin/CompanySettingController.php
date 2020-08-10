<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MasterController;
use App\Http\Requests\CompanySettingRequest;
use App\Models\Company;

class CompanySettingController extends MasterController
{
	

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Show the user a form to change his personal information.
     */
    public function showSetting()
    {
        $data['title'] = "Company information";
        return view('vendor.custom.common.settings.company_setting', $data);
    }

    /**
     * Update resource
     * @param App\Http\Request\UpdateRequest $request
     * @return $response
     */
    public function update(CompanySettingRequest $request){
       //die('3242');
		try{
            
            $companySetting = $this->user->company->update($request->all());
			//dd($companySetting);
            if($companySetting){
                if($request->hasFile('file')){
                    if($request->file('file')->isValid()){
                        $file = $request->file('file');
                        $filename = time() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('/uploads/logo'), $filename);
                        $this->user->company->logo = $filename;
                        $this->user->company->save();
                    }
                }
				
				$this->user->company->owner->email = $request->main_email_address;
                $this->user->company->owner->save();

                \Alert::success(__('admin.company_info_updated'))->flash();
            }else{
                \Alert::error(__('admin.opps'))->flash();
            }
        }catch(\Exception $e){
            \Alert::error(__('admin.opps'))->flash();
        	//dd($e);
		}
        return redirect()->route('company.setting')->with('tabName', $request->tab_name);

    }

}
