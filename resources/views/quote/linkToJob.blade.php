@inject('MainMenu', 'App\View\Components\MainMenu')
@inject('Quote', 'App\Models\Quote')

@php
/*
View variables:
===============
    - $Job: ?Job
*/
@endphp

@extends('layout.modal', [
    'divId' => 'linkToJob' . date('YmdHis') . rand(),
    'maxHeight' => '150vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        Vincular Orçamento
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="linkToJob-add" method="POST" action="{{ route('quote.saveLinkToJobHtml') }}">
        <input type="hidden" name="jcid" value="{{ $Job?->codedId }}" />
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="form-group mb-3">
                    <label class="form-label text-dark" for="current_pwd">Orçamentos</label>
                    <select
                        class="bootstrap-select form-select form-control-sm mb-3"
                        name="quote-to-link"
                        data-live-search="true"
                    >
                        <option value="">Escolha ...</option>
                        @foreach ($Quote::fGetAllToLinkToJob() as $Quote)
                            <option value="{{ $Quote->codedId }}">
                                ID: {{ $Quote->id }} |
                                Data: {{ $Quote->formattedDate }} |
                                Cliente: {{ $Quote->client->name }} |
                                Total: {{ $Quote->formattedTotal }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions mt-5">
            <div class="text-end">
                <input type="submit" class="btn-modal-save btn btn-ciclo-yellow" value="Salvar" />
                <a href="{{ $MainMenu::JS_URL }}" class="btn-modal-close btn btn-light">Fechar</a>
            </div>
        </div>
    </form>
@endsection