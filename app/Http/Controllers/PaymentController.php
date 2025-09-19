<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaid;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Square\Exceptions\SquareApiException;
use Square\SquareClient;
use App\Models\Invoice;
use App\Models\ClientAssignedPackage;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Square\Environments;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Types\Money;

use function PHPUnit\Framework\isEmpty;

class PaymentController extends Controller
{
    public function payWithPaypal(Request $request)
    {
        try {
            $validated = $request->validate([
                'orderId' => 'required|string',
                'invoiceId' => 'required|exists:invoices,id',
                'amount' => 'required|numeric',
                'details' => 'required',
                'tip' => 'nullable|numeric'
            ]);
            Log::info($validated);
            $paymentData = [
                'invoice_id' => $validated['invoiceId'],
                'price' => $validated['amount'],
                'tip' => number_format($validated['tip'] ?? 0),
                'transaction_id' => $validated['orderId'],
                'transaction_details' => json_encode($validated['details'])
            ];
            Payment::storePaymentData($paymentData, 'paypal');
            $this->updatePaymentStatusAndAssignment($validated);
            return ResponseTrait::success('Payment Processed Successfully', [
                'success' => 1
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('Payment processing failed', [
                'error' => $th->getMessage(),
            ]);
        }
    }
    public function payWithSquare(Request $request)
    {
        $validated = $request->validate([
            'sourceId' => 'required|string',
            'invoiceId' => 'required|exists:invoices,id',
            'amount' => 'required|numeric',
            'tip' => 'nullable|numeric',
            // 'assignedPackageId' => 'exists:client_assigned_packages,id'
        ]);
        $amount = (int) $validated['amount'] * 100;
        $square = new SquareClient(
            token: env('SQUARE_TOKEN'),
            options: [
                'baseUrl' =>  Environments::Production->value,
            ],
        );
        try {
            $response = $square->payments->create(
                request: new CreatePaymentRequest(
                    [
                        'idempotencyKey' => (string) Str::uuid(),
                        'sourceId' => $validated['sourceId'],
                        'amountMoney' => new Money([
                            'amount' => $amount,
                            'currency' => 'USD'
                        ]),
                        'locationId'  => env('SQUARE_LOCATION_ID'),
                    ],
                )
            );

            if ($response->getErrors() === null) {
                $payment = $response->getPayment();
                $paymentData = [
                    'invoice_id' => $validated['invoiceId'],
                    'price' => number_format($payment->getAmountMoney()->getAmount() / 100, 2),
                    'tip' => number_format($validated['tip'] ?? 0),
                    'transaction_id' => $payment->getId(),
                    'transaction_details' => $payment->jsonSerialize()
                ];

                Payment::storePaymentData($paymentData, 'square');
                $this->updatePaymentStatusAndAssignment($validated);
                return ResponseTrait::success('Payment Processed Successfully', [
                    'success' => $payment->getStatus()
                ]);
            }

            return ResponseTrait::error('Payment processing failed');
        } catch (SquareApiException $e) {
            return ResponseTrait::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'details' => $e->getErrors()
            ]);
        }
    }

    protected function updatePaymentStatusAndAssignment($data)
    {
        DB::beginTransaction();
        $invoice = Invoice::findOrFail($data['invoiceId']);
        $invoice->update(['status' => 1, 'tip' => $data['tip'] ?? 0]);
        $this->sendEmailToCustomerAndAdmins($invoice);
        if ($invoice->assigned_package_id != null) {
            $invoice->load('assignedPackage');
            $assignedPackage = ClientAssignedPackage::findOrFail($invoice->assignedPackage->id);
            $assignedPackage->update(['status' => 1]);
        }
        DB::commit();
    }

    public function sendEmailToCustomerAndAdmins($invoice)
    {
        try {
            // Email to Client
            Mail::to($invoice->client->email)
                ->send(new OrderPaid($invoice, true));

            // Email to agent and admins
            Mail::to($invoice->createdBy->email)
                ->cc(config('constants.emails'))
                ->send(new OrderPaid($invoice));
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while paying the invoice: ' . $th->getMessage());
        }
    }
}
