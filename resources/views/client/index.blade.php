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
    <div class="row">
        <div class="col-12">
            <livewire:table
                :config="App\Tables\ClientsTable::class"
            />
        </div>
    </div>
@endsection