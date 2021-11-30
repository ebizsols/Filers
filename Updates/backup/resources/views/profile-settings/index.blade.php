@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
                @method('PUT')
                @include('sections.password-autocomplete-hide')

                <div class="row">
                    <div class="col-lg-12">
                        @php
                            $userImage = $user->hasGravatar($user->email) ? str_replace('?s=200&d=mp', '', $user->image_url) : asset('img/avatar.png');
                        @endphp
                        <x-forms.file allowedFileExtensions="png jpg jpeg" class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.profile.profilePicture')"
                            :fieldValue="($user->image ? $user->image_url : $userImage)" fieldName="image"
                            fieldId="profile-image" :popover="__('modules.themeSettings.logoSize')">
                        </x-forms.file>
                    </div>

                    <div class="col-lg-4">
                        <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr">@lang('modules.profile.yourName')</label>
                        <div class="input-group">
                            <select class="select-picker form-control" name="salutation" id="salutation"
                            data-live-search="true">
                                <option value="">--</option>
                                @foreach ($salutations as $salutation)
                                    <option value="{{ $salutation }}" @if($user->salutation == $salutation) selected @endif >@lang('app.'.$salutation)</option>
                                @endforeach
                            </select>
                            <div class="input-group-append w-70">
                                <input type="text" class="form-control f-14" placeholder="@lang('placeholders.name')"
                                name="name" id="name" value="{{ ucwords($user->name) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.profile.yourEmail')"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.email')" fieldName="email"
                            fieldId="email" :fieldValue="$user->email"></x-forms.text>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.label class="mt-3" fieldId="password" :fieldLabel="__('modules.profile.yourPassword')">
                        </x-forms.label>
                        <x-forms.input-group>

                            <input type="password" name="password" id="password" autocomplete="off"
                                placeholder="@lang('placeholders.password')" class="form-control height-35 f-14">
                            <x-slot name="preappend">
                                <button type="button" data-toggle="tooltip" data-original-title="@lang('app.viewPassword')"
                                    class="btn btn-outline-secondary border-grey height-35 toggle-password"><i
                                        class="fa fa-eye"></i></button>
                            </x-slot>
                            <x-slot name="append">
                                <button id="random_password" type="button" data-toggle="tooltip"
                                    data-original-title="@lang('modules.client.generateRandomPassword')"
                                    class="btn btn-outline-secondary border-grey height-35"><i
                                        class="fa fa-random"></i></button>
                            </x-slot>
                        </x-forms.input-group>
                        <small class="form-text text-muted">@lang('modules.client.passwordUpdateNote')</small>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100"
                                for="usr">@lang('modules.emailSettings.emailNotifications')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.enable')"
                                    fieldName="email_notifications" fieldValue="1" checked="true"
                                    :checked="($user->email_notifications === 1) ? 'checked' : ''">
                                </x-forms.radio>
                                <x-forms.radio fieldId="login-no" :fieldLabel="__('app.disable')" fieldValue="0"
                                    fieldName="email_notifications"
                                    :checked="($user->email_notifications === 0) ? 'checked' : ''">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100" for="usr">@lang('app.rtlTheme')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="rtl-yes" :fieldLabel="__('app.yes')" fieldName="rtl" fieldValue="1"
                                    :checked="($user->rtl === 1) ? 'checked' : ''">
                                </x-forms.radio>
                                <x-forms.radio fieldId="rtl-no" :fieldLabel="__('app.no')" fieldValue="0" fieldName="rtl"
                                    :checked="($user->rtl === 0) ? 'checked' : ''">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.select fieldId="phone_code" :fieldLabel="__('app.country')" fieldName="phone_code"
                            search="true" alignRight="true">
                            <option value="">--</option>
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" {{ $item->id == $user->country_id ? 'selected' : '' }}>
                                    {{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.tel fieldId="mobile" :fieldLabel="__('app.mobile')" fieldName="mobile"
                            :fieldPlaceholder="__('placeholders.mobile')" :fieldValue="$user->mobile"></x-forms.tel>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.changeLanguage')"
                            fieldName="locale" search="true">
                            <option data-content="<span class='flag-icon flag-icon-gb flag-icon-squared'></span> English"
                                {{ user()->locale == 'en' ? 'selected' : '' }} value="en">English
                            </option>
                            @foreach ($languageSettings as $language)
                                <option {{ user()->locale == $language->language_code ? 'selected' : '' }}
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($language->language_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                    value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="gender" :fieldLabel="__('modules.employees.gender')" fieldName="gender"
                            search="true">
                            <option @if ($user->gender == 'male') selected @endif value="male">@lang('app.male')</option>
                            <option @if ($user->gender == 'female') selected @endif value="female">@lang('app.female')</option>
                            <option @if ($user->gender == 'others') selected @endif value="others">@lang('app.others')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group my-3">

                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.profile.yourAddress')"
                                fieldRequired="false" fieldName="address" fieldId="address"
                                :fieldPlaceholder="__('placeholders.address')"
                                :fieldValue="($user->employeeDetails ? $user->employeeDetails->address : $user->clientDetails->address)">
                            </x-forms.textarea>

                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="action">
                <!-- Buttons Start -->
                <div class="w-100 border-top-grey">
                    <x-setting-form-actions>

                        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>

                        <x-forms.button-cancel :link="url()->previous()" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </x-setting-form-actions>
                    <div class="d-flex d-lg-none d-md-none p-4">
                        <div class="d-flex w-100">
                            <x-forms.button-primary class="mr-3 w-100" icon="check">@lang('app.save')
                            </x-forms.button-primary>
                        </div>
                        <x-forms.button-cancel :link="url()->previous()" class="w-100">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </div>
                </div>
                <!-- Buttons End -->
            </x-slot>

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>
        $('#random_password').click(function() {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('#save-form').click(function() {
            var url = "{{ route('profile.update', [$user->id]) }}";
            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                buttonSelector: "#save-form",
                file: true,
                data: $('#editSettings').serialize(),
            });
        });
    </script>
@endpush
