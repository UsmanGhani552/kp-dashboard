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
use Square\Environments;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Types\Money;

class PaymentController extends Controller
{
    public function payWithSquare(Request $request)
    {
        $validated = $request->validate([
            'sourceId' => 'required|string',
            'invoiceId' => 'required|exists:invoices,id',
            'amount' => 'required|numeric',
            // 'assignedPackageId' => 'exists:client_assigned_packages,id'
        ]);
        $amount = (int) $validated['amount'] * 100;
        $square = new SquareClient(
            token: env('SQUARE_TOKEN'),
            options: [
                'baseUrl' =>  Environments::Sandbox->value,
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
                    // 'assigned_package_id' => $validated['assignedPackageId'] ?? null,
                    'price' => number_format($payment->getAmountMoney()->getAmount() / 100, 2),
                    'transaction_id' => $payment->getId(),
                    'transaction_details' => $payment->jsonSerialize()
                ];

                Payment::storePaymentData($paymentData, 'square');
                $this->updatePaymentStatusAndAssignment($validated);
                return ResponseTrait::success('Payment Processed Successfully', [
                    'status' => $payment->getStatus()
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
        $invoice = Invoice::findOrFail($data['invoiceId']);
        $invoice->update(['status' => 1]);
        $this->sendEmailToCustomerAndAdmins($invoice);
        $assignedPackage = ClientAssignedPackage::findOrFail($invoice->assignedPackage->id);
        $assignedPackage->update(['status' => 1]);
    }

    public function sendEmailToCustomerAndAdmins($invoice)
    {
        // Email to Client
        Mail::to($invoice->client->email)
            ->send(new OrderPaid($invoice, true));

        // Email to agent and admins
        Mail::to($invoice->createdBy->email)
            ->cc(config('constants.emails'))
            ->send(new OrderPaid($invoice));
    }
}
