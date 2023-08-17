@inject('Carbon', 'Carbon\Carbon')
@inject('mClient', 'App\Models\Client')
@inject('mQuote', 'App\Models\Quote')

@php
/*
View variables:
===============
    - $Quote: ?Quote
    - $disabled: bool
    - $type: string [view | edit | add]
*/

$disabled = $disabled ?? true;
$type = $type ?? 'view';
@endphp

<div class="row">
    <div class="col-12">
        <label class="form-label">
            <small class="form-required">*</small>
            Cliente
        </label>
        @if ($disabled || 'add' !== $type)
            <input type="hidden" name="quote-client" value="{{ $Quote?->client?->codedId }}" />
            <input
                disabled="disabled"
                type="text"
                class="form-control form-control-sm mb-3"
                placeholder="Cliente"
                name="quote-client-dis"
                value="{{ $Quote?->client?->name }}"
            />
        @else
            <select
                class="bootstrap-select form-select form-control-sm mb-3"
                name="quote-client"
                data-live-search="true"
            >
                <option value="">Escolha ...</option>

                @foreach (
                    $mClient::where('active', 1)
                        ->orderBy('name')
                        ->get() as $client
                )
                    <option
                        value="{{ $client->codedId }}"
                        {{ $client->id !== $Quote?->client?->id ? '': 'selected' }}
                    >{{ $client->name }}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-4">
        <label class="form-label">
            <small class="form-required">*</small>
            Data
        </label>
        <input
            {{ (!$disabled) ?: 'disabled' }}
            class="form-control form-control-sm jq-datepicker"
            placeholder="Data"
            name="quote-date"
            value="{{ (null === $Quote?->date) ? '': $Carbon::createFromFormat('Y-m-d', $Quote?->date)->format('d/m/Y') }}"
        />
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">
            <small class="form-required">*</small>
            Validade (em dias)
        </label>
        <input
            {{ (!$disabled) ?: 'disabled' }}
            type="text"
            class="form-control form-control-sm"
            placeholder="Validade (em dias)"
            name="quote-validity"
            maxlength="3"
            value="{{ $Quote?->validity_days }}"
        />
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">
            <small class="form-required">*</small>
            Forma Pagamento
        </label>

        @if ($disabled)
            <input
                disabled
                type="text"
                class="form-control form-control-sm mb-3"
                placeholder="Forma Pagamento"
                name="quote-payment-type"
                value="{{ $Quote?->payment_type }}"
            />
        @else
            <select
                class="form-select form-control-sm mb-3"
                name="quote-payment-type"
            >
                <option value="">Escolha ...</option>

                @foreach ($mQuote::PAYMENT_TYPES as $paymentType)
                    <option
                        value="{{ $paymentType }}"
                        {{ $paymentType !== $Quote?->payment_type ? '': 'selected' }}
                    >{{ $paymentType }}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mb-3">
            <label class="form-label">
                Forma de Pagamento - Notas
            </label>
            <textarea
                {{ !$disabled ?: 'disabled' }}
                class="form-control form-control-sm"
                placeholder="Forma de Pagamento - Notas"
                name="quote-pt-memo"
            >{{ $Quote?->payment_type_memo }}</textarea>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group mb-3">
            <label class="form-label">
                Observações
            </label>
            <textarea
                {{ !$disabled ?: 'disabled' }}
                class="form-control form-control-sm"
                placeholder="Observações"
                name="quote-notes"
            >{{ $Quote?->notes }}</textarea>
        </div>
    </div>
</div>

@if ($Quote?->id)
    <div class="row">
        <div class="col-12 border-bottom mb-2 text-dark">
            <h4>Items:</h4>
        </div>
        <div class="col-12">
            <livewire:table
                :config="App\Tables\QuoteItemsTable::class"
                :configParams="[
                    'vQuoteId' => $Quote?->id,
                    'vDisabled' => $disabled,
                ]"
            />
        </div>
    </div>
@endif