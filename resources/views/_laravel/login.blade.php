@extends('layout.core', [
    'PAGE_TITLE' => 'Login'
])

@section('HEADER_CUSTOM_CSS')
@endsection

@section('BODY_CONTENT')
    <!-- /free-dash/assets/images/big/auth-bg.jpg -->
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative"
        style="background:url(https://wallpaperaccess.com/full/456236.jpg) no-repeat center center;">
        <div class="auth-box row">
            <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url(/free-dash/assets/images/big/3.jpg);">
            </div>
            <div class="col-lg-5 col-md-7 bg-white">
                <div class="p-3">
                    <div class="text-center">
                        <img src="/free-dash/assets/images/big/icon.png" alt="wrapkit">
                    </div>
                    <h2 class="mt-3 text-center">Sign In</h2>
                    <p class="text-center">Enter your email address and password to access admin panel.</p>
                    <form class="mt-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <label class="form-label text-dark" for="uname">Username</label>
                                    <input class="form-control" id="uname" type="text"
                                        placeholder="enter your username">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <label class="form-label text-dark" for="pwd">Password</label>
                                    <input class="form-control" id="pwd" type="password"
                                        placeholder="enter your password">
                                </div>
                            </div>
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn w-100 btn-dark">Sign In</button>
                            </div>
                            <div class="col-lg-12 text-center mt-5">
                                Don't have an account? <a href="#" class="text-danger">Sign Up</a>
                            </div>
                        </div>
                    </form>
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