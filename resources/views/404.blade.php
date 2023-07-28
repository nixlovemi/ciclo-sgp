@extends('layout.core', [
    'PAGE_TITLE' => 'Oops, não encontrado',
])

@section('HEADER_CUSTOM_CSS')
    <style>
        #dv-404-main-box {
            background-color: #68C4BF;
        }
        #dv-404-main-box .modal-bg-img {
            background-size: contain;
            background-repeat: no-repeat;
        }
    </style>
@endsection

@section('BODY_CONTENT')
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative"
        style="background:url(/img/demo/login-bg.jpg) no-repeat center center;"
    >
        <div id="dv-404-main-box" class="auth-box row" style="width:100%">
            <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url(/img/404-image.jpg);"></div>
            <div class="col-lg-5 col-md-7 bg-white">
                <div class="p-3">
                    <h2 class="mt-3 text-center">Página não encontrada!</h2>
                    <p class="text-center">
                        A página que você tentou acessar foi movida, removida, renomeada ou talvez nunca existiu
                        <br />
                        (ㆆ _ ㆆ)
                    </p>
                    <div class="col-lg-12 text-center">
                        <button
                            onclick="javascript:document.location.href='{{ route('site.login') }}'"
                            type="button"
                            class="btn w-100 btn-ciclo-yellow"
                        >
                            Voltar ao início
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('FOOTER_CUSTOM_JS')
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $(".preloader ").fadeOut();
    </script>
@endsection