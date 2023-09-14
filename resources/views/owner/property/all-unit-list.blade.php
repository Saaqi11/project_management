@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                                title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('owner.property.allUnit') }}"
                                                title="{{ __('Properties') }}">{{ __('Properties') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tenants-details-layout-wrap position-relative">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                                <div class="account-settings-rightside bg-off-white theme-border radius-4 p-25">
                                    <div class="tenants-details-payment-history">
                                        <div class="account-settings-content-box">
                                            <div class="tenants-details-payment-history-table">
                                                <table id="allDataTable" class="table responsive theme-border p-20">
                                                    <thead>
                                                        <tr>
                                                            <th class="all">{{ __('SL') }}</th>
                                                            <th class="all">{{ __('Name') }}</th>
                                                            <th class="all">{{ __('Property') }}</th>
                                                            <th class="all">{{ __('Tenant') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($units as $unit)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $unit->unit_name }}</td>
                                                                <td>{{ $unit->property_name }}</td>
                                                                <td>
                                                                    @if ($unit->first_name)
                                                                        <span class="text-success">{{ $unit->first_name }}
                                                                            {{ $unit->last_name }}</span>
                                                                    @else
                                                                        <span
                                                                            class="text-danger">{{ __('Not Available') }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/pages/alldatatables.init.js') }}"></script>
@endpush
