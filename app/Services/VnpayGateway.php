<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use Illuminate\Support\Facades\URL;

class VnpayGateway implements PaymentGateway
{
    public function createPaymentUrl(Order $order): string
    {
        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => config('services.vnpay.tmn_code'),
            'vnp_Amount' => $order->total * 100,
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => request()->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan '.$order->order_number,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => route('payment.vnpay.return'),
            'vnp_TxnRef' => $order->order_number,
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];
        ksort($params);
        $query = http_build_query($params);
        $hash = hash_hmac('sha512', $query, (string) config('services.vnpay.hash_secret'));

        return config('services.vnpay.url').'?'.$query.'&vnp_SecureHash='.$hash;
    }

    public function verify(array $payload): bool
    {
        $provided = (string) ($payload['vnp_SecureHash'] ?? '');
        $data = collect($payload)->except(['vnp_SecureHash', 'vnp_SecureHashType'])->all();
        ksort($data);
        $expected = hash_hmac('sha512', http_build_query($data), (string) config('services.vnpay.hash_secret'));

        return hash_equals($expected, $provided);
    }

    public function transactionId(array $payload): string
    {
        return (string) ($payload['vnp_TransactionNo'] ?? $payload['vnp_TxnRef'] ?? '');
    }

    public function isSuccessful(array $payload): bool
    {
        return ($payload['vnp_ResponseCode'] ?? '') === '00' && ($payload['vnp_TransactionStatus'] ?? '') === '00';
    }

    public function amount(array $payload): int
    {
        return (int) (($payload['vnp_Amount'] ?? 0) / 100);
    }

    public function callbackUrl(): string
    {
        return URL::route('payment.vnpay.ipn');
    }
}
