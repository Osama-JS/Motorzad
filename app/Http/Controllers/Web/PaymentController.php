<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HyperpayTransaction;
use App\Services\HyperPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected HyperPayService $hyperPayService
    ) {}

    /**
     * Dedicated, standalone checkout page for both Web and Mobile App WebView.
     */
    public function checkout(Request $request, $transactionId)
    {
        $transaction = HyperpayTransaction::with('user')->findOrFail($transactionId);

        // Security check: verify token or authenticated user session
        $expectedToken = hash_hmac('sha256', $transaction->id . $transaction->merchant_transaction_id, config('app.key'));
        $providedToken = (string) $request->query('token', '');
        $source = $request->query('source', 'web');

        $isAuthorized = hash_equals($expectedToken, $providedToken) || (auth()->check() && auth()->id() === $transaction->user_id);
        if (!$isAuthorized) {
            abort(403, __('Unauthorized payment session access.'));
        }

        // If already paid, redirect straight to success
        if ($transaction->status === 'paid') {
            return redirect()->route('payments.success', [
                'status'         => 'success',
                'transaction_id' => $transaction->id,
                'merchant_id'    => $transaction->merchant_transaction_id,
                'amount'         => $transaction->amount,
                'source'         => $source,
                'token'          => $expectedToken,
            ]);
        }

        $widgetBrands = $this->hyperPayService->getWidgetBrands($transaction->brand);
        $scriptUrl = rtrim($this->hyperPayService->getBaseUrl(), '/') . "/v1/paymentWidgets.js?checkoutId={$transaction->checkout_id}";
        $returnUrl = route('payments.callback', [
            'token'  => $expectedToken,
            'source' => $source,
        ]);

        return view('payments.checkout', [
            'transaction'  => $transaction,
            'widgetBrands' => $widgetBrands,
            'scriptUrl'    => $scriptUrl,
            'returnUrl'    => $returnUrl,
            'source'       => $source,
            'token'        => $expectedToken,
        ]);
    }

    /**
     * HyperPay callback endpoint (return URL after 3D Secure / OTP authentication).
     */
    public function callback(Request $request)
    {
        $checkoutId = $request->query('id');
        $source = $request->query('source', 'web');
        $providedToken = (string) $request->query('token', '');

        if (!$checkoutId) {
            return redirect()->route('payments.failure', [
                'status'  => 'failed',
                'message' => __('Invalid gateway reference returned.'),
                'source'  => $source,
            ]);
        }

        $transaction = HyperpayTransaction::where('checkout_id', $checkoutId)->first();
        if (!$transaction) {
            return redirect()->route('payments.failure', [
                'status'  => 'failed',
                'message' => __('Transaction record not found.'),
                'source'  => $source,
            ]);
        }

        $expectedToken = hash_hmac('sha256', $transaction->id . $transaction->merchant_transaction_id, config('app.key'));
        if (!empty($providedToken) && !hash_equals($expectedToken, $providedToken)) {
            abort(403, __('Unauthorized callback token verification.'));
        }

        try {
            $paymentData = $this->hyperPayService->verifyPayment($checkoutId, $transaction->brand);
            $resultCode = $paymentData['result']['code'] ?? '';

            if ($this->hyperPayService->isSuccessCode($resultCode)) {
                $this->hyperPayService->processSuccessfulPayment($transaction, $paymentData);

                return redirect()->route('payments.success', [
                    'status'         => 'success',
                    'transaction_id' => $transaction->id,
                    'merchant_id'    => $transaction->merchant_transaction_id,
                    'amount'         => $transaction->amount,
                    'source'         => $source,
                    'token'          => $expectedToken,
                ]);
            } else {
                $this->hyperPayService->markAsFailed($transaction, $paymentData);
                $errorDesc = $paymentData['result']['description'] ?? __('The payment could not be processed by your card issuer.');

                return redirect()->route('payments.failure', [
                    'status'         => 'failed',
                    'transaction_id' => $transaction->id,
                    'message'        => $errorDesc,
                    'source'         => $source,
                    'token'          => $expectedToken,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('HyperPay Web Callback Error: ' . $e->getMessage());

            return redirect()->route('payments.failure', [
                'status'         => 'failed',
                'transaction_id' => $transaction->id,
                'message'        => $e->getMessage(),
                'source'         => $source,
                'token'          => $expectedToken,
            ]);
        }
    }

    /**
     * Dedicated payment success page.
     */
    public function success(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $source = $request->query('source', 'web');
        $transaction = null;

        if ($transactionId) {
            $transaction = HyperpayTransaction::find($transactionId);
        }

        return view('payments.success', [
            'transaction' => $transaction,
            'amount'      => $request->query('amount', $transaction?->amount ?? 0),
            'merchantId'  => $request->query('merchant_id', $transaction?->merchant_transaction_id ?? ''),
            'source'      => $source,
            'brand'       => $transaction?->brand ?? 'mada',
        ]);
    }

    /**
     * Dedicated payment failure page.
     */
    public function failure(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $source = $request->query('source', 'web');
        $message = $request->query('message', __('تعذر إتمام عملية الدفع. يرجى التأكد من بيانات البطاقة أو المحاولة لاحقاً.'));
        $transaction = null;

        if ($transactionId) {
            $transaction = HyperpayTransaction::find($transactionId);
        }

        $retryUrl = null;
        if ($transaction) {
            $token = hash_hmac('sha256', $transaction->id . $transaction->merchant_transaction_id, config('app.key'));
            $retryUrl = route('payments.checkout', [
                'transaction' => $transaction->id,
                'token'       => $token,
                'source'      => $source,
            ]);
        }

        return view('payments.failure', [
            'transaction' => $transaction,
            'message'     => $message,
            'source'      => $source,
            'retryUrl'    => $retryUrl,
        ]);
    }
}
