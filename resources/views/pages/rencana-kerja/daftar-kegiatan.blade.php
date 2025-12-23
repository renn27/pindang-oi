@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="From Elements" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Kegiatan">
            <x-tables.basic-tables.basic-tables-five />
        </x-common.component-card>
    </div>
@endsection
