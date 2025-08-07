<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Mail\OrderCreated;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client.packages', 'createdBy', 'brand')->orderBy('id', 'desc')->get();
        if(count($invoices) > 0) {
            $grouped = $invoices->groupBy('client_id')->map(function ($group) {
                return $group->sum('price');
            })->sortDesc();
            $topClientId = $grouped->keys()->first();
            $topClientName = optional($invoices->firstWhere('client_id', $topClientId)?->client)->name;
            $topClientTotal = $grouped->first();
            $invoices[0]['top_client'] = [
                'id' => $topClientId,
                'name' => $topClientName,
                'amount' => $topClientTotal,
            ];
        }
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
            $this->sendEmailToCustomerAndAdmins($invoice);
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
            return ResponseTrait::success('Invoice updated successfully', [
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
        $invoice = Invoice::with('client', 'package', 'brand')->findOrFail($id);
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

    public function sendEmailToCustomerAndAdmins($invoice)
    {
        // Email to Client
        Mail::to($invoice->client->email)
            ->send(new OrderCreated($invoice));

        // Email to agent and admins
        Mail::to($invoice->createdBy->email)
            ->cc(config('constants.emails'))
            ->send(new OrderCreated($invoice));
    }
}
