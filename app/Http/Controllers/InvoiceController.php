<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client.packages')->get();
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
    public function update(UpdateInvoiceRequest $request,$id)
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

    public function delete($id) {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->deleteInvoice();
            return ResponseTrait::success('Invoice deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the invoice: ' . $th->getMessage());
        }
    }
}
