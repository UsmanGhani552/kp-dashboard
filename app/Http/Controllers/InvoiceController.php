<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client.packages', 'createdBy')->orderBy('created_at', 'desc')->get();
        return ResponseTrait::success('Invoices retrieved successfully', [
            'invoices' => $invoices,
        ]);
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $data = array_merge($request->validated(), [
                'user_id' => auth()->user()->id,
            ]);
            $invoice = Invoice::createInvoice($data);
            return ResponseTrait::success('Invoice created successfully', [
                'invoice' => $invoice,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the invoice: ' . $th->getMessage());
        }
    }
    public function update(UpdateInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $data = array_merge($request->validated(), [
                'user_id' => auth()->user()->id,
            ]);
            $invoice->updateInvoice($data);
            return ResponseTrait::success('Invoice created successfully', [
                'invoice' => $invoice,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the invoice: ' . $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->deleteInvoice();
            return ResponseTrait::success('Invoice deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the invoice: ' . $th->getMessage());
        }
    }

    public function getPaymentTypes()
    {
        try {
            $payment_types = PaymentType::all();
            return ResponseTrait::success('Payment types', [
                'payment_types' => $payment_types,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the invoice: ' . $th->getMessage());
        }
    }

    public function getPaymentHistory()
    {
        $client = auth()->user();
        $invoices = Invoice::where('client_id', $client->id)->get();
        return ResponseTrait::success('Payment types', [
            'payment_history' => $invoices,
        ]);
    }

    public function getInvoice($id)
    {
        $invoice = Invoice::with('client','package')->findOrFail($id);
        return ResponseTrait::success('true', [
            'invoice' => $invoice
        ]);
    }

    public function getInvoiceByAssignment($assignment_id)
    {
        try {
            $invoice = Invoice::where('assigned_package_id', $assignment_id)->with('client', 'package')->firstOrFail();
            return ResponseTrait::success('true', [
                'invoice' => $invoice
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the invoice: ' . $th->getMessage());
        }
    }
}
