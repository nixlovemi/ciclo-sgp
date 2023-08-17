@inject('JobFile', 'App\Models\JobFile')
@inject('MainMenu', 'App\View\Components\MainMenu')

@php
/*
View variables:
===============
    - $Job: ?Job
*/
@endphp

@extends('layout.modal', [
    'divId' => date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        Adicionar Arquivo
    </h5>

    @php
    /*
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
    */
    @endphp
@endsection

@section('MODAL_BODY')
    <form id="jobFile-add" method="POST" action="{{ route('jobFile.doAdd') }}" enctype="multipart/form-data">
        <input type="hidden" name="jcid" value="{{ $Job?->codedId }}" />
        @csrf

        <div class="form-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="form-label">
                            <small class="form-required">*</small>
                            Título
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="Título"
                            name="jf-title"
                            maxlength="60"
                            value=""
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-3">
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Tipo
                        </label>
                        <select
                            class="form-select form-control-sm mb-3"
                            name="jf-tipo"
                        >
                            <option value="{{ $JobFile::TYPE_URL }}">{{ $JobFile::JOB_FILE_TYPES[$JobFile::TYPE_URL] }}</option>
                            <option value="{{ $JobFile::TYPE_FILE }}">{{ $JobFile::JOB_FILE_TYPES[$JobFile::TYPE_FILE] }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-sm-9">
                    <div class="form-group mb-3">
                        <span data-jf-tipo="{{ $JobFile::TYPE_URL }}">
                            <label class="form-label">
                                <small class="form-required">*</small>
                                URL
                            </label>
                            <input
                                type="text"
                                class="form-control form-control-sm mb-3"
                                placeholder="URL"
                                name="jf-url"
                                value=""
                            />
                        </span>
    
                        <span style="display:none;" data-jf-tipo="{{ $JobFile::TYPE_FILE }}">
                            <label class="form-label">
                                <small class="form-required">*</small>
                                Arquivo
                            </label>
                            <input
                                style="width:calc(100% - 10px); display:inline-block; margin-left:8px;"
                                class="form-control form-control-sm"
                                type="file"
                                name="jf-file"
                            />
                            <small style="position:relative; left:8px;" id="textHelp" class="form-text text-muted">Máximo 10mb</small>
                        </span>
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