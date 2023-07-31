@extends('layout.login', [
    'PAGE_TITLE' => 'Recuperar Senha',
    'TITLE' => 'Recuperar Senha',
    'SUB_TITLE' => 'Informe seu e-mail para iniciar o processo de recuperação de senha',
])

@section('LOGIN_CUSTOM_CSS')
@endsection

@section('BODY')
    <form class="mt-4" method="POST" action="{{ route('site.doRecoverPwd') }}">
        @csrf
        
        <div class="row">
            <div class="col-lg-12">
                <x-notification />
            </div>

            <div class="col-lg-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="email">E-mail</label>
                    <input class="form-control" name="email" type="text" placeholder="Preencha o e-mail ..." />
                </div>
            </div>
            <div class="col-lg-12 text-center">
                <button type="submit" class="btn w-100 btn-ciclo-yellow">Enviar</button>
            </div>
            <div class="col-lg-12 text-center mt-5">
                <a href="{{ route('site.login') }}" class="text-info">Voltar para o login</a>
            </div>
        </div>
    </form>
@endsection