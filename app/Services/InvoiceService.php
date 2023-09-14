<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\GatewayCurrency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\Order;
use App\Models\Property;
use App\Models\Tenant;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    use ResponseTrait;

    public function getAllInvoices()
    {
        $response['pageTitle'] = __('All Invoices');
        $response['invoices'] = Invoice::where('owner_user_id', auth()->id())->with(['property', 'propertyUnit', 'invoiceItems'])->latest()->get();
        $response['properties'] = Property::where('owner_user_id', auth()->id())->get();
        $response['invoiceTypes'] = InvoiceType::where('owner_user_id', auth()->id())->get();
        $response['pendingInvoices'] = Invoice::where('owner_user_id', auth()->id())->pending()->get();
        $response['paidInvoices'] = Invoice::where('owner_user_id', auth()->id())->paid()->get();
        $response['overDueInvoices'] = Invoice::where('owner_user_id', auth()->id())->overDue()->get();
        $response['totalInvoice'] = Invoice::where('owner_user_id', auth()->id())->count();
        $response['totalPendingInvoice'] = Invoice::where('owner_user_id', auth()->id())->pending()->count();
        $response['totalBankPendingInvoice'] = Invoice::query()
            ->where('invoices.owner_user_id', auth()->id())
            ->join('orders', 'invoices.order_id', '=', 'orders.id')
            ->join('gateways', 'orders.gateway_id', '=', 'gateways.id')
            ->where('gateways.slug', 'bank')
            ->where('orders.payment_status', INVOICE_STATUS_PENDING)
            ->count();
        $response['totalPaidInvoice'] = Invoice::where('owner_user_id', auth()->id())->paid()->count();
        $response['totalOverDueInvoice'] = Invoice::where('owner_user_id', auth()->id())->overDue()->count();
        return $response;
    }

    public function getAllInvoicesData($request)
    {
        $invoice = Invoice::query()
            ->where('invoices.owner_user_id', auth()->id())
            ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
            ->leftJoin('properties', 'invoices.property_id', '=', 'properties.id')
            ->leftJoin('gateways', 'orders.gateway_id', '=', 'gateways.id')
            ->leftJoin('file_managers', ['orders.deposit_slip_id' => 'file_managers.id', 'file_managers.origin_type' => (DB::raw("'App\\\Models\\\Order'"))])
            ->select(['invoices.*', 'gateways.title as gatewayTitle', 'gateways.slug as gatewaySlug', 'file_managers.file_name', 'file_managers.folder_name']);

        return datatables($invoice)
            ->addColumn('invoice', function ($invoice) {
                return '<h6>' . $invoice->invoice_no . '</h6>
                        <p class="font-13">' . $invoice->name . '</p>';
            })->addColumn('property', function ($invoice) {
                return '<h6>' . @$invoice->property->name . '</h6>
                        <p class="font-13">' . @$invoice->propertyUnit->unit_name . '</p>';
            })->addColumn('due_date', function ($item) {
                return $item->due_date;
            })->addColumn('amount', function ($invoice) {
                return currencyPrice(invoiceItemTotalAmount($invoice->id));
            })->addColumn('status', function ($invoice) {
                if ($invoice->status == INVOICE_STATUS_PAID) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Paid</div>';
                } elseif ($invoice->status == INVOICE_STATUS_OVER_DUE) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Due</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Pending</div>';
                }
            })->addColumn('gateway', function ($invoice) {
                if ($invoice->gatewaySlug == 'bank') {
                    return  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '" title="Bank slip download" download>' . $invoice->gatewayTitle . '</a>';
                }
                return $invoice->gatewayTitle;
            })->addColumn('action', function ($invoice) {
                $html = '<div class="tbl-action-btns d-inline-flex">';
                if ($invoice->status == INVOICE_STATUS_PENDING) {
                    $html .= '<button type="button" class="p-1 tbl-action-btn edit" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>';
                    $html .= '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                    $html .= '<button type="button" onclick="deleteItem(\'' . route('owner.invoice.destroy', $invoice->id) . '\', \'allInvoiceDatatable\')" class="p-1 tbl-action-btn" title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>';
                    if ($invoice->gatewaySlug == 'bank') {
                        $html .=  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '"  class="p-1 tbl-action-btn" title="Bank slip download" download><span class="iconify" data-icon="fa6-solid:download"></span></a>';
                        $html .= '<button type="button" class="p-1 tbl-action-btn payStatus" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Payment Status Change"><span class="iconify" data-icon="fluent:text-change-previous-20-filled"></span></button>';
                    }
                    $html .=  '<button type="button" class="p-1 tbl-action-btn reminder" data-id="' . $invoice->id . '" title="Send Reminder"><span class="iconify" data-icon="ri:send-plane-fill"></span></button>';
                } elseif ($invoice->status == INVOICE_STATUS_PAID) {
                    $html .=  '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                    $html .=  '<button type="button" class="p-1 tbl-action-btn reminder" data-id="' . $invoice->id . '" title="Send Reminder"><span class="iconify" data-icon="ri:send-plane-fill"></span></button>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['invoice', 'property', 'status', 'gateway', 'action'])
            ->make(true);
    }

    public function getPaidInvoicesData($request)
    {
        $invoice = Invoice::where('owner_user_id', auth()->id())->paid();
        return datatables($invoice)
            ->addColumn('invoice', function ($invoice) {
                return '<h6>' . $invoice->invoice_no . '</h6>
                        <p class="font-13">' . $invoice->name . '</p>';
            })->addColumn('property', function ($invoice) {
                return '<h6>' . @$invoice->property->name . '</h6>
                        <p class="font-13">' . @$invoice->propertyUnit->unit_name . '</p>';
            })->addColumn('due_date', function ($item) {
                return $item->due_date;
            })->addColumn('amount', function ($invoice) {
                return currencyPrice(invoiceItemTotalAmount($invoice->id));
            })->addColumn('status', function ($invoice) {
                if ($invoice->status == INVOICE_STATUS_PAID) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Paid</div>';
                } elseif ($invoice->status == INVOICE_STATUS_OVER_DUE) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Due</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Pending</div>';
                }
            })->addColumn('action', function ($invoice) {
                $html = '<div class="tbl-action-btns d-inline-flex">';
                if ($invoice->status == INVOICE_STATUS_PENDING) {
                    $html .= '<button type="button" class="p-1 tbl-action-btn edit" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>';
                    $html .= '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                    $html .= '<button type="button" onclick="deleteItem(\'' . route('owner.invoice.destroy', $invoice->id) . '\', \'allInvoiceDatatable\')" class="p-1 tbl-action-btn" title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>';
                    if ($invoice->gatewaySlug == 'bank') {
                        $html .=  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '"  class="p-1 tbl-action-btn" title="Bank slip download" download><span class="iconify" data-icon="fa6-solid:download"></span></a>';
                        $html .= '<button type="button" class="p-1 tbl-action-btn payStatus" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Payment Status Change"><span class="iconify" data-icon="fluent:text-change-previous-20-filled"></span></button>';
                    }
                } elseif ($invoice->status == INVOICE_STATUS_PAID) {
                    $html .=  '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['invoice', 'property', 'status', 'action'])
            ->make(true);
    }

    public function getPendingInvoicesData($request)
    {
        $invoice = Invoice::where('owner_user_id', auth()->id())->pending();
        return datatables($invoice)
            ->addColumn('invoice', function ($invoice) {
                return '<h6>' . $invoice->invoice_no . '</h6>
                        <p class="font-13">' . $invoice->name . '</p>';
            })->addColumn('property', function ($invoice) {
                return '<h6>' . @$invoice->property->name . '</h6>
                        <p class="font-13">' . @$invoice->propertyUnit->unit_name . '</p>';
            })->addColumn('due_date', function ($item) {
                return $item->due_date;
            })->addColumn('amount', function ($invoice) {
                return currencyPrice(invoiceItemTotalAmount($invoice->id));
            })->addColumn('status', function ($invoice) {
                if ($invoice->status == INVOICE_STATUS_PAID) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Paid</div>';
                } elseif ($invoice->status == INVOICE_STATUS_OVER_DUE) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Due</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Pending</div>';
                }
            })->addColumn('action', function ($invoice) {
                $html = '<div class="tbl-action-btns d-inline-flex">';
                if ($invoice->status == INVOICE_STATUS_PENDING) {
                    $html .= '<button type="button" class="p-1 tbl-action-btn edit" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>';
                    $html .= '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                    $html .= '<button type="button" onclick="deleteItem(\'' . route('owner.invoice.destroy', $invoice->id) . '\', \'allInvoiceDatatable\')" class="p-1 tbl-action-btn" title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>';
                    if ($invoice->gatewaySlug == 'bank') {
                        $html .=  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '"  class="p-1 tbl-action-btn" title="Bank slip download" download><span class="iconify" data-icon="fa6-solid:download"></span></a>';
                        $html .= '<button type="button" class="p-1 tbl-action-btn payStatus" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Payment Status Change"><span class="iconify" data-icon="fluent:text-change-previous-20-filled"></span></button>';
                    } else {
                        $html .= '<button type="button" class="p-1 tbl-action-btn payStatus" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Payment Status Change"><span class="iconify" data-icon="fluent:text-change-previous-20-filled"></span></button>';
                    }
                } elseif ($invoice->status == INVOICE_STATUS_PAID) {
                    $html .=  '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['invoice', 'property', 'status', 'action'])
            ->make(true);
    }

    public function getBankPendingInvoicesData()
    {
        $invoice = Invoice::query()
            ->join('orders', 'invoices.order_id', '=', 'orders.id')
            ->join('gateways', 'orders.gateway_id', '=', 'gateways.id')
            ->join('file_managers', ['orders.deposit_slip_id' => 'file_managers.id', 'file_managers.origin_type' => (DB::raw("'App\\\Models\\\Order'"))])
            ->select(['invoices.*', 'gateways.title as gatewayTitle', 'gateways.slug as gatewaySlug', 'file_managers.file_name', 'file_managers.folder_name'])
            ->where('gateways.slug', 'bank')
            ->where('invoices.owner_user_id', auth()->id())
            ->where('orders.payment_status', INVOICE_STATUS_PENDING);
        return datatables($invoice)
            ->addColumn('invoice', function ($invoice) {
                return '<h6>' . $invoice->invoice_no . '</h6>
                        <p class="font-13">' . $invoice->name . '</p>';
            })->addColumn('property', function ($invoice) {
                return '<h6>' . @$invoice->property->name . '</h6>
                        <p class="font-13">' . @$invoice->propertyUnit->unit_name . '</p>';
            })->addColumn('due_date', function ($item) {
                return $item->due_date;
            })->addColumn('amount', function ($invoice) {
                return currencyPrice(invoiceItemTotalAmount($invoice->id));
            })->addColumn('gateway', function ($invoice) {
                if ($invoice->gatewaySlug == 'bank') {
                    return  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '" title="Bank slip download" download>' . $invoice->gatewayTitle . '</a>';
                }
                return $invoice->gatewayTitle;
            })->addColumn('status', function ($invoice) {
                if ($invoice->status == INVOICE_STATUS_PAID) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Paid</div>';
                } elseif ($invoice->status == INVOICE_STATUS_OVER_DUE) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Due</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Pending</div>';
                }
            })->addColumn('action', function ($invoice) {
                $html = '<div class="tbl-action-btns d-inline-flex">';
                if ($invoice->status == INVOICE_STATUS_PENDING) {
                    $html .= '<button type="button" class="p-1 tbl-action-btn edit" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>';
                    $html .= '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                    $html .= '<button type="button" onclick="deleteItem(\'' . route('owner.invoice.destroy', $invoice->id) . '\', \'allInvoiceDatatable\')" class="p-1 tbl-action-btn" title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>';
                    if ($invoice->gatewaySlug == 'bank') {
                        $html .=  '<a href="' . getFileUrl($invoice->folder_name, $invoice->file_name) . '"  class="p-1 tbl-action-btn" title="Bank slip download" download><span class="iconify" data-icon="fa6-solid:download"></span></a>';
                        $html .= '<button type="button" class="p-1 tbl-action-btn payStatus" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Payment Status Change"><span class="iconify" data-icon="fluent:text-change-previous-20-filled"></span></button>';
                    }
                } elseif ($invoice->status == INVOICE_STATUS_PAID) {
                    $html .=  '<button type="button" class="p-1 tbl-action-btn view" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['invoice', 'property', 'status', 'gateway', 'action'])
            ->make(true);
    }

    public function getOverDueInvoicesData($request)
    {
        $invoice = Invoice::where('owner_user_id', auth()->id())->overDue();
        return datatables($invoice)
            ->addColumn('invoice', function ($invoice) {
                return '<h6>' . $invoice->invoice_no . '</h6>
                        <p class="font-13">' . $invoice->name . '</p>';
            })->addColumn('property', function ($invoice) {
                return '<h6>' . @$invoice->property->name . '</h6>
                        <p class="font-13">' . @$invoice->propertyUnit->unit_name . '</p>';
            })->addColumn('due_date', function ($item) {
                return $item->due_date;
            })->addColumn('amount', function ($invoice) {
                return currencyPrice(invoiceItemTotalAmount($invoice->id));
            })->addColumn('status', function ($invoice) {
                if ($invoice->status == INVOICE_STATUS_PAID) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Paid</div>';
                } elseif ($invoice->status == INVOICE_STATUS_OVER_DUE) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Due</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Pending</div>';
                }
            })
            ->addColumn('action', function ($invoice) {
                return '<div class="tbl-action-btns d-inline-flex">
                            <a href="#" data-updateurl="' . route('owner.invoice.update', $invoice->id) . '" class="p-1 tbl-action-btn edit" data-id="' . $invoice->id . '" data-detailsurl="' . route('owner.invoice.details', $invoice->id) . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></a>
                            <a href="#" class="p-1 tbl-action-btn" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></a>
                            <button onclick="deleteItem(\'' . route('owner.invoice.destroy', $invoice->id) . '\', \'overdueInvoiceDatatable\')" class="p-1 tbl-action-btn" title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>
                        </div>';
            })
            ->rawColumns(['invoice', 'property', 'status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $tenant = Tenant::where('owner_user_id', auth()->id())->where('unit_id', $request->property_unit_id)->where('status', TENANT_STATUS_ACTIVE)->first();
            if (!$tenant) {
                throw new Exception('Tenant Not Found');
            }
            $id = $request->get('id', '');
            $invoiceExist = Invoice::query()
                ->where('property_id', $request->property_id)
                ->where('property_unit_id', $request->property_unit_id)
                ->where('owner_user_id', auth()->id())
                ->where('month', $request->month)
                ->whereYear('created_at', '=', date('Y'))
                ->where(function ($q) use ($id) {
                    if ($id != '') {
                        $q->whereNot('id', $id);
                    }
                })
                ->exists();
            if ($invoiceExist) {
                throw new Exception('Invoice Already Generate');
            }
            if ($id != '') {
                $invoice = Invoice::findOrFail($request->id);
            } else {
                if (!getOwnerLimit(RULES_INVOICE) > 0) {
                    throw new Exception('Your Invoice Limit finished');
                }
                $invoice = new Invoice();
            }
            $invoice->name = $request->name;
            $invoice->tenant_id = $tenant->id;
            $invoice->owner_user_id = auth()->id();
            $invoice->property_id = $request->property_id;
            $invoice->property_unit_id = $request->property_unit_id;
            $invoice->month = $request->month;
            $invoice->due_date = $request->due_date;
            $invoice->save();

            $totalAmount = 0;
            $totalTax = 0;
            $now = now();
            $tax = taxSetting(auth()->id());
            if (is_null($request->invoiceItem)) {
                throw new Exception('Add invoice item at least one');
            }
            if ($request->invoiceItem['invoice_type_id'] > 0) {
                for ($i = 0; $i < count($request->invoiceItem['invoice_type_id']); $i++) {
                    if ($request->invoiceItem['id'][$i]) {
                        $invoiceItem = InvoiceItem::findOrFail($request->invoiceItem['id'][$i]);
                    } else {
                        $invoiceItem = new InvoiceItem();
                    }
                    $invoiceItem->invoice_id = $invoice->id;
                    $invoiceItem->invoice_type_id = $request->invoiceItem['invoice_type_id'][$i];
                    $invoiceItem->amount = $request->invoiceItem['amount'][$i];
                    $invoiceItem->description = $request->invoiceItem['description'][$i];
                    $invoiceItem->updated_at = $now;
                    $invoiceType = InvoiceType::findOrFail($request->invoiceItem['invoice_type_id'][$i]);
                    if (isset($tax) && $tax->type == TAX_TYPE_PERCENTAGE) {
                        $invoiceItem->tax_amount = $invoiceItem->amount * $invoiceType->tax * 0.01;
                    } else {
                        $invoiceItem->tax_amount = $invoiceType->tax;
                    }
                    $invoiceItem->save();
                    $totalAmount += $invoiceItem->amount + $invoiceItem->tax_amount;
                    $totalTax += $invoiceItem->tax_amount;
                }
                InvoiceItem::where('invoice_id', $invoice->id)->where('updated_at', '!=', $now)->get()->map(function ($q) {
                    $q->delete();
                });
            }
            $invoice->amount = $totalAmount;
            $invoice->tax_amount = $totalTax;
            $invoice->save();
            $title = __("You have a new invoice");
            $body = __("Please check the invoice and response as soon as possible.");
            addNotification($title, $body, null, null, $tenant->user_id, auth()->user()->id);

            DB::commit();
            $message = $request->id ? UPDATED_SUCCESSFULLY : CREATED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function sendSingleNotification($request)
    {
        try {
            $invoice = Invoice::where('owner_user_id', auth()->id())->findOrFail($request->invoice_id);
            addNotification($request->title, $request->body, null, null, $invoice->tenant->user_id, auth()->id());
            $message = "Notification Sent Successfully";
            return $this->success([], $message);
        } catch (\Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function sendMultiNotification($request)
    {
        try {
            $tenants = Tenant::query()
                ->where('owner_user_id', auth()->id())
                ->where('status', TENANT_STATUS_ACTIVE)
                ->when($request->property_id, function ($q) use ($request) {
                    $q->where('property_id', $request->property_id);
                })
                ->when($request->unit_id, function ($q) use ($request) {
                    $q->where('unit_id', $request->unit_id);
                })
                ->select('user_id')
                ->get();
            foreach ($tenants as $tenant) {
                addNotification($request->title, $request->body, null, null, $tenant->user_id, auth()->id());
            }
            $message = "Notification Sent Successfully";
            return $this->success([], $message);
        } catch (\Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::where('owner_user_id', auth()->id())->findOrFail($id);
            $invoice->delete();
            DB::commit();
            $message = DELETED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function types()
    {
        return InvoiceType::where('owner_user_id', auth()->id())->all();
    }

    public function getById($id)
    {
        if (auth()->user()->role == USER_ROLE_OWNER) {
            $userId = auth()->id();
        } else {
            $userId = auth()->user()->owner_user_id;
        }
        return Invoice::where('owner_user_id', $userId)->findOrFail($id);
    }

    public function getByIdCheckTenantAuthId($id)
    {
        return Invoice::query()
            ->where('owner_user_id', auth()->user()->owner_user_id)
            ->where('tenant_id', auth()->user()->tenant->id)
            ->findOrFail($id);
    }

    public function getItemsByInvoiceId($id)
    {
        return InvoiceItem::where('invoice_id', $id)->get();
    }

    public function getByTenantId($id)
    {
        return Invoice::where('owner_user_id', auth()->user()->owner_user_id)->where('tenant_id', $id)->get();
    }

    public function getOrderById($id)
    {
        return Order::query()
            ->leftJoin('gateways', 'orders.gateway_id', '=', 'gateways.id')
            ->select(['orders.*', 'gateways.title as gatewayTitle'])
            ->where('orders.payment_status', INVOICE_STATUS_PAID)
            ->where('orders.id', $id)
            ->first();
    }

    public function getCurrencyByGatewayId($id)
    {
        $currencies = GatewayCurrency::where('owner_user_id', auth()->user()->owner_user_id)->where('gateway_id', $id)->get();
        foreach ($currencies as $currency) {
            $currency->symbol =  $currency->symbol;
        }
        return $currencies;
    }

    public function paymentStatusChange($request)
    {
        DB::beginTransaction();
        try {

            if ($request->status == INVOICE_STATUS_PAID) {
                $invoice = Invoice::where('owner_user_id', auth()->id())->findOrFail($request->id);
                $order = Order::find($invoice->order_id);
                if (is_null($order)) {
                    $order = Order::create([
                        'user_id' => $invoice->tenant->user->id,
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'system_currency' => Currency::where('current_currency', 'on')->first()->currency_code,
                        'conversion_rate' => 1,
                        'subtotal' => $invoice->amount,
                        'total' => $invoice->amount,
                        'transaction_amount' => $invoice->amount * 1,
                        'payment_status' => INVOICE_STATUS_PENDING,
                        'bank_id' => null,
                        'bank_name' => null,
                        'bank_account_number' => null,
                        'deposit_by' => null,
                        'deposit_slip_id' => null
                    ]);
                }

                $order->payment_status = INVOICE_STATUS_PAID;
                $order->transaction_id = str_replace("-", "", uuid_create(UUID_TYPE_RANDOM));
                $order->save();

                $invoice->order_id = $order->id;
                $invoice->status = INVOICE_STATUS_PAID;
                $invoice->save();
            }
            DB::commit();
            $message = __(UPDATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }
}
