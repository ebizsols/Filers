<?php

namespace App\Http\Requests\Admin\App;

use App\Http\Requests\CoreRequest;

class UpdateAppSetting extends CoreRequest
{

    /** @return true  */
    public function authorize()
    {
        return true;
    }

    /** @return array  */
    public function rules()
    {
        $rules = [];
        $rules['allowed_file_types'] = 'required';

        if(!is_null($this->latitude)){
            $rules['latitude'] = 'required|numeric|between:-90,90|regex:/^\d+(\.\d{1,8})?$/';
        }
        
        if(!is_null($this->longitude)){
            $rules['longitude'] = 'required|numeric|between:-180,180';
        }

        return $rules;
    }

}
