@inject('ValidatePassword', 'App\Helpers\ValidatePassword')

@php
/*
View variables:
===============
    - $ID_KEY: string
*/
@endphp

@extends('layout.login', [
    'PAGE_TITLE' => 'Nova Senha',
    'TITLE' => 'Criar nova senha',
    'SUB_TITLE' => 'Use os campos abaixo para gerar sua nova senha ',
])

@section('LOGIN_CUSTOM_CSS')
@endsection

@section('BODY')
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-light bg-light text-dark border-0 m-0" role="alert">
                <strong>A senha deve conter:</strong>
                <br />
                <ul class="p-0 m-0">
                    @foreach ($ValidatePassword::getRulesTexts() as $rulesText)
                        <li>- {{ $rulesText }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <form class="mt-4" method="POST" action="{{ route('site.doChangeNewPwd') }}">
        @csrf
        <input type="hidden" name="ik" value="{{ $ID_KEY ?? '' }}" />
        
        <div class="row">
            <div class="col-lg-12">
                <x-notification />
            </div>

            <div class="col-lg-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="new_pwd">Nova Senha</label>
                    <input class="form-control" name="new_pwd" type="password" placeholder="Nova senha ..." />
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="new_pwd_retype">Repetir Nova Senha</label>
                    <input class="form-control" name="new_pwd_retype" type="password" placeholder="Repetir nova senha ..." />
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