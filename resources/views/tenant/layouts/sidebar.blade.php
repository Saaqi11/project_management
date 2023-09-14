<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ route('tenant.dashboard') }}">
                        <i class="ri-dashboard-line"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenant.invoice.index') }}">
                        <i class="ri-bill-line"></i>
                        <span>{{ __('Invoice') }}</span>
                    </a>
                </li>
                @if (ownerCurrentPackage(auth()->user()->owner_user_id)?->ticket_support == ACTIVE || isAddonInstalled('PROTYSAAS') < 1)
                    <li>
                        <a href="{{ route('tenant.ticket.index') }}">
                            <i class="ri-bookmark-2-line"></i>
                            <span>{{ __('My Tickets') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('tenant.information.index') }}">
                        <i class="ri-folder-info-line"></i>
                        <span>{{ __('Information') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('tenant.document.index') }}">
                        <i class="ri-article-line"></i>
                        <span>{{ __('Documents') }}</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="ri-account-circle-line"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('profile') }}">{{ __('My Profile') }}</a></li>
                        <li><a href="{{ route('change-password') }}">{{ __('Change Password') }}</a></li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
