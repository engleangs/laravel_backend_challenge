<?php 
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class CustomerLoginRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|alpha_spaces|max:200',
            'email' => 'required|unique:customer,email|email',
            'password' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'name is required.|USR_02|name',
            'email.required'  => 'email is required.|USR_02|email',
            'email.unique'  => 'email already exists.|USR_04|email',
            'email.email'  => 'email already exists.|USR_03|email',
            'password'  => 'password is required.|USR_02|email',
        ];
    }
}