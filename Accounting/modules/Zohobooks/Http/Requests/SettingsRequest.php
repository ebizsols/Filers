<?php

namespace Modules\Zohobooks\Http\Requests;

use App\Abstracts\Http\FormRequest;

class SettingsRequest extends FormRequest
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
        $rules = [
            'client_id'     => 'required|string|size:35',
            'client_secret' => 'required|string|size:42',
        ];

        return $rules;
    }
}
