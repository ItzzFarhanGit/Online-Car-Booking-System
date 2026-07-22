<?php
/**
 * PayHere payment gateway helper functions.
 * Docs: https://support.payhere.lk/api-&-mobile-sdk/checkout-api
 */

function payhere_checkout_url() {
    return PAYHERE_SANDBOX ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout';
}

/**
 * Generates the hash PayHere requires on the checkout form.
 */
function payhere_generate_hash($order_id, $amount, $currency = 'LKR') {
    $amount_formatted = number_format((float) $amount, 2, '.', '');
    return strtoupper(
        md5(
            PAYHERE_MERCHANT_ID .
            $order_id .
            $amount_formatted .
            $currency .
            strtoupper(md5(PAYHERE_MERCHANT_SECRET))
        )
    );
}

/**
 * Verifies the md5sig sent by PayHere on the notify_url callback.
 */
function payhere_verify_signature($merchant_id, $order_id, $payhere_amount, $payhere_currency, $status_code, $md5sig) {
    $local_sig = strtoupper(
        md5(
            $merchant_id .
            $order_id .
            $payhere_amount .
            $payhere_currency .
            $status_code .
            strtoupper(md5(PAYHERE_MERCHANT_SECRET))
        )
    );
    return hash_equals($local_sig, strtoupper($md5sig));
}

/**
 * Generates a unique order id for a booking, e.g. CB-20260722-000123-4821
 */
function payhere_generate_order_id($booking_id) {
    return 'CB-' . date('Ymd') . '-' . str_pad((string) $booking_id, 6, '0', STR_PAD_LEFT) . '-' . random_int(1000, 9999);
}
