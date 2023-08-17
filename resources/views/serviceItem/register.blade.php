@inject('Permissions', 'App\Helpers\Permissions')
@inject('mServiceItem', 'App\Models\ServiceItem')

@php
/*
View variables:
===============
    - $title: string
    - $type: string [view | edit | add]
    - $action: string
    - $ServiceItem: ?ServiceItem
*/

if (false === array_search($type ?? '', ['view', 'edit', 'add'])) {
    $type = 'add';
}

$action = $action ?? '';
$title = $title ?? '';
$canEdit = ('view' !== $type && $Permissions::checkPermission($Permissions::ACL_SERVICE_ITEM_EDIT));
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Cadastro de Items',
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
    @if (('add' !== $type) && (!$ServiceItem?->id > 0))
        <x-notification
            type="warning"
            title="Aviso!"
            content="Item não encontrado! <a href={{ route('serviceItems.index') }}>Clique aqui</a> para voltar a lista de items."
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
                                            Informações do Item
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-1">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">ID</label>
                                                    <input
                                                        disabled
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="ID"
                                                        name="si-id"
                                                        value="{{ $ServiceItem?->id }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-8">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Descrição</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Descrição"
                                                        name="si-description"
                                                        value="{{ $ServiceItem?->description }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-1">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Moeda</label>
                                                    <select
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        class="form-control form-control-sm"
                                                        name="si-currency"
                                                    >
                                                        @foreach (array_merge(
                                                            ['' => 'Selecione ...'],
                                                            $mServiceItem::CURRENCY_TYPES
                                                        ) as $currency)
                                                            <option
                                                                value="{{ $currency }}"
                                                                {{ $currency !== $ServiceItem?->currency ? '': 'selected' }}
                                                            >{{ $currency }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Preço</label>
                                                    <input
                                                        {{ ($canEdit) ?: 'disabled' }}
                                                        type="text"
                                                        class="form-control form-control-sm jq-mask-money"
                                                        placeholder="Preço"
                                                        name="si-price"
                                                        data-thousands="{{ $mServiceItem::PRICE_THOUSAND_SEP }}"
                                                        data-decimal="{{ $mServiceItem::PRICE_DECIMAL_SEP }}"
                                                        value="{{ $ServiceItem?->formattedPrice }}"
                                                    />
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
                            
                            <a href="{{ route('serviceItems.index') }}" class="btn btn-light">Voltar para lista</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection