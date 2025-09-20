<?php

$data['origin'] = 41294;
$data['destination'] = $_POST['subdistrict_id'];
$data['weight'] = 1000;
$data['courier'] = "jne";
$data['price'] = "lowest";

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "key: 7123dee0806a9765ab599efde8006a56"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #: " . $err;
} else {
    $array_response = json_decode($response, true);
    if (isset($array_response['data'])) {
        foreach ($array_response['data'] as $key => $value) {
            echo round($value['cost'] / 1.16, 0);
            break;
        }
    }
}
