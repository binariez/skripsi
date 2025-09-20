<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/district/363",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "key: 7123dee0806a9765ab599efde8006a56"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $array_response = json_decode($response, true);
    if (isset($array_response['data'])) {
        echo "<option selected disabled>--Pilih Kecamatan--</option>";
        foreach ($array_response['data'] as $key => $value) {
            echo "<option value='" . $value['id'] . "'>";
            echo $value["name"];
            echo "</option>";
        }
    }
}
