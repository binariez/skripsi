<?php
require_once __DIR__ . '/../../vendor/autoload.php';

\Midtrans\Config::$serverKey = $_ENV['MIDTRANS'];
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$params = array(
    'transaction_details' => array(
        'order_id' => $_POST['orderid'],
        'gross_amount' => $_POST['total_bayar'],
    ),
    'customer_details' => array(
        'shipping_address' => array(
            'first_name' => $_POST['penerima'],
            'phone' => $_POST['no_hp'],
            'address' => "$_POST[alamat], $_POST[kecamatan], $_POST[kelurahan]",
            'city' => "Asahan",
            'postal_code' => $_POST['kodepos'],
        )
    )
);

$snapToken = \Midtrans\Snap::getSnapToken($params);
echo $snapToken;
