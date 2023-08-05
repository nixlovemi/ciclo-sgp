@inject('Permissions', 'App\Helpers\Permissions')
@inject('mUser', 'App\Models\User')

@php
/*
View variables:
===============
    - $title: string
    - $type: string [view | edit | add]
    - $action: string
    - $User: ?User
*/

if (false === array_search($type ?? '', ['view', 'edit', 'add'])) {
    $type = 'add';
}

$action = $action ?? '';
$title = $title ?? '';
$canEdit = ('view' !== $type && $Permissions::checkPermission($Permissions::ACL_USER_EDIT));
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Usuários',
    'BODY_TITLE' => $title
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
    @if (('add' !== $type) && (!$User?->id > 0))
        <x-notification
            type="warning"
            title="Aviso!"
            content="Usuário não encontrado! <a href={{ route('user.index') }}>Clique aqui</a> para voltar a lista de usuários."
        />
    @else
        <div class="row">
            <div class="col-lg-12">
                <x-notification />
            </div>

            <div class="col-12">
                <form method="POST" action="{{ $action }}">
                    @csrf

                    <div class="form-body">
                        <div id="accordion" class="custom-accordion mb-4">
                            <div class="card mb-1">
                                <div class="card-header" id="headingOne">
                                    <h5 class="m-0">
                                        <a
                                            class="custom-accordion-title d-block pt-2 pb-2 collapsed"
                                            data-bs-toggle="collapse"
                                            href="#collapseOne"
                                            aria-expanded="false"
                                            aria-controls="collapseOne"
                                        >
                                            Informações do Usuário
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
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
                                                        {{ ($canEdit) ?: 'disabled' }}
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
                                            <div class="col-12 col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">E-mail</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="email"
                                                        class="form-control form-control-sm"
                                                        placeholder="Email"
                                                        name="user-email"
                                                        value="{{ $User?->email }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Senha</label>
                                                    <input
                                                        {{ ($type === 'add') ?: 'disabled' }}
                                                        type="password"
                                                        class="form-control form-control-sm"
                                                        placeholder="Senha"
                                                        name="user-password"
                                                        value=""
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Cargo</label>
                                                    <select
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        class="form-control form-control-sm"
                                                        name="user-role"
                                                    >
                                                        @php
                                                        $roles = $mUser::USER_ROLES;
                                                        asort($roles);
                                                        @endphp

                                                        @foreach ($roles as $key => $role)
                                                            <option
                                                                value="{{ $key }}"
                                                                {{ $key !== $User?->role ? '': 'selected' }}
                                                            >{{ $role }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="text-end">
                            @if ($canEdit)
                                <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                            @endif
                            
                            <a href="{{ route('user.index') }}" class="btn btn-light">Voltar para lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection