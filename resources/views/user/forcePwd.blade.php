@php
/*
View variables:
===============
    - $User: User
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Resetar Senha',
    'BODY_TITLE' => 'Resetar Senha'
])

@section('DASHBOARD_CUSTOM_CSS')
@endsection

@section('DASHBOARD_CUSTOM_JS')
@endsection

@section('DASHBOARD_SEARCH_BOX')
@endsection

@section('DASHBOARD_MENU')
    <x-main-menu />
@endsection

@section('DASHBOARD_CONTENT')
    @include('partials.passwordRules')

    <form class="mt-2" method="POST" action="{{ route('user.doResetPwd') }}">
        @csrf
        <input type="hidden" name="uid" value="{{ $User?->codedId }}" />
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <x-notification />
                    </div>
        
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label class="form-label text-dark" for="u-name">Nome</label>
                            <input class="form-control" name="u-name" type="text" disabled value="{{ $User?->name }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label class="form-label text-dark" for="u-email">E-mail</label>
                            <input class="form-control" name="u-email" type="text" disabled value="{{ $User?->email }}" />
                        </div>
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
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="text-end">
                <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                <a href="{{ route('user.index') }}" class="btn btn-light">Voltar para lista</a>
            </div>
        </div>
    </form>
@endsection