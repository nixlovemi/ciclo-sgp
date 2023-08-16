@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $BODY_TITLE: string
*/

$User = $SysUtils::getLoggedInUser();
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('HEADER_CUSTOM_CSS')
    @yield('DASHBOARD_CUSTOM_CSS')

    <!-- Custom CSS -->
    <link href="/free-dash/assets/extra-libs/c3/c3.min.css" rel="stylesheet" />
    <link href="/free-dash/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet" />
    <link href="/free-dash/assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
@endsection

@section('BODY_CONTENT')
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin6">
        <nav class="navbar top-navbar navbar-expand-xl">
            <div class="navbar-header" data-logobg="skin6">
                <!-- This is for the sidebar toggle which is visible on mobile only -->
                <a class="nav-toggler waves-effect waves-light d-block d-lg-none" href="javascript:void(0)">
                    <i class="ti-menu ti-close"></i>
                </a>
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-brand">
                    <!-- Logo icon -->
                    <a href="{{ route('site.dashboard') }}">
                        <img id="dashboard-logo-ciclo" src="/img/Logo-Ciclo.jpg" alt="Sistema SGP - Ciclo Comunicação" class="img-fluid" />
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Toggle which is visible on mobile only -->
                <!-- ============================================================== -->
                <a
                    class="topbartoggler d-block d-lg-none waves-effect waves-light"
                    href="javascript:void(0)"
                    data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                >
                    <i class="ti-more"></i>
                </a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse collapse" id="navbarSupportedContent">
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav float-left me-auto ms-3 ps-1">
                    @php
                    /*
                    <!-- Notification -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle pl-md-3 position-relative" href="javascript:void(0)"
                            id="bell" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span><i data-feather="bell" class="svg-icon"></i></span>
                            <span class="badge text-bg-primary notify-no rounded-circle">5</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                            <ul class="list-style-none">
                                <li>
                                    <div class="message-center notifications position-relative">
                                        <!-- Message -->
                                        <a href="javascript:void(0)"
                                            class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <div class="btn btn-danger rounded-circle btn-circle"><i
                                                    data-feather="airplay" class="text-white"></i></div>
                                            <div class="w-75 d-inline-block v-middle ps-2">
                                                <h6 class="message-title mb-0 mt-1">Luanch Admin</h6>
                                                <span class="font-12 text-nowrap d-block text-muted">Just see
                                                    the my new
                                                    admin!</span>
                                                <span class="font-12 text-nowrap d-block text-muted">9:30 AM</span>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)"
                                            class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <span class="btn btn-success text-white rounded-circle btn-circle"><i
                                                    data-feather="calendar" class="text-white"></i></span>
                                            <div class="w-75 d-inline-block v-middle ps-2">
                                                <h6 class="message-title mb-0 mt-1">Event today</h6>
                                                <span
                                                    class="font-12 text-nowrap d-block text-muted text-truncate">Just
                                                    a reminder that you have event</span>
                                                <span class="font-12 text-nowrap d-block text-muted">9:10 AM</span>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)"
                                            class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <span class="btn btn-info rounded-circle btn-circle"><i
                                                    data-feather="settings" class="text-white"></i></span>
                                            <div class="w-75 d-inline-block v-middle ps-2">
                                                <h6 class="message-title mb-0 mt-1">Settings</h6>
                                                <span
                                                    class="font-12 text-nowrap d-block text-muted text-truncate">You
                                                    can customize this template
                                                    as you want</span>
                                                <span class="font-12 text-nowrap d-block text-muted">9:08 AM</span>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)"
                                            class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <span class="btn btn-primary rounded-circle btn-circle"><i
                                                    data-feather="box" class="text-white"></i></span>
                                            <div class="w-75 d-inline-block v-middle ps-2">
                                                <h6 class="message-title mb-0 mt-1">Pavan kumar</h6> <span
                                                    class="font-12 text-nowrap d-block text-muted">Just
                                                    see the my admin!</span>
                                                <span class="font-12 text-nowrap d-block text-muted">9:02 AM</span>
                                            </div>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <a class="nav-link pt-3 text-center text-dark" href="javascript:void(0);">
                                        <strong>Check all notifications</strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- End Notification -->
                    <!-- ============================================================== -->
                    <!-- create new -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="settings" class="svg-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="javascript:void(0)">
                            <div class="customize-input">
                                <select
                                    class="custom-select form-control bg-white custom-radius custom-shadow border-0">
                                    <option selected>EN</option>
                                    <option value="1">AB</option>
                                    <option value="2">AK</option>
                                    <option value="3">BE</option>
                                </select>
                            </div>
                        </a>
                    </li>
                    */
                    @endphp
                </ul>
                <!-- ============================================================== -->
                <!-- Right side toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav float-end">
                    <!-- ============================================================== -->
                    <!-- Search -->
                    <!-- ============================================================== -->
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="javascript:void(0)">
                            @yield('DASHBOARD_SEARCH_BOX')

                            @php
                            /*
                            <form>
                                <div class="customize-input">
                                    <input class="form-control custom-shadow custom-radius border-0 bg-white"
                                        type="search" placeholder="Search" aria-label="Search">
                                    <i class="form-control-icon" data-feather="search"></i>
                                </div>
                            </form>
                            */
                            @endphp
                        </a>
                    </li>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ $User->getPictureUrl() }}" alt="user" class="rounded-circle" width="40" />
                            <span class="ms-2 d-none d-lg-inline-block">
                                <span>Olá,</span>
                                <span class="text-dark">{{ $User->name }}</span>
                                <i data-feather="chevron-down" class="svg-icon"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-right user-dd animated flipInY">
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                <i data-feather="user" class="svg-icon me-2 ms-1"></i>
                                Meu Perfil
                            </a>
                            <a class="dropdown-item" href="{{ route('user.changePwd') }}">
                                <i class="fas fa-key me-2 ms-1"></i>
                                Alterar Senha
                            </a>
                            <a class="dropdown-item" href="{{ route('site.login') }}">
                                <i data-feather="power" class="svg-icon me-2 ms-1"></i>
                                Sair
                            </a>

                            @php
                                # <div class="dropdown-divider"></div>
                            @endphp
                        </div>
                    </li>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                </ul>
            </div>
        </nav>
    </header>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <aside class="left-sidebar" data-sidebarbg="skin6">
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar navigation-->
            @yield('DASHBOARD_MENU')
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
    <!-- ============================================================== -->
    <!-- End Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-7 align-self-center">
                    <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">
                        {{ $BODY_TITLE ?? '' }}
                    </h3>

                    @php
                    /*
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 p-0">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    */
                    @endphp
                </div>
                <div class="col-5 align-self-center">
                    @php
                    /*
                    <div class="customize-input float-end">
                        <select class="custom-select custom-select-set form-control bg-white border-0 custom-shadow custom-radius">
                            <option selected>Aug 23</option>
                            <option value="1">July 23</option>
                            <option value="2">Jun 23</option>
                        </select>
                    </div>
                    */
                    @endphp
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            @yield('DASHBOARD_CONTENT')
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer text-center text-muted">
            Todos os direitos reservados para Ciclo Comunicação. Desenvolvido por <a target="_blank" href="mailto:leandro.parra.85@gmail.com">Leandro</a>.
        </footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
@endsection

@section('FOOTER_CUSTOM_JS')
    @yield('DASHBOARD_CUSTOM_JS')

    <!-- apps -->
    <script src="/free-dash/js/app-style-switcher.js"></script>
    <script src="/free-dash/js/feather.min.js"></script>
    <script src="/free-dash/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="/free-dash/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="/free-dash/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <script src="/free-dash/assets/extra-libs/c3/d3.min.js"></script>
    <script src="/free-dash/assets/extra-libs/c3/c3.min.js"></script>
    <script src="/free-dash/assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="/free-dash/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="/free-dash/assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="/free-dash/assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js"></script>
    <script src="/free-dash/js/pages/dashboards/dashboard1.min.js"></script>
@endsection