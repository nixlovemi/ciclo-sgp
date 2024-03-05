@inject('Permissions', 'App\Helpers\Permissions')
@inject('SysUtils', 'App\Helpers\SysUtils')
@inject('cJob', 'App\Http\Controllers\Job')
@inject('Client', 'App\Models\Client')
@inject('mJob', 'App\Models\Job')
@inject('mJobFile', 'App\Models\JobFile')
@inject('mUser', 'App\Models\User')
@inject('mJobInvoice', 'App\Models\JobInvoice')
@inject('MainMenu', 'App\View\Components\MainMenu')
@inject('Carbon', 'Carbon\Carbon')

@php
/*
View variables:
===============
    - $title: string
    - $type: string [view | edit | add]
    - $action: string
    - $Job: ?Job
*/

$title = $title ?? '';

if (false === array_search($type ?? '', ['view', 'edit', 'add'])) {
    $type = 'add';
}

$disabled = !in_array($type, ['add', 'edit']) || in_array($Job?->status, [$mJob::STATUS_DONE, $mJob::STATUS_CANCEL]);
$loggedInUser = $SysUtils::getLoggedInUser();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Job',
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
    <div class="row">
        <div class="col-lg-12">
            <x-notification />
        </div>

        <div class="col-12">
            <form id="job-register" method="POST" action="{{ $action ?? '' }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jcid" value="{{ $Job?->codedId }}" />

                <div class="form-body">
                    <div id="{{ $cJob::JOB_ACCORDION_ID }}" class="custom-accordion mb-4">
                        <div id="job" class="card mb-1">
                            <div class="card-header" id="headingOne">
                                <h5 class="m-0">
                                    <a
                                        class="custom-accordion-title d-block pt-2 pb-2 collapsed"
                                        data-bs-toggle="collapse"
                                        href="#collapseOne"
                                        aria-expanded="false"
                                        aria-controls="collapseOne"
                                    >
                                        Job
                                        <span class="float-end">
                                            <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                        </span>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#{{ $cJob::JOB_ACCORDION_ID }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-3">
                                            <label class="form-label">
                                                <small class="form-required">*</small>
                                                Status
                                            </label>

                                            @if ($disabled)
                                                <input
                                                    disabled
                                                    type="text"
                                                    class="form-control form-control-sm mb-3 bg-ciclo"
                                                    placeholder="Status"
                                                    name="job-status"
                                                    value="{{ $mJob::JOB_STATUSES[$Job?->status] ?? '' }}"
                                                />
                                            @else
                                                <select
                                                    class="form-select form-control-sm mb-3 bg-ciclo"
                                                    name="job-status"
                                                >
                                                    <option value="">Escolha ...</option>

                                                    @foreach ($mJob::JOB_STATUSES_ADD as $key => $status)
                                                        <option
                                                            value="{{ $key }}"
                                                            {{ $key !== $Job?->status ? '': 'selected' }}
                                                        >{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-7">
                                            <label class="form-label">
                                                <small class="form-required">*</small>
                                                Título
                                            </label>
                                            <input
                                                {{ (!$disabled) ?: 'disabled' }}
                                                type="text"
                                                maxlength="120"
                                                class="form-control form-control-sm"
                                                placeholder="Título"
                                                name="job-title"
                                                value="{{ $Job?->title }}"
                                            />
                                        </div>

                                        <div class="col-12 col-md-2">
                                            <label class="form-label">
                                                <small class="form-required">*</small>
                                                Prev. Entrega
                                            </label>
                                            <input
                                                {{ (!$disabled) ?: 'disabled' }}
                                                class="form-control form-control-sm jq-datepicker"
                                                placeholder="Prev. Entrega"
                                                name="job-due-date"
                                                value="{{ (null === $Job?->due_date) ? '': $Carbon::createFromFormat('Y-m-d', $Job?->due_date)->format('d/m/Y') }}"
                                            />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <label class="form-label">
                                                <small class="form-required">*</small>
                                                Cliente
                                            </label>

                                            @if ($disabled || 'edit' === $type)
                                                <input
                                                    disabled
                                                    type="text"
                                                    class="form-control form-control-sm mb-3"
                                                    placeholder="Cliente"
                                                    name="job-client"
                                                    value="{{ $Job?->client?->name }}"
                                                />
                                            @else
                                                <select
                                                    class="bootstrap-select form-select form-control-sm mb-3"
                                                    name="job-client"
                                                    data-live-search="true"
                                                >
                                                    <option value="">Escolha ...</option>

                                                    @foreach (
                                                        $Client::where('active', 1)
                                                            ->orderBy('name')
                                                            ->get() as $client
                                                    )
                                                        <option
                                                            value="{{ $client->codedId }}"
                                                            {{ $client->id !== $Job?->client?->id ? '': 'selected' }}
                                                        >{{ $client->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label class="form-label">
                                                Responsável
                                            </label>

                                            <input
                                                {{ (!$disabled) ?: 'disabled' }}
                                                type="text"
                                                maxlength="60"
                                                class="form-control form-control-sm"
                                                placeholder="Responsável"
                                                name="job-responsible"
                                                value="{{ $Job?->responsible }}"
                                            />
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label class="form-label">
                                                Responsável Ciclo
                                            </label>

                                            @if ($disabled)
                                                <input
                                                    disabled
                                                    type="text"
                                                    class="form-control form-control-sm mb-3"
                                                    placeholder="Status"
                                                    name="job-user-responsible"
                                                    value="{{ $Job?->userResponsible?->name ?? '' }}"
                                                />
                                            @else
                                                <select
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="bootstrap-select form-select form-control-sm mb-3"
                                                    name="job-user-responsible"
                                                    data-live-search="true"
                                                >
                                                    <option value="">Escolha ...</option>

                                                    @foreach (
                                                        $mUser::where('active', 1)
                                                            ->whereIn('role', [$mUser::ROLE_CREATIVE, $mUser::ROLE_EDITOR])
                                                            ->orderBy('name')
                                                            ->get() as $vUser
                                                    )
                                                        <option
                                                            value="{{ $vUser->codedId }}"
                                                            {{ $vUser->id !== $Job?->userResponsible?->id ? '': 'selected' }}
                                                        >{{ $vUser->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="briefing" class="card mb-1">
                            <div class="card-header" id="headingTwo">
                                <div class="row g-0">
                                    <div class="col-11">
                                        <h5 class="m-0">
                                            <a
                                                class="custom-accordion-title d-block pt-2 pb-2"
                                                data-bs-toggle="collapse"
                                                href="#collapseTwo"
                                                aria-expanded="false"
                                                aria-controls="collapseTwo"
                                            >
                                                Briefing
                                                <span class="float-end">
                                                    <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </span>
                                            </a>
                                        </h5>
                                    </div>
                                    <div class="col-1 align-items-end">
                                        @if ($Job?->codedId && $Job?->briefing)
                                            <div class="ms-auto">
                                                <div class="dropdown sub-dropdown">
                                                    <button
                                                        class="btn btn-link text-muted dropdown-toggle"
                                                        type="button"
                                                        id="dd0"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false"
                                                    >
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                            
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd0" style="">
                                                            <a
                                                                class="dropdown-item"
                                                                target="_blank"
                                                                href="{{ route('job.briefingPdf', ['codedId' => $Job?->codedId]) }}"
                                                            >Ver PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#{{ $cJob::JOB_ACCORDION_ID }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Breve Descrição do Job
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-objectvie"
                                                >{{ $Job?->briefing?->objective }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Uso do Material:
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-material"
                                                >{{ $Job?->briefing?->material }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Informações Técnicas
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-technical"
                                                >{{ $Job?->briefing?->technical }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Mensagem e Informações de Conteúdo
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-content-info"
                                                >{{ $Job?->briefing?->content_info }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Conceito Criativo / Identidade do Job
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-creative-det"
                                                >{{ $Job?->briefing?->creative_details }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    Entregáveis
                                                </label>
                                                <textarea
                                                    {{ !$disabled ?: 'disabled' }}
                                                    class="form-control form-control-sm"
                                                    name="job-b-deliverables"
                                                >{{ $Job?->briefing?->deliverables }}</textarea>
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
                                                    name="job-b-notes"
                                                >{{ $Job?->briefing?->notes }}</textarea>
                                            </div>
                                        </div>

                                        @if ($type !== 'add')
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">
                                                        Revisão Final
                                                    </label>
                                                    
                                                    <livewire:table
                                                        :config="App\Tables\JobsFileTableBriefingSection::class"
                                                        :configParams="[
                                                            'vJobId' => $Job?->id,
                                                            'vDisabled' => $disabled,
                                                            'vJobSections' => [$mJobFile::JOB_SECTION_BRIEFING_FINAL_REVIEW],
                                                        ]"
                                                    />
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($type !== 'add')

                            <div id="files" class="card mb-1">
                                <div class="card-header" id="headingThree">
                                    <h5 class="m-0">
                                        <a
                                            class="custom-accordion-title d-block pt-2 pb-2"
                                            data-bs-toggle="collapse"
                                            href="#collapseThree"
                                            aria-expanded="false"
                                            aria-controls="collapseThree"
                                        >
                                            Arquivos
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#{{ $cJob::JOB_ACCORDION_ID }}">
                                    <div class="card-body">
                                        <livewire:table
                                            :config="App\Tables\JobsFileTable::class"
                                            :configParams="[
                                                'vJobId' => $Job?->id,
                                                'vDisabled' => $disabled,
                                            ]"
                                        />
                                    </div>
                                </div>
                            </div>
                        
                        @endif

                        @if ($type !== 'add' && ($loggedInUser?->canSeeJobQuoteTab()))
                            <span id="job-partials-quoteCard" data-disabled="{{ (int) $disabled }}">
                                @include('job.partials.quoteCard', [
                                    'dataParent' => '#' . $cJob::JOB_ACCORDION_ID,
                                    'disabled' => $disabled,
                                    'Job' => $Job,
                                    'showForm' => false,
                                ])
                            </span>
                        @endif

                        @if ($type !== 'add' && true === $loggedInUser?->isAdmin())

                            <div id="invoice" class="card mb-1">
                                <div class="card-header" id="headingFive">
                                    <h5 class="m-0">
                                        <a
                                            class="custom-accordion-title d-block pt-2 pb-2"
                                            data-bs-toggle="collapse"
                                            href="#collapseFive"
                                            aria-expanded="false"
                                            aria-controls="collapseFive"
                                        >
                                            Financeiro
                                            <span class="float-end">
                                                <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </span>
                                        </a>
                                    </h5>
                                </div>

                                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#{{ $cJob::JOB_ACCORDION_ID }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-3 mb-1">
                                                <label class="form-label">
                                                    Número da NF
                                                </label>
                                                <input
                                                    {{ (!$disabled) ?: 'disabled' }}
                                                    type="text"
                                                    class="form-control form-control-sm"
                                                    placeholder="Número da NF"
                                                    name="jinvoice-number"
                                                    value="{{ $Job?->invoice?->invoice_number }}"
                                                />
                                            </div>

                                            <div class="col-12 col-md-3 mb-1">
                                                <label class="form-label">
                                                    Dt Faturamento
                                                </label>
                                                <input
                                                    {{ (!$disabled) ?: 'disabled' }}
                                                    type="text"
                                                    class="form-control form-control-sm jq-datepicker"
                                                    placeholder="Dt Faturamento"
                                                    name="jinvoice-date"
                                                    value="{{ $Job?->invoice?->formattedInvoiceDate }}"
                                                />
                                            </div>

                                            <div class="col-12 col-md-3 mb-1">
                                                <label class="form-label">
                                                    Dt Vencimento
                                                </label>
                                                <input
                                                    {{ (!$disabled) ?: 'disabled' }}
                                                    type="text"
                                                    class="form-control form-control-sm jq-datepicker"
                                                    placeholder="Dt Vencimento"
                                                    name="jinvoice-due"
                                                    value="{{ $Job?->invoice?->formattedDueDate }}"
                                                />
                                            </div>
                                            
                                            <div class="col-12 col-md-3 mb-1">
                                                <label class="form-label">
                                                    Total
                                                </label>
                                                <input
                                                    {{ (!$disabled) ?: 'disabled' }}
                                                    type="text"
                                                    class="form-control form-control-sm jq-mask-money"
                                                    placeholder="Total"
                                                    name="jinvoice-total"
                                                    data-thousands="{{ $mJobInvoice::PRICE_THOUSAND_SEP }}"
                                                    data-decimal="{{ $mJobInvoice::PRICE_DECIMAL_SEP }}"
                                                    value="{{ $Job?->invoice?->formattedTotal ?: '' }}"
                                                />
                                            </div>
                                        </div>

                                        <br />

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label">
                                                    PDF da Nota
                                                </label>
                                                <div class="input-group flex-nowrap">
                                                    <div class="custom-file w-100">
                                                        <input
                                                            {{ (!$disabled) ?: 'disabled' }}
                                                            class="form-control form-control-sm"
                                                            type="file"
                                                            accept="text/xml, application/pdf"
                                                            name="jinvoice-path"
                                                            id="jinvoice-path"
                                                        />
                                                    </div>

                                                    @if (!$disabled && !empty($Job?->invoice?->invoice_path))
                                                        <button
                                                            class="btn btn-danger btn-sm"
                                                            type="button"
                                                            onclick="window.open('{{ $Job?->invoice?->invoice_path }}')"
                                                        >
                                                            <i class="fas fa-file-pdf text-white"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <div class="text-end">
                        @if (in_array($type, ['add', 'edit']))
                            <button type="submit" class="btn btn-ciclo-yellow">Salvar</button>
                        @endif
                        
                        <a href="{{ route('job.index') }}" class="btn btn-light">Voltar para lista</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection