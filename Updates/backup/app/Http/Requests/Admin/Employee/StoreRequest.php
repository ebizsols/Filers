<?php

namespace App\Http\Requests\Admin\Employee;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends CoreRequest
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
    public function rules()
    {
        $setting = global_setting();
        $rules = [
            'employee_id' => 'required|unique:employee_details|max:100',
            'name' => 'required|max:50',
            'email' => 'required|email:rfc|unique:users|max:100',
            'password' => 'required|min:8|max:50',
            'slack_username' => 'nullable|unique:employee_details,slack_username|max:30',
            'hourly_rate' => 'nullable|numeric',
            'joining_date' => 'required',
            'last_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
            'department' => 'required',
            'designation' => 'required',
            'country' => 'required_with:mobile',
        ];

        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');

            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\Models\CustomField::findOrFail($id);

                if ($customField->required == 'yes' && (is_null($value) || $value == '')) {
                    $rules['custom_fields_data['.$key.']'] = 'required';
                }
            }

        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');

            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\Models\CustomField::findOrFail($id);

                if ($customField->required == 'yes') {
                    $attributes['custom_fields_data['.$key.']'] = $customField->label;
                }
            }
        }

        return $attributes;
    }

}
