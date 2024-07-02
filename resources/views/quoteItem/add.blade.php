@inject('mQuoteItem', 'App\Models\QuoteItem')
@inject('MainMenu', 'App\View\Components\MainMenu')
@inject('ServiceItem', 'App\Models\ServiceItem')

@php
/*
View variables:
===============
    - $QuoteItem: ?QuoteItem
    - $Quote: ?Quote
*/

$Quote = $Quote ?? null;
$QuoteItem = $QuoteItem ?? null;
@endphp

@extends('layout.modal', [
    'divId' => date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ isset($QuoteItem) ? 'Editar': 'Adicionar'}} Item
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="quoteItem-add" method="POST" action="{{ route('quoteItem.doAdd') }}">
        <input type="hidden" name="qcid" value="{{ $Quote?->codedId }}" />
        <input type="hidden" name="qicid" value="{{ $QuoteItem?->codedId }}" />
        @csrf

        <div class="form-body mb-3">
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="form-label">
                            <small class="form-required">*</small>
                            Item
                        </label>
                        <select
                            class="bootstrap-select form-select form-control-sm mb-3"
                            name="qi-item"
                            data-live-search="true"
                        >
                            <option value="">Escolha ...</option>

                            @foreach (
                                $ServiceItem::where('active', 1)
                                    ->orderBy('description')
                                    ->get() as $serviceItem
                            )
                                <option
                                    value="{{ $serviceItem->codedId }}"
                                    data-price="{{ $serviceItem->formattedPrice }}"
                                    {{ $serviceItem->id !== $QuoteItem?->serviceItem?->id ? '': 'selected' }}
                                >{{ $serviceItem->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-3">
                    <label class="form-label">
                        <small class="form-required">*</small>
                        Quantidade
                    </label>
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control form-control-sm jq-mask-money"
                            placeholder="Quantidade"
                            name="qi-qty"
                            data-thousands="{{ $ServiceItem::PRICE_THOUSAND_SEP }}"
                            data-decimal="{{ $ServiceItem::PRICE_DECIMAL_SEP }}"
                            value="{{ $QuoteItem?->formattedQuantity ?: '1,00' }}"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">
                        <small class="form-required">*</small>
                        Unidade
                    </label>
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="Unidade"
                            name="qi-type"
                            value="{{ $QuoteItem?->type ?: 'UN' }}"
                            maxlength="10"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">
                        <small class="form-required">*</small>
                        Preço
                    </label>
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control form-control-sm jq-mask-money"
                            placeholder="Preço"
                            name="qi-price"
                            data-thousands="{{ $ServiceItem::PRICE_THOUSAND_SEP }}"
                            data-decimal="{{ $ServiceItem::PRICE_DECIMAL_SEP }}"
                            value="{{ $QuoteItem?->formattedPrice ?: '' }}"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">
                        Desconto
                    </label>
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control form-control-sm jq-mask-money"
                            placeholder="Desconto"
                            name="qi-discount"
                            data-thousands="{{ $ServiceItem::PRICE_THOUSAND_SEP }}"
                            data-decimal="{{ $ServiceItem::PRICE_DECIMAL_SEP }}"
                            value="{{ $QuoteItem?->formattedDiscount ?: '' }}"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="text-end">
                <input type="submit" class="btn-modal-save btn btn-ciclo-yellow" value="Salvar" />
                <a href="{{ $MainMenu::JS_URL }}" class="btn-modal-close btn btn-light">Fechar</a>
            </div>
        </div>
    </form>
@endsection