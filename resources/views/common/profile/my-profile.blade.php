@extends(getLayout() . '.layouts.app')

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
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                                title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item">{{ __('Profile') }}</li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Profile Page Area row Start -->
                    <div class="row">

                        <!-- Profile Page Content Area Start -->
                        <div class="profile-page-content-area">
                            <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                    <div class="settings-inner-box-fields p-20 pb-0">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-between">
                                                <!-- Upload Profile Photo Box Start -->
                                                <div
                                                    class="upload-profile-photo-box upload-profile-photo-with-delete-btn mb-25">
                                                    <div class="profile-user position-relative d-inline-block">
                                                        <img src="@if (auth()->user()->image) {{ auth()->user()->image }} @else {{ asset('assets/images/users/empty-user.jpg') }} @endif"
                                                            class="rounded-circle avatar-xl default-user-profile-image"
                                                            alt="user-profile-image">
                                                        <div
                                                            class="avatar-xs p-0 rounded-circle default-profile-photo-edit">
                                                            <input id="default-profile-img-file-input" type="file"
                                                                name="image" class="default-profile-img-file-input">
                                                            <label for="default-profile-img-file-input"
                                                                class="default-profile-photo-edit avatar-xs">
                                                                <span class="avatar-title rounded-circle"
                                                                    title="Change Image">
                                                                    <i class="ri-camera-fill"></i>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Upload Profile Photo Box End -->
                                                @if (auth()->user()->role == USER_ROLE_TENANT || auth()->user()->role == USER_ROLE_MAINTAINER)
                                                    <div>
                                                        <button type="button" class="theme-btn-red" id="deleteMyAccountBtn"
                                                            title="{{ __('Delete my account') }}">{{ __('Delete my Account') }}</button>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-25">
                                                <label
                                                    class="label-text-title color-heading font-medium mb-2">{{ __('First Name') }}</label>
                                                <input type="text" class="form-control" name="first_name"
                                                    placeholder="{{ __('First Name') }}"
                                                    value="{{ auth()->user()->first_name }}">
                                                @error('first_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-25">
                                                <label
                                                    class="label-text-title color-heading font-medium mb-2">{{ __('Last Name') }}</label>
                                                <input type="text" class="form-control" name="last_name"
                                                    placeholder="{{ __('Last Name') }}"
                                                    value="{{ auth()->user()->last_name }}">
                                                @error('last_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-25">
                                                <label
                                                    class="label-text-title color-heading font-medium mb-2">{{ __('Email') }}</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="{{ __('Email') }}" value="{{ auth()->user()->email }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-25">
                                                <label
                                                    class="label-text-title color-heading font-medium mb-2">{{ __('Contact Number') }}</label>
                                                <input type="text" class="form-control"
                                                    name="contact_number" placeholder="{{ __('Contact Number') }}"
                                                    value="{{ auth()->user()->contact_number }}">
                                                @error('contact_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-25">
                                                <button type="submit" class="theme-btn"
                                                    title="{{ __('Update') }}">{{ __('Update') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Profile Page Content Area End -->
                    </div>
                    <!-- Profile Page Area row End -->
                </div>
                <!-- Page Content Wrapper End -->
            </div>
        </div>
        <!-- End Page-content -->
    </div>
    <!-- Right Content End -->

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteModalLabel">{{ __('Information') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                            class="iconify" data-icon="akar-icons:cross"></span></button>
                </div>
                <form class="ajax" action="{{ route('delete-my-account') }}" method="POST" autocomplete="off"
                    data-handler="getShowMessage">
                    <div class="modal-body">
                        @csrf
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <p>Please type your email of this account <span
                                            class="fw-bold">({{ auth()->user()->email }})</span> to confirm its
                                        deletion from this application. After successfully deletion you can't recover this
                                        account</p>
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Email') }}
                                        <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="email" autocomplete="off"
                                        placeholder="{{ __('Email') }}">
                                </div>
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Password') }}
                                        <strong class="text-danger">*</strong></label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="{{ __('Password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="theme-btn-back me-3" data-bs-dismiss="modal"
                            title="{{ __('Back') }}">{{ __('Back') }}</button>
                        <button type="submit" class="theme-btn me-3"
                            title="{{ __('Delete') }}">{{ __('Delete') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- default profile-photo upload/change init js -->
    <script src="{{ asset('/') }}assets/js/pages/default-profile-setting.init.js"></script>
    <script src="{{ asset('assets/js/custom/delete-my-account.js') }}"></script>
@endpush
