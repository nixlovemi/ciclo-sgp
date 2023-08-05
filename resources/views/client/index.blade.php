@inject('Permissions', 'App\Helpers\Permissions')

@extends('layout.dashboard', [
    'PAGE_TITLE' => 'Clientes',
    'BODY_TITLE' => 'Lista dos clientes'
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
    @if ($Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT))
        <a href="{{ route('client.add') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-plus"></i>
            Adicionar
        </a>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-body px-2 py-0">
                    <livewire:table
                        :config="App\Tables\ClientsTable::class"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection