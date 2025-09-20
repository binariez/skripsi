<?php
function User($uname, $pwd, $role, $pfp, $nama, $jk, $almt, $hp, $poin)
{
    $arr = array(
        "user_uname" => $uname,
        "user_pwd" => $pwd,
        "user_role" => $role,
        "user_pfp" => $pfp,
        "user_nama" => $nama,
        "plg_jk" => $jk,
        "plg_alamat" => $almt,
        "plg_hp" => $hp,
        "plg_poin" => $poin,
        "is_verified" => false,
    );
    return $arr;
}

function Produk($prod_kode, $prod_nama, $prod_deskripsi, $prod_harga, $prod_stok, $prod_gambar)
{
    $arr = array(
        "prod_kode"       => $prod_kode,
        "prod_nama"       => $prod_nama,
        "prod_deskripsi"  => $prod_deskripsi,
        "prod_harga"      => $prod_harga,
        "prod_stok"       => $prod_stok,
        "prod_gambar"     => $prod_gambar,
    );
    return $arr;
}

function Keranjang($userid, $prod_id, $qty)
{
    $userid_obj = new MongoDB\BSON\ObjectId($userid);
    $arr = array(
        "plg_id"      => $userid_obj,
        "prod_id"     => $prod_id,
        "qty"         => $qty,
    );
    return $arr;
}

function CommitTransaksi($orderid, $userid, $tanggal, $penerima, $alamat, $nohp, $total, $pointerpakai, $totaldiskon, $ongkir, $status, $items)
{
    $userid_obj = new MongoDB\BSON\ObjectId($userid);

    $arr = array(
        "orderid" => $orderid,
        "plg_id" => $userid_obj,
        "trx_tgl" => $tanggal,
        "trx_belanja" => (int)$total,
        "trx_bldiskon" => (int)$totaldiskon,
        "trx_pointerpakai" => (int)$pointerpakai,
        "trx_ongkir" => (int)$ongkir,
        "trx_alamat" => $alamat,
        "trx_hp" => $nohp,
        "trx_penerima" => $penerima,
        "trx_status" => $status,
        "paket_status" => "MENUNGGU",
        "paket_kirim" => "",
        "paket_selesai" => "",
        "items" => $items,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    );
    return $arr;
}
