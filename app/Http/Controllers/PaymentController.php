<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function vnpayReturn(Request $request, PaymentGateway $gateway): RedirectResponse
    {
        $payload = $request->query();
        $order = Order::query()->where('order_number', $payload['vnp_TxnRef'] ?? '')->firstOrFail();
        $this->apply($order, $payload, $gateway);

        return redirect()->route('orders.show', $order)->with('success', $order->fresh()->payment_status->label());
    }

    public function vnpayIpn(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $payload = $request->query();
        $order = Order::query()->where('order_number', $payload['vnp_TxnRef'] ?? '')->first();
        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }
        if (! $gateway->verify($payload)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }
        if ($gateway->amount($payload) !== $order->total) {
            return response()->json(['RspCode' => '04', 'Message' => 'Amount mismatch']);
        }
        $this->apply($order, $payload, $gateway);

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    private function apply(Order $order, array $payload, PaymentGateway $gateway): void
    {
        if (! $gateway->verify($payload) || $gateway->amount($payload) !== $order->total) {
            return;
        }
        DB::transaction(function () use ($order, $payload, $gateway): void {
            $payment = $order->payments()->lockForUpdate()->where('method', 'vnpay')->latest('id')->firstOrFail();
            if ($payment->status === PaymentStatus::Paid) {
                return;
            }
            $status = $gateway->isSuccessful($payload) ? PaymentStatus::Paid : PaymentStatus::Failed;
            $payment->update([
                'status' => $status,
                'transaction_id' => $gateway->transactionId($payload),
                'gateway_response' => $payload,
                'paid_at' => $status === PaymentStatus::Paid ? now() : null,
            ]);
            $order->update([
                'payment_status' => $status,
                'status' => $status === PaymentStatus::Paid ? OrderStatus::Confirmed : $order->status,
            ]);
        });
    }
}
