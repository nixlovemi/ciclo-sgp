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
$typeStr = [
    'view' => 'Visualizar',
    'edit' => 'Editar',
    'add' => 'Novo',
];
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Orçamento',
    'BODY_TITLE' => $Quote?->id > 0 ? 'Editar': 'Novo' . ' Orçamento',
    'BODY_TITLE' => sprintf(
        '%s Orçamento%s',
        $typeStr[$type] ?? '',
        in_array($type, ['edit', 'add']) ? ' | ' . $Quote?->id: ''
    ),
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
            <div class="card mb-1">
                <div class="card-body">
                    <form method="POST" action="{{ route('quote.doAdd') }}">
                        <input type="hidden" name="qcid" value="{{ $Quote?->codedId }}" />
                        @csrf
        
                        <div class="form-body">
                            @include('quote.partials.formRows', [
                                'Quote' => $Quote,
                                'disabled' => $disabled,
                                'type' => $type
                            ])
                        </div>
                        
                        <div class="form-actions">
                            <div class="text-end">
                                @if (in_array($type, ['add', 'edit']))
                                    <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                                @endif

                                @if ('add' !== $type)
                                    <a target="_blank" href="{{ route('quote.pdf', ['codedId' => $Quote->codedId]) }}" class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i>
                                        Imprimir PDF
                                    </a>
                                @endif
                                
                                <a href="{{ route('quote.index') }}" class="btn btn-light">Voltar para lista</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection