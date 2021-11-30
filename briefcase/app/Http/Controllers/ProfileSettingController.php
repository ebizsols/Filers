<?php

namespace App\Http\Controllers;

use App\Models\Country;

class ProfileSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->activeSettingMenu = 'profile_settings';
    }

    public function index()
    {
        $this->countries = Country::get();
        $this->salutations = ['mr', 'mrs', 'miss', 'dr', 'sir', 'madam'];

        return view('profile-settings.index', $this->data);
    }

}
