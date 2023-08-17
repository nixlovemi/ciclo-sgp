@inject('MainMenu', 'App\View\Components\MainMenu')

@php
/*
View variables:
===============
    - $Job: ?Job
    - $dataParent: string
    - $disabled: bool
    - $showForm: bool
*/

$cardId = md5(date('YmdHis') . rand(1, 100));
$hasQuote = $Job?->quote;
@endphp

<div id="job-part-quote-card" class="card mb-1">
    <div class="card-header" id="heading{{ $cardId }}">
        <div class="row g-0">
            <div class="col-11">
                <h5 class="m-0">
                    <a
                        class="custom-accordion-title d-block pt-2 pb-2"
                        data-bs-toggle="collapse"
                        href="#collapse{{ $cardId }}"
                        aria-expanded="false"
                        aria-controls="collapse{{ $cardId }}"
                    >
                        Orçamento
                        <span class="float-end">
                            <i class="mdi mdi-chevron-down accordion-arrow"></i>
                        </span>
                    </a>
                </h5>
            </div>
            <div class="col-1 align-items-end">
                <div class="ms-auto">
                    <div class="dropdown sub-dropdown">
                        <button
                            class="btn btn-link text-muted dropdown-toggle"
                            type="button"
                            id="dd1"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd1" style="">
                            @if($hasQuote)
                                <a
                                    class="dropdown-item"
                                    target="_blank"
                                    href="{{ route('quote.pdf', ['codedId' => $Job?->quote->codedId]) }}"
                                >Ver PDF</a>
                                <a
                                    class="dropdown-item job-remove-quote"
                                    href="{{ $MainMenu::JS_URL }}"
                                >Desvincular</a>
                                <a
                                    class="dropdown-item job-delete-quote"
                                    href="{{ $MainMenu::JS_URL }}"
                                >Deletar</a>
                            @else
                                <a
                                    class="dropdown-item job-add-quote"
                                    href="{{ $MainMenu::JS_URL }}"
                                >Adicionar</a>
                                <a
                                    class="dropdown-item job-link-quote"
                                    href="{{ $MainMenu::JS_URL }}"
                                    data-jid="{{ $Job?->codedId }}"
                                >Vincular</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="collapse{{ $cardId }}" class="collapse" aria-labelledby="heading{{ $cardId }}" data-parent="{{ $dataParent }}">
        <div class="card-body">
            @if (!$hasQuote)
                <i class="fas fa-info-circle"></i>
                Nenhum orçamento vinculado com o Job. Use o menu a direita para adicionar ou vincular um orçamento.
            @else
                @include('quote.showDiv', [
                    'Quote' => $Job?->quote,
                    'type' => 'edit',
                    'disabled' => $disabled,
                    'showForm' => $showForm ?? null
                ])
            @endif
        </div>
    </div>
</div>