<?php

namespace App\Http\Requests;

use App\User;
use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = User::find($this->get('id'));
        switch ($this->method()) {
            case 'GET':{
                return [
                    'tuning_credit_group_id'=> 'required|integer',
                    'first_name'      => 'required|string|max:191',
                    'last_name'       => 'required|string|max:191',
                    'business_name'   => 'required|string|max:191',
                    'business_name'   => 'required|string|max:191',
                    'address_line_1'  => 'required|string|max:191',
                    'address_line_2'  => 'nullable|string|max:191',
                    'phone'           => 'required|max:30|regex:/^[0-9 ]+$/',
                    'county'          => 'required|string|max:191',
                    'town'            => 'required|string|max:191',
                    'post_code'       => 'nullable|string|max:191',
                    'email'           => 'required|email|unique:users,email',
                ];
            }
            case 'DELETE': {
                    return [];
                }
            case 'POST': {
                    return [
                        'tuning_credit_group_id'=> 'required|integer',
                        'first_name'      => 'required|string|max:191',
                        'last_name'       => 'required|string|max:191',
                        'business_name'   => 'required|string|max:191',
                        'business_name'   => 'required|string|max:191',
                        'address_line_1'  => 'required|string|max:191',
                        'address_line_2'  => 'nullable|string|max:191',
                        'phone'           => 'required|max:30|regex:/^[0-9 ]+$/',
                        'county'          => 'required|string|max:191',
                        'town'            => 'required|string|max:191',
                        'post_code'       => 'nullable|string|max:191',
                        'email'           => 'required|email|unique:users,email',
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'tuning_credit_group_id'=> 'required|integer',
                        'first_name'      => 'required|string|max:191',
                        'last_name'       => 'required|string|max:191',
                        'business_name'   => 'required|string|max:191',
                        'business_name'   => 'required|string|max:191',
                        'address_line_1'  => 'required|string|max:191',
                        'address_line_2'  => 'nullable|string|max:191',
                        'phone'           => 'required|max:30|regex:/^[0-9 ]+$/',
                        'county'          => 'required|string|max:191',
                        'town'            => 'required|string|max:191',
                        'post_code'       => 'nullable|string|max:191',
                        'email'           => 'required|email|unique:users,email,'.$user->id.',id',
                    ];
                }
            default:break;
        }
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tuning_credit_group_id.required' => 'The tuning price type field is required.',
            'tuning_credit_group_id.integer' => 'The tuning price type field format is invalid.',
        ];
    }
}
