<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('Invoice') }}</title>
    @include('common.layouts.style')
</head>

<body>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="invoice-preview-wrap" id="printDiv1">
                        <div class="invoice-heading-part">
                            <div class="invoice-heading-left">
                                <img src="{{ getSettingImage('app_logo') }}" alt="">
                                <h4>{{ $invoice->invoice_no }}</h4>
                            </div>
                            <div class="invoice-heading-right">
                                <div class="invoice-heading-right-status-btn">
                                    {{ $invoice->status == INVOICE_STATUS_PAID ? 'Paid' : 'Pending' }}</div>
                            </div>
                        </div>
                        <div class="invoice-address-part">
                            <div class="invoice-address-part-left">
                                <h4 class="invoice-generate-title">{{ __('Invoice To') }}</h4>
                                <div class="invoice-address">
                                    <h5>{{ $tenant->first_name }} {{ $tenant->last_name }}</h5>
                                    <small>{{ $tenant->email }}</small>
                                    <h6>{{ $tenant->property_name }}</h6>
                                    <small>{{ $tenant->unit_name }}</small>
                                </div>
                            </div>
                            <div class="invoice-address-part-right">
                                <h4 class="invoice-generate-title">{{ __('Pay To') }}</h4>
                                <div class="invoice-address">
                                    <h5>{{ getOption('app_name') }}</h5>
                                    <h6>{{ getOption('app_location') }}</h6>
                                    <small>{{ getOption('app_contact_number') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="invoice-table-part">

                            <h4 class="invoice-generate-title invoice-heading-color">{{ __('Invoice Items') }}</h4>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="invoice-heading-color">{{ __('Type') }}</th>
                                            <th class="invoice-heading-color">{{ __('Description') }}</th>
                                            <th class="invoice-heading-color">{{ __('Date') }}</th>
                                            <th class="invoice-tbl-last-field invoice-heading-color">
                                                {{ __('Amount') }}</th>
                                            <th class="invoice-tbl-last-field invoice-heading-color">
                                                {{ __('Tax') }}</th>
                                            <th class="invoice-tbl-last-field invoice-heading-color">
                                                {{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td>{{ $item->invoiceType?->name }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                                <td class="invoice-tbl-last-field">{{ currencyPrice($item->amount) }}
                                                <td class="invoice-tbl-last-field">
                                                    {{ currencyPrice($item->tax_amount) }}
                                                </td>
                                                <td class="invoice-tbl-last-field">
                                                    {{ currencyPrice($item->amount + $item->tax_amount) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="show-total-box">
                                <div class="invoice-tbl-last-field">{{ __('Total') }}: <span
                                        class="invoice-heading-color">{{ currencyPrice($invoice->amount) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="transaction-table-part">
                            <h4 class="invoice-generate-title invoice-heading-color">{{ __('Transaction Details') }}
                            </h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="invoice-heading-color">{{ __('Date') }}</th>
                                            <th class="invoice-heading-color">{{ __('Gateway') }}</th>
                                            <th class="invoice-heading-color">{{ __('Transaction ID') }}</th>
                                            <th class="invoice-tbl-last-field invoice-heading-color">
                                                {{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($order)
                                            <tr>
                                                <td>{{ $order?->created_at->format('Y-m-d') }}</td>
                                                <td>{{ $order?->gatewayTitle ?? 'Cash' }}</td>
                                                <td>{{ $order?->payment_id }}</td>
                                                <td class="invoice-tbl-last-field">{{ currencyPrice($order?->total) }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">{{ __('No Data Found') }}</td>
                                            </tr>
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('common.layouts.script')
    <script>
        function printDiv() {
            $(".print-button").hide();

            var DocumentContainer = document.getElementById('printDiv1');
            var WindowObject = window.open('', "_blank",
                "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();
            WindowObject.focus();
            WindowObject.print();
            WindowObject.close();
        }

        window.print()
    </script>
</body>

</html>
