<?php

use MongoDB\BSON\ObjectId;

// HAPUS PELANGGAN
if (isset($_GET['hapus_pelanggan'])) {
    try {
        $idPelanggan = $_GET['hapus_pelanggan'];

        if (!preg_match('/^[0-9a-f]{24}$/i', $idPelanggan)) {
            throw new Exception("ID tidak valid");
        }

        $deleteResult = $db->user->deleteOne([
            '_id' => new ObjectId($idPelanggan)
        ]);

        if ($deleteResult->getDeletedCount() > 0) {
            header("Location: index.php?i=plg&status=deleted");
        } else {
            header("Location: index.php?i=plg&status=notfound");
        }
        exit;
    } catch (Exception $e) {
        header("Location: index.php?i=plg&status=error");
        exit;
    }
}

// HAPUS PRODUK
if (isset($_GET['hapus_produk'])) {
    try {
        $idProduk = $_GET['hapus_produk'];

        if (!preg_match('/^[0-9a-f]{24}$/i', $idProduk)) {
            throw new Exception("ID tidak valid");
        }

        $produk = $db->produk->findOne(
            ['_id' => new ObjectId($idProduk)],
            ['projection' => ['prod_gambar' => 1]]
        );

        $deleteResult = $db->produk->deleteOne(['_id' => new ObjectId($idProduk)]);

        if ($deleteResult->getDeletedCount() > 0) {
            if (!empty($produk['prod_gambar'])) {
                $deleteUrl = "https://img.nafisahcake.store/delete.php";
                $fileName  = $produk['prod_gambar']; // hanya nama file

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $deleteUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $fileName]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }

            $db->keranjang->deleteMany(['prod_id' => new ObjectId($idProduk)]);
            header("Location: index.php?i=prod&status=deleted");
        } else {
            header("Location: index.php?i=prod&status=notfound");
        }
        exit;
    } catch (Exception $e) {
        header("Location: index.php?i=prod&status=error");
        exit;
    }
}


// TAMBAH PRODUK
if (isset($_GET['tambah_produk']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $kode   = trim($_POST['prod_kode']);
        $nama   = trim($_POST['prod_nama']);
        $desk   = trim($_POST['prod_deskripsi']);
        $harga  = intval($_POST['prod_harga']);
        $stok   = intval($_POST['prod_stok']);
        $gambar = "";

        $existing = $db->produk->findOne(['prod_kode' => $kode]);
        if ($existing) {
            header("Location: index.php?i=prod&status=kode_exist");
            exit;
        }

        if (!empty($_FILES['prod_gambar']['name'])) {
            $uploadUrl = "https://img.nafisahcake.store/upload.php";
            $cfile = new CURLFile(
                $_FILES['prod_gambar']['tmp_name'],
                $_FILES['prod_gambar']['type'],
                $_FILES['prod_gambar']['name']
            );
            $data = ["file" => $cfile];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if ($result && $result['status'] === "success") {
                $gambar = $result['filename']; // hanya nama file
            }
        }

        $voucherId = !empty($_POST['id_voucher']) ? new ObjectId($_POST['id_voucher']) : null;

        $insertData = [
            'prod_kode'      => $kode,
            'prod_nama'      => $nama,
            'prod_deskripsi' => $desk,
            'prod_harga'     => $harga,
            'prod_stok'      => (int)$stok,
            'prod_gambar'    => $gambar,
        ];

        if ($voucherId) {
            $insertData['id_voucher'] = $voucherId;
        }

        $db->produk->insertOne($insertData);

        header("Location: index.php?i=prod&status=added");
        exit;
    } catch (Exception $e) {
        header("Location: index.php?i=prod&status=error");
        exit;
    }
}


// EDIT PRODUK
if (isset($_GET['edit_produk']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $idProduk = $_POST['prod_id'];
        $voucherId = !empty($_POST['id_voucher']) ? new ObjectId($_POST['id_voucher']) : null;

        $harga = intval($_POST['prod_harga']);

        $updateData = [
            'prod_kode'      => $_POST['prod_kode'],
            'prod_nama'      => $_POST['prod_nama'],
            'prod_deskripsi' => $_POST['prod_deskripsi'],
            'prod_harga'     => $harga,
            'prod_stok'      => (int)$_POST['prod_stok'],
        ];

        if ($voucherId) {
            $updateData['id_voucher'] = $voucherId;
        } else {
            $updateData['id_voucher'] = null;
        }

        if (!empty($_FILES['prod_gambar']['name'])) {
            $produkLama = $db->produk->findOne(
                ['_id' => new ObjectId($idProduk)],
                ['projection' => ['prod_gambar' => 1]]
            );

            if (!empty($produkLama['prod_gambar'])) {
                $deleteUrl = "https://img.nafisahcake.store/delete.php";
                $fileName  = $produkLama['prod_gambar'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $deleteUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $fileName]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }

            $uploadUrl = "https://img.nafisahcake.store/upload.php";
            $cfile = new CURLFile(
                $_FILES['prod_gambar']['tmp_name'],
                $_FILES['prod_gambar']['type'],
                $_FILES['prod_gambar']['name']
            );
            $data = ["file" => $cfile];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if ($result && $result['status'] === "success") {
                $updateData['prod_gambar'] = $result['filename']; // simpan nama file
            }
        }

        $updateResult = $db->produk->updateOne(
            ['_id' => new ObjectId($idProduk)],
            ['$set' => $updateData]
        );

        if ($updateResult->getModifiedCount() > 0) {
            header("Location: index.php?i=prod&status=updated");
        } else {
            header("Location: index.php?i=prod&status=nochange");
        }
        exit;
    } catch (Exception $e) {
        header("Location: index.php?i=prod&status=error");
        exit;
    }
}

// UPDATE STATUS PESANAN
if (isset($_POST['ubah_status_id']) && isset($_POST['status_baru'])) {
    try {
        $id = new MongoDB\BSON\ObjectId($_POST['ubah_status_id']);
        $statusBaru = strtoupper(trim($_POST['status_baru']));

        // Validasi status
        $statusValid = ['MENUNGGU', 'PROSES', 'SEDANG DIKIRIM', 'SELESAI', 'BATAL'];
        if (!in_array($statusBaru, $statusValid)) {
            header("Location: index.php?i=trans&status=error");
            exit;
        }

        // Update status di koleksi transaksi
        $updateResult = $db->transaksi->updateOne(
            ['_id' => $id],
            ['$set' => ['paket_status' => $statusBaru]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            header("Location: index.php?i=trans&status=updated");
        } else {
            header("Location: index.php?i=trans&status=error");
        }
        exit;
    } catch (Exception $e) {
        header("Location: index.php?i=trans&status=error");
        exit;
    }
}

function getTotalCustomers($db)
{
    return $db->user->countDocuments(['user_role' => ['$ne' => 'admin']]);
}

function getTotalTransactions($db)
{
    return $db->transaksi->countDocuments([]);
}

function getTotalProducts($db)
{
    return $db->produk->countDocuments([]);
}

function getTotalRevenue($db)
{
    $pipeline = [
        ['$match' => ['paket_status' => 'SELESAI']], // filter hanya transaksi selesai
        ['$group' => [
            '_id' => null,
            'totalRevenue' => ['$sum' => '$trx_belanja']
        ]]
    ];

    $result = $db->transaksi->aggregate($pipeline);
    $revenue = 0;
    foreach ($result as $doc) {
        $revenue = $doc['totalRevenue'];
    }
    return $revenue;
}

function getReview($db)
{
    $pipeline = [
        // Urutkan berdasarkan tanggal terbaru
        ['$sort' => ['review_tgl' => -1]],
        // Batasi hanya 3 review
        ['$limit' => 3],
        // Join ke user
        [
            '$lookup' => [
                'from' => 'user',
                'localField' => 'id_plg',
                'foreignField' => '_id',
                'as' => 'user'
            ]
        ],
        // Ambil hanya satu user (karena id_plg pasti 1 user)
        ['$unwind' => '$user'],
        // Join ke produk
        [
            '$lookup' => [
                'from' => 'produk',
                'localField' => 'id_prod',
                'foreignField' => '_id',
                'as' => 'produk'
            ]
        ],
        // Ambil hanya satu produk
        ['$unwind' => '$produk'],
        // Proyeksikan field yang mau ditampilkan
        [
            '$project' => [
                'review_isi' => 1,
                'review_rating' => 1,
                'review_tgl' => 1,
                'plg_nama' => '$user.user_nama',
                'prod_nama' => '$produk.prod_nama'
            ]
        ]
    ];

    $result = $db->review->aggregate($pipeline);
    return iterator_to_array($result);
}

function getLatestTransactions($db)
{
    $pipeline = [
        ['$sort' => ['trx_tgl' => -1]],  // urutkan dari terbaru
        ['$limit' => 3],                 // ambil 3 transaksi
        [
            '$lookup' => [
                'from' => 'user',
                'localField' => 'plg_id',
                'foreignField' => '_id',
                'as' => 'user'
            ]
        ],
        ['$unwind' => ['path' => '$user', 'preserveNullAndEmptyArrays' => true]],
        [
            '$project' => [
                'orderid' => 1,
                'trx_tgl' => 1,
                'trx_belanja' => 1,
                'trx_ongkir' => 1,
                'trx_status' => 1,
                'paket_status' => 1,
                'items' => 1,
                'plg_nama' => '$user.user_nama'
            ]
        ]
    ];

    $result = $db->transaksi->aggregate($pipeline);
    return iterator_to_array($result);
}

// HAPUS KODE VOUCHER
function hapusVouch($id)
{
    global $db;

    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $db->voucher->deleteOne(["_id" => $id]);

    header("Location: index.php?i=vouch&status=deleted");
    exit;
}

// UBAH PASSWORD OWNER & ADMIN
function updateAdmin()
{
    global $db;
    $collection = $db->user;

    // ambil data dari form
    $user_uname = $_POST['user_uname'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Password tidak sama!'); window.history.back();</script>";
        exit;
    }

    // update password user
    $updateResult = $collection->updateOne(
        ['user_uname' => $user_uname],
        ['$set' => ['user_pwd' => $new_password]]
    );

    if ($updateResult->getModifiedCount() > 0) {
        echo "<script>alert('Password $user_uname berhasil diubah!'); window.history.back();</script>";
    } else {
        echo "<script>alert('Password $user_uname gagal diubah!'); window.history.back();</script>";
    }
}
