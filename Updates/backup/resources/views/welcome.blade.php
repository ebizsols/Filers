<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">

    <!-- Template CSS -->
    <link href="{{ asset('vendor/froiden-helper/helper.css') }}" rel="stylesheet">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>Worksuite - Login</title>

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $global->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
</head>

<body>

    <header class="sticky-top d-flex justify-content-center align-items-center login_header bg-white">
        <img class="pr-2" src="{{ asset("images/logo.png") }}" alt="Logo" />
        <h3 class="mb-0 pl-1">Worksuite</h3>
    </header>

    <section class="bg-grey py-5 login_section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="login_box mx-auto rounded bg-white text-center">
                        <h3 class="text-capitalize mb-4 f-w-500">log in</h3>
                        <a class="mb-3 height_50 rounded f-w-500" href="javascript:;"><img src="{{ asset("images/google.png") }}" alt="Google" />Sign in with Google</a>
                        <a class="mb-3 height_50 rounded f-w-500 d-none" href="javascript:;"><img src="images/fb.png" alt="Google" />Sign in with Facebook</a>
                        <p class="position-relative my-4">or, use my email address</p>

                        <form class="ajax-form" id="login-form" method="POST">
                            @include('sections.password-autocomplete-hide')
                            <div class="form-group text-left">
                                <label for="email" class="f-w-500">Email address</label>
                                <input type="email" class="form-control height-50 f-15 light_text" placeholder="e.g. admin@example.com" id="email">
                            </div>
                            <div class="form-group text-left d-none">
                                <label for="password">Password</label>
                                <input type="password" class="form-control height-50 f-15 light_text" placeholder="Password" id="password">
                            </div>

                            <button type="button" id="store-task" class="spinner-button blue_btn form_btn f-w-500 rounded w-100 height-50 f-18">Next <i class="fa fa-arrow-right pl-1"></i></button>

                        </form>
                    </div>
                    <div class="forgot_pswd mt-3">
                        <a href="">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Required Javascript -->
    <script src="{{ asset('vendor/bootstrap/javascript/bootstrap-native.js') }}"></script>

    <!-- Font Awesome -->
    <script src="{{ asset('vendor/jquery/all.min.js') }}"></script>

    <!-- Template JS -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('vendor/froiden-helper/helper.js') }}"></script>
    <script>
        $('#store-task').click(function () {
            $.easyAjax({
                url: '',
                container: '#login-form',
                disableButton: true,
                buttonSelector: "#store-task",
                type: "GET",
                data: $('#login-form').serialize()
            })
        });

    </script>

</body>

</html>
