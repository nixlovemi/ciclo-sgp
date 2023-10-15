@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <!-- Favicon icon -->
        <!-- <link rel="icon" type="image/png" sizes="16x16" href="/free-dash/assets/images/favicon.png" /> -->
        <link rel="icon" href="/img/favicon/cropped-Favcon-32x32.jpeg" sizes="32x32" />
        <link rel="icon" href="/img/favicon/cropped-Favcon-192x192.jpeg" sizes="192x192" />
        <link rel="apple-touch-icon" href="/img/favicon/cropped-Favcon-180x180.jpeg" />
        <meta name="msapplication-TileImage" content="/img/favicon/cropped-Favcon-270x270.jpeg" />
        <title>{{ $PAGE_TITLE ?? '' }} | {{ env('SITE_DISPLAY_NAME') }}</title>
        <!-- Custom CSS -->
        @livewireStyles
        @yield('HEADER_CUSTOM_CSS')
        <!-- Custom CSS -->
        <link href="/free-dash/assets/extra-libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
        <link href="/free-dash/assets/extra-libs/sweetalert2/bootstrap-4.min.css" rel="stylesheet" />
        <link href="/free-dash/assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="/free-dash/css/style.min.css" rel="stylesheet" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- ========== -->
        <link href="/custom.css" rel="stylesheet" />
    </head>

    <body>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->

        <!--<div class="main-wrapper">-->
        <div
            id="main-wrapper" data-theme="light" data-layout="vertical"
            data-navbarbg="skin6" data-sidebartype="full"
            data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full"
        >
            @yield('BODY_CONTENT')
        </div>

        <!-- ============================================================== -->
        <!-- All Required js -->
        <!-- ============================================================== -->
        <script src="/free-dash/assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="/free-dash/assets/extra-libs/jquery-maskmoney/dist/jquery.maskMoney.min.js"></script>
        <script src="/free-dash/assets/extra-libs/jquery-loading-overlay-master/dist/loadingoverlay.min.js"></script>
        <script src="/free-dash/assets/extra-libs/sweetalert2/sweetalert2.all.min.js"></script>
        <script src="/free-dash/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        @livewireScripts
        @yield('FOOTER_CUSTOM_JS')
        <script src="/custom.js"></script>
    </body>
</html>