@inject('mJob', 'App\Models\Job')

@extends('layout.core', [
    'PAGE_TITLE' => 'Sistema de Gerenciamento de Produção'
])

@section('HEADER_CUSTOM_CSS')
    @yield('DASHBOARD_CUSTOM_CSS')

    <!-- Custom CSS -->
    <link href="/free-dash/assets/extra-libs/c3/c3.min.css" rel="stylesheet" />
    <link href="/free-dash/assets/extra-libs/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="/free-dash/assets/extra-libs/jquery-ui-1.13.2/jquery-ui.min.css" rel="stylesheet" />

    <style>
        html, #main-wrapper {
            background-color: #000;
            color: #FFF;
        }
        #ciclo-show-jobs {
            width: 25%;
            float: right;
        }
        .card {
            background-color: #FFDE3F !important;
            color: #000 !important;
            margin-bottom: 15px !important;
        }
        .card .card-body {
            padding: 1.5%;
        }
        .card.last-week {
            background-color: #ff9f3f !important;
        }

        @media (max-width: 768px) {
            #ciclo-show-jobs {
                float: none;

                margin-left: auto;
                margin-right: auto;
                display: block;
            }
            #header-first-col {
                text-align: center;
            }
        }
    </style>
@endsection

@section('BODY_CONTENT')
    <div class="w-100 p-2">
        <div class="row mb-5">
            <div id="header-first-col" class="col-12 mb-3 col-md-3 mb-md-0">
                @php
                $now = \Carbon\Carbon::now();
                @endphp

                {{ $now->format('d/m/Y') }}
                <br />
                {{ ucfirst($now->locale('pt_BR')->dayName) }} {{ $now->format('H:i') }}
            </div>

            <div class="col-12 mb-3 text-center col-md-6 mb-md-0">
                <h1 class="text-center">
                    Sistema de Gerenciamento de Produção
                </h1>
                <div class="text-center">                          
                    <a href="{{ route('site.dashboard') }}" class="btn btn-light">Voltar</a>
                </div>
            </div>

            <div class="col-12 float-none col-md-3">
                <img id="ciclo-show-jobs" src="/img/logo-show-jobs.png" alt="Sistema SGP - Ciclo Comunicação" class="img-fluid" />
            </div>
        </div>

        @php
        $arrJobs = $mJob::getShowJobsData();
        $arrJobs = $mJob::orderShowJobs($arrJobs);

        // cut array in half
        $cutNbr = ceil(count($arrJobs) / 2);
        $arrJobs1 = array_slice($arrJobs, 0, $cutNbr);
        $arrJobs2 = array_slice($arrJobs, $cutNbr);
        @endphp

        <div class="row">
            <div class="col-12 col-md-6">
                @foreach ($arrJobs1 as $job)
                    {!! $mJob::showJobsCard($job); !!}
                @endforeach
            </div>

            <div class="col-12 col-md-6">
                @foreach ($arrJobs2 as $job)
                    {!! $mJob::showJobsCard($job); !!}
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('FOOTER_CUSTOM_JS')
    @yield('DASHBOARD_CUSTOM_JS')

    <!-- apps -->
    <script src="/free-dash/js/feather.min.js"></script>
    <script src="/free-dash/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <!--Custom JavaScript -->
    <script src="/free-dash/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <script src="/free-dash/assets/extra-libs/c3/d3.min.js"></script>
    <script src="/free-dash/assets/extra-libs/c3/c3.min.js"></script>
    <script src="/free-dash/js/pages/dashboards/dashboard1.min.js"></script>
    <script src="/free-dash/assets/extra-libs/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="/free-dash/assets/extra-libs/jquery-ui-1.13.2/jquery-ui.min.js"></script>

    <script>
        window.setTimeout( function() {
            window.location.reload();
        }, 60000);
    </script>
@endsection