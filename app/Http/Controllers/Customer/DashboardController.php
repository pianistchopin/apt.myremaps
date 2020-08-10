<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MasterController;
use App\Models\CustomerRating;
use App\Models\Company;
use Illuminate\Http\Request;
class DashboardController extends MasterController
{

    public function __construct(){
        parent::__construct();
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
		$customerRating = CustomerRating::where(['user_id'=>$this->user->id,'company_id'=>$this->user->company_id])->first();
		
        try{
			$data['customerRating']  = $customerRating;
            $data['title'] = trans('backpack::base.dashboard');
            $data['fileServices'] = $this->user->fileServices()->orderBy('id', 'DESC')->take(5)->get();
            $data['openFileServices'] = $this->user->fileServices()->where('status', 'O')->count();
            $data['waitingFileServices'] = $this->user->fileServices()->where('status', 'W')->count();
            $data['complatedFileServices'] = $this->user->fileServices()->where('status', 'C')->count();
            return view('backpack::dashboard', $data);
        }catch(\Exception $e){
            return abort(404);
        }
    }
	
	public function addRating(Request $request){
		if(isset($request->id) && !empty($request->id) ){
			$id = $request->id;
			$model = CustomerRating::where(['id'=>$id])->first();
		}else{
			$model = new CustomerRating();
		}
		$model->rating = $request->rating;
		$model->user_id = $this->user->id;
		$model->company_id = $this->user->company_id;
		$model->save();
		
		$avgRating = $model::where('company_id',$model->company_id)->avg('rating');
		
		$company = Company::find($model->company_id);
		$company->rating = $avgRating;
		$company->save();
		return redirect(backpack_url('dashboard'))->with('Rating Added');
	}
}
