<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
                'title' => 'required',
                // 'description' => 'required',
                'client_name' => 'required',
                'budget' => 'required|digits_between:3,10',
                'deadline' => 'required'
            ];
        }else{
            return [
                'title' => 'required',
                // 'description' => 'required',
                'client_name' => 'required',
                'budget' => 'required|digits_between:3,10',
                'deadline' => 'required'
            ];
        }
    }

    public function messages(){
        return [
            'title.required' => 'Please Enter Project Title',
            // 'description.required' => 'Please Enter Project Description',
            'client_name.required' => 'Please Enter Client Name',
            'budget.required' => 'Please Enter Budget',
            'deadline.required' => 'Please Select Dead Line',
        ];
    }
}
