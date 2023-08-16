@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Alterar Senha',
    'BODY_TITLE' => 'Alterar Senha'
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

    <form class="mt-2" method="POST" action="{{ route('user.doChangePwd') }}">
        @csrf
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <x-notification />
                    </div>
        
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label class="form-label text-dark" for="current_pwd">Senha Atual</label>
                            <input class="form-control" name="current_pwd" type="password" placeholder="Senha Atual ..." />
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
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn w-100 btn-ciclo-yellow">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection