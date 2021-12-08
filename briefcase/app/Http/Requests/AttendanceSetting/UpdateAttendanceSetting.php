<?php

namespace App\Http\Requests\AttendanceSetting;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceSetting extends CoreRequest
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
        $data = [];

        if($this->radius_check == 'yes')
        {
            $data['radius'] = 'required';
        }

        if($this->has('alert_after_status') && $this->has('alert_after_status') == 'on')
        {
            $data['alert_after'] = 'required';
        }

        $data['office_start_time'] = 'required';
        $data['office_end_time'] = 'required';
        $data['late_mark_duration'] = 'required | integer | min:0';
        $data['clockin_in_day'] = 'required | integer | min:0';

        if($this->has('halfday_mark_time')) {
            $data['halfday_mark_time'] = 'after:office_start_time';
        }

        return $data;
    }

}
