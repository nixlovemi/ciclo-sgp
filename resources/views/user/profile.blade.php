@inject('SysUtils', 'App\Helpers\SysUtils')

@php
$User = $SysUtils::getLoggedInUser();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Meu Perfil',
    'BODY_TITLE' => 'Meu Perfil'
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
<div class="row">
    <div class="col-lg-12">
        <x-notification />
    </div>

    <div class="col-12">
        <form method="POST" action="{{ route('user.saveProfile') }}" enctype="multipart/form-data">
            @csrf

            <div class="card mb-1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">ID</label>
                                <input
                                    disabled
                                    type="text"
                                    class="form-control form-control-sm"
                                    placeholder="ID"
                                    name="user-id"
                                    value="{{ $User?->id }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-10">
                            <div class="form-group mb-3">
                                <label class="form-label">Nome</label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Nome"
                                    name="user-name"
                                    value="{{ $User?->name }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">E-mail</label>
                                <input
                                    type="email"
                                    class="form-control form-control-sm"
                                    placeholder="Email"
                                    name="user-email"
                                    value="{{ $User?->email }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Cargo</label>
                                <input
                                    disabled
                                    type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Cargo"
                                    name="user-role"
                                    value="{{ $User?->roleDescription }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <img src="{{ $User->getPictureUrl() }}" alt="user" class="rounded-circle" width="50" />
                            <input
                                style="width:calc(100% - 65px); display:inline-block; margin-left:8px;"
                                class="form-control form-control-sm"
                                type="file"
                                accept="image/x-png,image/jpeg,image/jpg"
                                name="user-picture"
                            />
                            <small style="display:block;" id="helper-user-picture" class="form-text text-muted">Selecione uma imagem na proporção 250x250</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions mt-3">
                <div class="text-end">
                    <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection