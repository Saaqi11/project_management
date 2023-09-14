@extends('owner.layouts.app')

@section('content')
    <!-- Right Content Start -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Content Wrapper Start -->
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ __('Settings') }}</h3>
                                </div>

                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                                title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item"><a href="#"
                                                title="{{ __('Settings') }}">{{ __('Settings') }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Settings Page Layout Wrap Area row Start -->
                    <div class="settings-page-layout-wrap position-relative">
                        <div class="row">
                            <!-- Account settings Left Side Start-->
                            @include('owner.setting.sidebar')
                            <!-- Account settings Left Side End-->

                            <!-- Account settings Area Right Side Start-->
                            <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-9">
                                <div class="account-settings-rightside bg-off-white theme-border radius-4 p-25">

                                    <!-- Invoice Type Settings Page Start -->
                                    <div class="invoice-type-settings-page-area">

                                        <!-- Account Settings Content Box Start -->
                                        <div class="account-settings-content-box">

                                            <div class="account-settings-title border-bottom mb-20 pb-20">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h4>{{ $pageTitle }}</h4>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="property-details-right text-end">
                                                            <button type="button" class="theme-btn" id="add"
                                                                title="{{ __('Add Invoice Type') }}">
                                                                {{ __('Add Invoice Type') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Invoice Type Table Area Start -->
                                            <div class="invoice-type-table-area">
                                                <!-- datatable Start -->
                                                <div class="table-responsive bg-white theme-border radius-4 p-25">
                                                    <table id="datatableSettingsInvoiceType"
                                                        class="table bg-white theme-border p-20 dt-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th class="all">{{ __('Name') }}</th>
                                                                <th class="all">{{ __('Tax') }}</th>
                                                                <th class="desktop">{{ __('Action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($invoiceTypes as $invoiceType)
                                                                <tr>
                                                                    <td>{{ $invoiceType->name }}</td>
                                                                    <td>{{ number_format($invoiceType->tax, 2) }}{{ taxSetting(auth()->id())->type == TAX_TYPE_PERCENTAGE ? '%' : '' }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="tbl-action-btns d-inline-flex">
                                                                            <a class="p-1 tbl-action-btn edit"
                                                                                data-item="{{ $invoiceType }}"
                                                                                title="Edit"><span class="iconify"
                                                                                    data-icon="clarity:note-edit-solid"></span>
                                                                            </a>
                                                                            <a href="#"
                                                                                class="p-1 tbl-action-btn deleteItem"
                                                                                data-formid="delete_row_form_{{ $invoiceType->id }}"
                                                                                title="Delete"><span class="iconify"
                                                                                    data-icon="ep:delete-filled"></span></a>
                                                                            <form
                                                                                action="{{ route('owner.setting.invoice-type.destroy', [$invoiceType->id]) }}"
                                                                                method="post"
                                                                                id="delete_row_form_{{ $invoiceType->id }}">
                                                                                {{ method_field('DELETE') }}
                                                                                <input type="hidden" name="_token"
                                                                                    value="{{ csrf_token() }}">
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- datatable End -->
                                            </div>
                                            <!-- Invoice Type Table Area End -->

                                        </div>
                                        <!-- Account Settings Content Box End -->

                                    </div>
                                    <!-- Invoice Type Settings Page End -->

                                </div>
                            </div>
                            <!-- Account settings Area Right Side End-->

                        </div>
                    </div>
                    <!-- Settings Page Layout Wrap Area row End -->

                </div>
                <!-- Page Content Wrapper End -->

            </div>

        </div>
        <!-- End Page-content -->

    </div>
    <!-- Right Content End -->

    <!-- Add Invoice Type Modal Start -->
    <div class="modal fade" id="addInvoiceTypeModal" tabindex="-1" aria-labelledby="addInvoiceTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addInvoiceTypeModalLabel">{{ __('Add Invoice Type') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                            class="iconify" data-icon="akar-icons:cross"></span>
                    </button>
                </div>
                <form class="ajax" action="{{ route('owner.setting.invoice-type.store') }}" method="post"
                    data-handler="getShowMessage">
                    <div class="modal-body">
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <label
                                        class="label-text-title color-heading font-medium mb-2">{{ __('Name') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ __('Name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Tax') }}
                                        ({{ taxSetting(auth()->id())->type == TAX_TYPE_PERCENTAGE ? '%' : 'Fixed' }})</label>
                                    <input type="text" name="tax" class="form-control"
                                        placeholder="{{ __('Tax') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="theme-btn-back me-3" data-bs-dismiss="modal"
                            title="{{ __('Back') }}">{{ __('Back') }}</button>
                        <button type="submit" class="theme-btn me-3"
                            title="{{ __('Add Invoice Type') }}">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade edit_modal" id="editInvoiceTypeModal" tabindex="-1"
        aria-labelledby="editInvoiceTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editInvoiceTypeModalLabel">{{ __('Edit Invoice Type') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                            class="iconify" data-icon="akar-icons:cross"></span>
                    </button>
                </div>
                <form class="ajax" action="{{ route('owner.setting.invoice-type.store') }}" method="post"
                    data-handler="getShowMessage">
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <label
                                        class="label-text-title color-heading font-medium mb-2">{{ __('Name') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ __('Name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Tax') }}
                                        ({{ taxSetting(auth()->id())->type == TAX_TYPE_PERCENTAGE ? '%' : 'Fixed' }})</label>
                                    <input type="text" name="tax" class="form-control"
                                        placeholder="{{ __('Tax') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="theme-btn-back me-3" data-bs-dismiss="modal"
                            title="Back">{{ __('Back') }}</button>
                        <button type="submit" class="theme-btn me-3"
                            title="{{ __('Update') }}">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')

    <script src="{{ asset('/') }}assets/js/pages/settings-invoice-type-datatables.init.js"></script>
    <script src="{{ asset('assets/js/custom/invoice-type.js') }}"></script>
@endpush
