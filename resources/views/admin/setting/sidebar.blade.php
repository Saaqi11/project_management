<div class="col-md-12 col-lg-12 col-xl-4 col-xxl-3">
    <div class="account-settings-leftside bg-white theme-border radius-4 p-20 mb-25">
        <div class="tenants-details-leftsidebar-wrap d-flex">
            <ul class="account-settings-menu list-group">
                <li>
                    <a href="{{ route('admin.setting.general-setting') }}"
                        class="account-settings-menu-item {{ @$subGeneralSettingActiveClass }}">
                        <span class="iconify" data-icon="carbon:settings"></span>{{ __('Basic Setting') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.setting.color-setting') }}"
                        class="account-settings-menu-item {{ @$subColorSettingActiveClass }}">
                        <span class="iconify"
                            data-icon="fluent:color-background-24-regular"></span>{{ __('Color Setting') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.language.index') }}"
                        class="account-settings-menu-item {{ @$subLanguageActiveClass }}">
                        <span class="iconify" data-icon="clarity:language-line"></span>{{ __('Language') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.setting.currency.index') }}"
                        class="account-settings-menu-item {{ @$subCurrencyActiveClass }}">
                        <span class="iconify" data-icon="heroicons:currency-dollar"></span>{{ __('Currency') }}
                    </a>
                </li>
                @if (isAddonInstalled('PROTYSAAS') > 1)
                    <li>
                        <a href="{{ route('admin.setting.gateway.index') }}"
                            class="account-settings-menu-item {{ @$subGatewaySettingActiveClass }}">
                            <span class="iconify"
                                data-icon="fluent:payment-16-regular"></span>{{ __('Payment Gateway') }}
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.setting.smtp.setting') }}"
                        class="account-settings-menu-item {{ @$subSmtpSettingActiveClass }}">
                        <span class="iconify" data-icon="mdi:git-issue"></span>{{ __('SMTP Setting') }}
                    </a>
                </li>
                @if (isAddonInstalled('PROTYSMS', 0) > 0)
                    <li>
                        <a href="{{ route('admin.setting.sms.setting') }}"
                            class="account-settings-menu-item {{ @$subSmsSettingActiveClass }}">
                            <span class="iconify"
                                data-icon="icon-park-outline:setting-web"></span>{{ __('Sms Setting') }}
                        </a>
                    </li>
                @endif
                @if (isAddonInstalled('PROTYAGREEMENT', 0) > 0)
                    <li>
                        <a href="{{ route('admin.setting.agreement.setting') }}"
                            class="account-settings-menu-item {{ @$subAgreementSettingActiveClass }}">
                            <span class="iconify"
                                data-icon="icon-park-outline:agreement"></span>{{ __('Agreement Setting') }}
                        </a>
                    </li>
                @endif
                @if (isAddonInstalled('PROTYSAAS') > 1)
                    <li class="mt-25">
                        <b>{{ __('Landing Page Setting') }}</b>
                    </li>
                    <li>
                        <a href="{{ route('admin.home-setting.section') }}"
                            class="account-settings-menu-item {{ @$subHomeSectionSettingActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('Section Show/Hide') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.feature.index') }}"
                            class="account-settings-menu-item {{ @$subFeatureActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('Features') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.how-it-work.index') }}"
                            class="account-settings-menu-item {{ @$subHowItWorkActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('How It Work') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.core-page.index') }}"
                            class="account-settings-menu-item {{ @$subCorePageActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('Core Page') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.testimonials.index') }}"
                            class="account-settings-menu-item {{ @$subTestimonialsActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('Testimonials') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.faq.index') }}"
                            class="account-settings-menu-item {{ @$subFaqActiveClass }}">
                            <span class="iconify" data-icon="carbon:settings"></span>{{ __('Faq') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
