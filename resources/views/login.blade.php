@extends('layout.login', [
    'PAGE_TITLE' => 'Login',
    'TITLE' => 'Login',
    'SUB_TITLE' => 'Entre no Sistema SGP utilizando seu e-mail e senha.',
])

@section('LOGIN_CUSTOM_CSS')
@endsection

@section('BODY')
    <form class="mt-4" method="POST" action="{{ route('site.doLogin') }}">
        @csrf
        
        <div class="row">
            <div class="col-lg-12 text-center">
                <x-notification />
            </div>

            <div class="col-lg-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="email">E-mail</label>
                    <input class="form-control" name="email" type="text" placeholder="Preencha o e-mail ..." />
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="pwd">Senha</label>
                    <input class="form-control" name="pwd" type="password" placeholder="Preencha a senha ..." />
                </div>
            </div>
            <div class="col-lg-12 text-center">
                <button type="submit" class="btn w-100 btn-ciclo-yellow">Entrar</button>
            </div>
            <div class="col-lg-12 text-center mt-5">
                <a href="javascript:;" class="text-info">Esqueceu a senha?</a>
            </div>
        </div>
    </form>
@endsection