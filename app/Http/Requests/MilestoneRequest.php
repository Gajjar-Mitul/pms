<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MilestoneRequest extends FormRequest
{
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
    public function rules(){
        if($this->method() == 'PATCH'){
            return [
                'name' => 'required',
                'amount' => 'required',
                'deadline' => 'required'
            ];
        }else{
            return [
                'name' => 'required',
                'amount' => 'required',
                'deadline' => 'required'
            ];
        }
    }

    public function messages(){
        return [
            'name.required' => 'Please Enter Milestone Name',
            'amount.required' => 'Please Enter Project Amount',
            'deadline.required' => 'Please Select Dead Line',
        ];
    }
}
