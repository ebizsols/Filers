<?php

namespace App\Http\Requests\User;

use App\Http\Requests\CoreRequest;

class UpdateProfile extends CoreRequest
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
        return [
            'email' => 'required|email:rfc|unique:users,email,'.$this->route('profile'),
            'name'  => 'required|max:50',
            'password'  => 'nullable|min:8|max:50',
            'image' => 'image|max:2048',
            'mobile' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
          'image.image' => 'Profile picture should be an image'
        ];
    }

}
