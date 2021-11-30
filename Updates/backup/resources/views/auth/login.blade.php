<x-auth>
    <form id="login-form" action="{{ route('login') }}" class="ajax-form" method="POST">
        {{ csrf_field() }}
        <section class="bg-grey py-5 login_section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="login_box mx-auto rounded bg-white text-center">
                            <h3 class="text-capitalize mb-4 f-w-500">@lang('app.login')</h3>

                            <script>
                                const facebook = "{{ route('social_login', 'facebook') }}";
                                const google = "{{ route('social_login', 'google') }}";
                                const twitter = "{{ route('social_login', 'twitter') }}";
                                const linkedin = "{{ route('social_login', 'linkedin') }}";
                            </script>

                            @if ($socialAuthSettings->google_status == 'enable')
                                <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = google;">
                                    <span>
                                        <img src="{{ asset('images/google.png') }}" alt="Google"/>
                                    </span>
                                    @lang('auth.signInGoogle')</a>
                            @endif
                            @if ($socialAuthSettings->facebook_status == 'enable')
                                <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = facebook;">
                                    <span>
                                        <img src="{{ asset('images/fb.png') }}" alt="Google"/>
                                    </span>
                                    @lang('auth.signInFacebook')
                                </a>
                            @endif
                            @if ($socialAuthSettings->twitter_status == 'enable')
                                <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = twitter;">
                                    <span>
                                        <img src="{{ asset('images/twitter.png') }}" alt="Google"/>
                                    </span>
                                    @lang('auth.signInTwitter')
                                </a>
                            @endif
                            @if ($socialAuthSettings->linkedin_status == 'enable')
                                <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = linkedin;">
                                    <span>
                                        <img src="{{ asset('images/linkedin.png') }}" alt="Google"/>
                                    </span>
                                    @lang('auth.signInLinkedin')
                                </a>
                            @endif

                            @if ($socialAuthSettings->social_auth_enable)
                                <p class="position-relative my-4">@lang('auth.useEmail')</p>
                            @endif

                            <div class="form-group text-left">
                                <label for="email" class="f-w-500">@lang('auth.email')</label>
                                <input tabindex="1" type="email" name="email" class="form-control height-50 f-15 light_text"
                                       autofocus placeholder="e.g. admin@example.com" id="email">
                                <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                            </div>

                            @if ($socialAuthSettings->social_auth_enable)
                                <button type="submit" id="submit-next"
                                        class="btn-primary f-w-500 rounded w-100 height-50 f-18 ">@lang('auth.next') <i
                                        class="fa fa-arrow-right pl-1"></i></button>
                            @endif

                            <div id="password-section"
                                 @if ($socialAuthSettings->social_auth_enable) class="d-none" @endif>
                                <div class="form-group text-left">
                                    <label for="password">@lang('app.password')</label>
                                    <input type="password" tabindex="2" name="password"
                                           class="form-control height-50 f-15 light_text" placeholder="Password"
                                           id="password">


                                </div>
                                <div class="forgot_pswd mb-3">
                                    <a href="{{ url('forgot-password') }}">@lang('app.forgotPassword')</a>
                                </div>

                                <div class="form-group text-left">
                                    <input id="checkbox-signup" type="checkbox" name="remember">
                                    <label for="checkbox-signup">@lang('app.rememberMe')</label>
                                </div>

                                @if($setting->google_recaptcha_status == 'active' && $setting->google_recaptcha_v2_status == 'active')
                                    <div class="form-group" id="captcha_container"></div>
                                @endif

                                @if ($errors->has('g-recaptcha-response'))
                                    <div
                                        class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}</div>
                                @endif

                                <button
                                    type="submit"
                                    id="submit-login"
                                    class="btn-primary f-w-500 rounded w-100 height-50 f-18">
                                    @lang('app.login') <i class="fa fa-arrow-right pl-1"></i>
                                </button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>
    </form>

    <x-slot name="scripts">

        @if($setting->google_recaptcha_status == 'active' && $setting->google_recaptcha_v2_status == 'active')
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                    async defer></script>
            <script>
                var gcv3;
                var onloadCallback = function () {
                    // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                    // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                    gcv3 = grecaptcha.render('captcha_container', {
                        'sitekey': '{{$setting->google_recaptcha_v2_site_key}}',
                        'theme': 'light',
                        'callback': function (response) {
                            if (response) {
                                $('#g_recaptcha').val(response);
                            }
                        },
                    });
                };
            </script>
        @endif
        @if($setting->google_recaptcha_status == 'active' && $setting->google_recaptcha_v3_status == 'active')
            <script
                src="https://www.google.com/recaptcha/api.js?render={{$setting->google_recaptcha_v3_site_key}}"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{$setting->google_recaptcha_v3_site_key}}').then(function (token) {
                        // Add your logic to submit to your backend server here.
                        $('#g_recaptcha').val(token);
                    });
                });
            </script>
        @endif

        <script>

            $(document).ready(function () {

                $('#submit-next').click(function () {
                    const url = "{{ route('check_email') }}";
                    $.easyAjax({
                        url: url,
                        container: '#login-form',
                        disableButton: true,
                        buttonSelector: "#submit-next",
                        type: "POST",
                        data: $('#login-form').serialize(),
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#submit-next').remove();
                                $('#password-section').removeClass('d-none');
                                $("#password").focus();
                            }
                        }
                    })
                });

                $('#submit-login').click(function () {

                    const url = "{{ route('login') }}";
                    $.easyAjax({
                        url: url,
                        container: '.login_box',
                        disableButton: true,
                        buttonSelector: "#submit-login",
                        type: "POST",
                        blockUI: true,
                        data: $('#login-form').serialize(),
                        success: function (response) {
                            if (response.two_factor == false) {
                               window.location.href = "{{ route('dashboard') }}";
                            }
                        }
                    })
                });

                @if(session('message'))
                Swal.fire({
                    icon: 'error',
                    text: '{{ session("message") }}',
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                })
                @endif

            });

        </script>
    </x-slot>

</x-auth>