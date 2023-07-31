@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $TITLE: string
    - $SUB_TITLE: string
*/
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('HEADER_CUSTOM_CSS')
    @yield('LOGIN_CUSTOM_CSS')
@endsection

@section('BODY_CONTENT')
    <!-- /free-dash/assets/images/big/auth-bg.jpg -->
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative"
        style="background:url(/img/login-bg.jpg) no-repeat center center; background-size:cover;"
    >
        <div class="auth-box row">
            <!-- /free-dash/assets/images/big/3.jpg -->
            <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url(/img/login-box.jpg);"></div>
            <div class="col-lg-5 col-md-7 bg-white">
                <div class="p-3">
                    <div class="text-center">
                        <i class="fas fa-tv" style="font-size: 2em !important;"></i>
                    </div>
                    <h2 class="mt-3 text-center">{!! $TITLE ?? 'Login' !!}</h2>
                    <p class="text-center">{!! $SUB_TITLE ?? '' !!}</p>
                    @yield('BODY')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('FOOTER_CUSTOM_JS')
    <!-- Bootstrap tether Core JavaScript -->
    <script src="/free-dash/assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $(".preloader ").fadeOut();
    </script>
@endsection