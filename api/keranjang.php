<?php
require_once __DIR__ . '/functions/Sessions.php';
require_once __DIR__ . '/functions/Template.php';
session_start();

// tambah produk ke keranjang
if (isset($_POST['tambah'])) {
    // jika produk sudah ada di keranjang
    $duplikat = $db->keranjang->findOne([
        'plg_id' => new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']),
        'prod_id' => new MongoDB\BSON\ObjectId($_POST['prod_id']),
    ]);
    if ($duplikat) {
        echo "<script>alert('Produk sudah ada di keranjang!'); window.location.href='keranjang.php';</script>";
        exit;
    }
    // jika belum
    else {
        $plg_id  = new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']);
        $prod_id = new MongoDB\BSON\ObjectId($_POST['prod_id']);
        $qty = 1;

        $keranjang = Keranjang($plg_id, $prod_id, $qty);
        global $db;
        $db->keranjang->insertOne($keranjang);
    }
    echo "<script>window.location.href='keranjang.php'</script>";
}

// update qty
if (isset($_POST['update_qty'])) {
    $prod_id = $_POST['prod_id'];
    $qty = (int) $_POST['qty'];

    if (isset($_SESSION['UserLogin'])) {
        global $db;
        $db->keranjang->updateOne(
            [
                'plg_id' => new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']),
                'prod_id' => new MongoDB\BSON\ObjectId($prod_id),
            ],
            [
                '$set' => ['qty' => $qty]
            ]
        );
    }
    echo "<script>window.location.href='keranjang.php'</script>";
}

// hapus dari keranjang
if (isset($_POST['hapus'])) {
    $plg_id = new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']);
    $prod_id = $_POST['prod_id'];
    $filter = [
        "plg_id" => new MongoDB\BSON\ObjectId($plg_id),
        "prod_id" => new MongoDB\BSON\ObjectId($prod_id),
    ];
    $db->keranjang->deleteOne($filter);
    echo "<script>window.location.href='keranjang.php'</script>";
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keranjang</title>
    <link rel="icon" href="../public/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,600&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-vWOP5H5Iip1VX3RO"></script>
    <style>
        body {
            font-family: "Poppins";
        }
    </style>
</head>

<body>
    <div class="flex min-h-screen flex-col">
        <?php include_once "pages/nav.php" ?>
        <main class="flex-grow pt-10 mx-10">

            <h1 class="mb-5 text-center text-2xl font-bold">Keranjang</h1>
            <hr class="mx-2 w-full mb-5">

            <!-- body keranjang -->
            <?php if (!isset($_SESSION['UserLogin'])) {
                include_once "pages/keranjang_anon.php";
            } else {
                include_once "pages/keranjang_body.php";
            }
            ?>

        </main>
        <?php include_once "pages/footer.php" ?>
    </div>

    <!-- API ONGKIR -->
    <script>
        $(document).ready(function() {
            // Nonaktifkan autocomplete
            $('input[name="no_hp"]').attr('autocomplete', 'none');

            // Ambil total poin pelanggan dari PHP
            const poinPelanggan = <?= $dataplg->plg_poin ?>;

            // Format angka ke IDR
            function formatIDR(angka) {
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR"
                }).format(angka);
            }

            // Tombol checkout
            $(document).on("click", ".btn_checkout", function(e) {
                e.preventDefault();

                // Tampilkan total awal
                $("input[name=total_vis]").val(formatIDR(parseInt($("input[name=total]").val())));

                // Load kecamatan
                $.ajax({
                    type: "post",
                    data: "kecamatan",
                    url: "pembayaran/data_kecamatan.php",
                    success: function(hasil) {
                        $("select[name=kecamatan]").html(hasil);
                    }
                });
            });

            // Pilih kecamatan -> load kelurahan
            $("select[name=kecamatan]").on("change", function() {
                var district_id = $("option:selected", this).val();
                var kecamatanOpts = $("option:selected", this).text();
                $("input[name=kec_hd]").val(kecamatanOpts);

                $.ajax({
                    type: "post",
                    data: "district_id=" + district_id,
                    url: "pembayaran/data_kelurahan.php",
                    success: function(hasil) {
                        $("select[name=kelurahan]").html(hasil);
                    }
                });
            });

            // Pilih kelurahan -> hitung ongkir
            $("select[name=kelurahan]").on("change", function() {
                var subdistrict_id = $("option:selected", this).val();
                var kelurahanOpts = $("option:selected", this).text();
                $("input[name=kel_hd]").val(kelurahanOpts);

                $.ajax({
                    url: "pembayaran/hitung_ongkir.php",
                    type: "post",
                    data: "subdistrict_id=" + subdistrict_id,
                    success: function(ongkir) {
                        ongkir = parseInt(ongkir) || 0;
                        $("input[name=ongkir_hd]").val(ongkir);
                        $("input[name=ongkir]").val(formatIDR(ongkir));
                        updateTotalBayar();
                    }
                });
            });

            // Tombol pakai poin
            $("button[name='pakaipoin']").on("click", function() {
                let totalBelanja = parseInt($("input[name='total']").val()) || 0;

                // Maksimum poin yang bisa dipakai = 50% dari total belanja
                let maxPoinBolehDipakai = Math.round(totalBelanja * 0.5);

                // Poin terpakai = minimum antara poin pelanggan dan batas 50%
                let poinTerpakai = Math.min(poinPelanggan, maxPoinBolehDipakai);

                // Pastikan bilangan bulat
                poinTerpakai = Math.floor(poinTerpakai);

                // Simpan poin yang terpakai di tombol agar bisa dicek di updateTotalBayar
                $(this).data("poin-terpakai", poinTerpakai)
                    .prop("disabled", true)
                    .text(`Poin Terpakai: ${poinTerpakai}`);

                $("input[name='pointerpakai']").val(poinTerpakai);

                updateTotalBayar();
            });

            // Update total bayar
            function updateTotalBayar() {
                let totalBelanja = parseInt($("input[name='total']").val()) || 0;
                let ongkir = parseInt($("input[name='ongkir_hd']").val()) || 0;

                // Cek tombol poin
                let tombolPoin = $("button[name='pakaipoin']");
                let poinTerpakai = tombolPoin.prop("disabled") ? tombolPoin.data("poin-terpakai") || 0 : 0;

                let totalSetelahDiskon = totalBelanja - poinTerpakai;
                if (totalSetelahDiskon < 0) totalSetelahDiskon = 0;
                let totalBayar = totalSetelahDiskon + ongkir;

                // Update tampilan
                $("input[name=total_vis]").val(formatIDR(totalSetelahDiskon)); // total belanja dikurangi poin
                $("input[name=total]").val(totalBelanja); // total belanja dikurangi poin untuk backend
                $("input[name=total_diskon]").val(totalSetelahDiskon); // total belanja dikurangi poin untuk backend
                $("input[name=total_bayar_hd]").val(totalBayar); // total bayar untuk backend
                $("input[name=total_bayar]").val(formatIDR(totalBayar)); // total bayar tampil di frontend
            }


            // Input ongkir manual (opsional)
            $("input[name='ongkir']").on("input", function() {
                let raw = $(this).val().replace(/[^\d]/g, '');
                $("input[name=ongkir_hd]").val(raw);
                $(this).val(formatIDR(raw));
                updateTotalBayar();
            });

            // Submit transaksi
            $("input[name=bayar]").on("click", function(e) {
                e.preventDefault();

                var penerima = $("input[name='penerima']").val().trim();
                var no_hp = $("input[name='no_hp']").val().trim();
                var kecamatanOpt = $("select[name='kecamatan'] option:selected").text();
                var kelurahanOpt = $("select[name='kelurahan'] option:selected").text();
                var alamat = $("input[name='alamat']").val().trim();
                var kodepos = $("input[name='kodepos']").val();
                var ongkirhd = $("input[name='ongkir_hd']").val();

                if (!penerima ||
                    !no_hp ||
                    kecamatanOpt === "--Pilih Kecamatan--" ||
                    kelurahanOpt === "--Pilih Desa/Kelurahan--" ||
                    !alamat ||
                    !ongkirhd) {
                    alert("Harap lengkapi semua data pengiriman terlebih dahulu.");
                    return;
                }

                this.form.submit();

                var dataKirim = {
                    penerima: penerima,
                    no_hp: no_hp,
                    kecamatan: kecamatanOpt,
                    kelurahan: kelurahanOpt,
                    alamat: alamat,
                    total_bayar: $("input[name=total_bayar_hd]").val(),
                    kodepos: kodepos,
                    orderid: $("input[name='orderid']").val()
                };

                $.ajax({
                    url: "pembayaran/midtrans.php",
                    type: "post",
                    data: dataKirim,
                    success: function(token) {
                        var tutupmodal = document.getElementById('tutup');
                        tutupmodal?.click();
                        window.snap.pay(token);
                    }
                });
            });

        });
    </script>


</body>

</html>