@inject('Country', 'App\Helpers\Country')
@inject('Permissions', 'App\Helpers\Permissions')

@php
/*
View variables:
===============
    - $title: string
    - $type: string [view | edit | add]
    - $action: string
    - $Client: ?Client
*/

if (false === array_search($type ?? '', ['view', 'edit', 'add'])) {
    $type = 'add';
}

$action = $action ?? '';
$title = $title ?? '';
$canEdit = ('view' !== $type && $Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT));
$canSeeJobs = (
    $Permissions::checkPermission($Permissions::ACL_JOB_VIEW) ||
    $Permissions::checkPermission($Permissions::ACL_JOB_EDIT)
);
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Clientes',
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
    @if (('add' !== $type) && (!$Client?->id > 0))
        <x-notification
            type="warning"
            title="Aviso!"
            content="Cliente não encontrado! <a href={{ route('client.index') }}>Clique aqui</a> para voltar a lista de clientes."
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
                                            Informações Gerais
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
                                                        name="client-id"
                                                        value="{{ $Client?->id }}"
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
                                                        name="client-name"
                                                        value="{{ $Client?->name }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">E-mail</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="email"
                                                        class="form-control form-control-sm"
                                                        placeholder="Email"
                                                        name="client-email"
                                                        value="{{ $Client?->email }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Telefone</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="tel"
                                                        class="form-control form-control-sm"
                                                        placeholder="Telefone"
                                                        name="client-phone"
                                                        value="{{ $Client?->phone }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Notas</label>
                                                    <textarea
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        class="form-control form-control-sm"
                                                        rows="3"
                                                        placeholder="Notas"
                                                        name="client-notes"
                                                        style="height: 6rem;"
                                                    >{{ $Client?->notes }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-1">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="m-0">
                                        <a
                                            class="custom-accordion-title d-block pt-2 pb-2 collapsed"
                                            data-bs-toggle="collapse"
                                            href="#collapseTwo"
                                            aria-expanded="false"
                                            aria-controls="collapseTwo"
                                        >
                                            Informações Fiscais
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-5">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">CPF/CNPJ</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="CPF/CNPJ"
                                                        name="client-b-id"
                                                        value="{{ $Client?->business_id }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-7">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Razão Social</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Razão Social"
                                                        name="client-b-name"
                                                        value="{{ $Client?->business_name }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-5">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Telefone Comercial</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="tel"
                                                        class="form-control form-control-sm"
                                                        placeholder="Telefone Comercial"
                                                        name="client-b-phone"
                                                        value="{{ $Client?->business_phone }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-7">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">E-mail Comercial</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="email"
                                                        class="form-control form-control-sm"
                                                        placeholder="E-mail Comercial"
                                                        name="client-b-email"
                                                        value="{{ $Client?->business_email }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-1">
                                <div class="card-header" id="headingThree">
                                    <h5 class="m-0">
                                        <a
                                            class="custom-accordion-title collapsed d-block pt-2 pb-2"
                                            data-bs-toggle="collapse"
                                            href="#collapseThree"
                                            aria-expanded="false"
                                            aria-controls="collapseThree"
                                        >
                                            Endereço
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Endereço</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Endereço"
                                                        name="client-street"
                                                        value="{{ $Client?->street }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Bairro / Complemento</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Bairro / Complemento"
                                                        name="client-street-2"
                                                        value="{{ $Client?->street_2 }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Cidade</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Cidade"
                                                        name="client-city"
                                                        value="{{ $Client?->city }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <select
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        class="form-control form-control-sm"
                                                        id="client-province"
                                                        name="client-province"
                                                    >
                                                        @foreach (array_merge(
                                                            ['' => 'Selecione ...'],
                                                            $Country::getProvinceByCountry($Client?->country ?? $Country::C_BRASIL)
                                                        ) as $short => $fullName)
                                                            <option
                                                                value="{{ $short }}"
                                                                {{ $short !== $Client?->province ? '': 'selected' }}
                                                            >{{ $fullName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">País</label>
                                                    <select
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        class="form-control form-control-sm ajax-country"
                                                        id="client-country"
                                                        name="client-country"
                                                        data-target="#client-province"
                                                    >
                                                        @foreach (array_merge(
                                                            ['' => 'Selecione ...'],
                                                            array_combine($Country::getCountries(), $Country::getCountries())
                                                        ) as $key => $country)
                                                            <option
                                                                value="{{ $key }}"
                                                                {{ $country !== $Client?->country ? '': 'selected' }}
                                                            >{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Código Postal</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Código Postal"
                                                        name="client-postal-code"
                                                        value="{{ $Client?->postal_code }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($canSeeJobs && $Client?->jobs?->count() > 0)
                                <div class="card mb-1">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="m-0">
                                            <a
                                                class="custom-accordion-title collapsed d-block pt-2 pb-2"
                                                data-bs-toggle="collapse"
                                                href="#collapseFour"
                                                aria-expanded="false"
                                                aria-controls="collapseFour"
                                            >
                                                Jobs
                                                <span class="float-end">
                                                    <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </span>
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                        <div class="card-body">
                                            <livewire:table
                                                :config="App\Tables\JobsTable::class"
                                                :configParams="[
                                                    'vClientId' => $Client?->id
                                                ]"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="text-end">
                            @if ($canEdit)
                                <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                            @endif

                            <a href="{{ route('client.index') }}" class="btn btn-light">Voltar para lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
