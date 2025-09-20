<?php
require_once "functions/Sessions.php";
require_once "functions/Template.php";

date_default_timezone_set('Asia/Jakarta');
session_start();

$userid = new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']);
$penerima = $_POST['penerima'];
$nohp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
$kecamatan = $_POST['kec_hd'];
$kelurahan = $_POST['kel_hd'];
$kodepos = $_POST['kodepos'];
$orderid = $_POST['orderid'];
$total = $_POST['total'];
$totaldiskon = $_POST['total_diskon'];
$pointerpakai = $_POST['pointerpakai'];
$ongkir = $_POST['ongkir_hd'];

global $db;
$cart = iterator_to_array($db->keranjang->find(['plg_id' => $userid]));
$items = [];

foreach ($cart as $c) {
    $prod = $db->produk->findOne([
        "_id" => new MongoDB\BSON\ObjectId($c['prod_id'])
    ]);

    if ($prod) {
        $harga = $prod['prod_harga'];
        $subtotal = $c['qty'] * $harga;

        $items[] = [
            "id_prod" => $prod['_id'],
            "nama_prod" => $prod['prod_nama'],
            "qty" => $c['qty'],
            "harga" => (int)$harga,
            "subtotal" => (int)$subtotal,
            "review" => false,
        ];
    }
}

$trx = CommitTransaksi(
    $orderid,
    $userid,
    date('Y-m-d H:i', strtotime('NOW')),
    $penerima,
    ($alamat . ', ' . $kecamatan . ', ' . $kelurahan . ', ' . $kodepos),
    $nohp,
    $total,
    $pointerpakai,
    $totaldiskon,
    $ongkir,
    "PENDING",
    $items,
);

$commit = $db->transaksi->insertOne($trx);
$poin = round($total / 100 * 3);

if ($commit->getInsertedCount() > 0) {
    $db->user->updateOne(
        ['_id' => $userid],
        ['$inc' => ['plg_poin' => -$pointerpakai]]
    );
    foreach ($items as $item) {
        $db->produk->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($item['id_prod'])],
            ['$inc' => ['prod_stok' => -$item['qty']]] // Mengurangi stok
        );
    }
    // $db->keranjang->deleteMany(['plg_id' => $userid]);
?>
    <script>
        window.close();
    </script>
<?php
    // }
}
