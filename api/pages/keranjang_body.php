<div class="flex flex-col items-center justify-center">
    <?php
    $plg_id = new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id']);

    $total = 0;
    $keranjangList = $db->keranjang->find(["plg_id" => $plg_id], ['sort' => ['_id' => -1]]);
    // isi keranjang 
    if ($db->keranjang->countDocuments(["plg_id" => $plg_id]) == 0) {
    ?>
        <p class=" text-center">Keranjang masih kosong!</p>
        <?php
    } else {
        foreach ($keranjangList as $key => $value) {
            $produk = $db->produk->findOne(["_id" => new MongoDB\BSON\ObjectId($value['prod_id'])]);
            $prod_stok = $produk->prod_stok;

            $harga = $produk->prod_harga;
            $hargaSetelahDiskon = $harga;
            $voucher = null;

            if (!empty($produk->id_voucher)) {
                $voucher = $db->voucher->findOne(['_id' => $produk->id_voucher]);
                if ($voucher) {
                    $diskon = intval($voucher['diskon']);
                    $hargaSetelahDiskon = $harga - ($harga * $diskon / 100);
                }
            }

            // tambahkan ke total keseluruhan
            $hargaPerProduk = $hargaSetelahDiskon * $value['qty']; // total untuk produk ini saja
            $total += $hargaPerProduk; // total keseluruhan

        ?>
            <div class="rounded-lg md:w-2/3 mx-auto">
                <div class="mb-6 rounded-lg bg-white p-6 shadow-md flex flex-col items-center md:flex-row md:items-center md:justify-between">
                    <img src="../public/produk_gambar/<?= $produk->prod_gambar ?>" alt="gambar"
                        class="w-full h-auto rounded-lg object-cover object-center mb-4 md:mb-0 md:w-[150px] aspect-square" />

                    <div class="flex flex-col items-center md:flex-row md:items-center md:justify-between md:flex-1 md:ml-4 w-full">
                        <!-- Nama produk -->
                        <div class="mb-3 md:mb-0 md:w-1/2 text-center md:text-left pr-0 md:pr-4 w-full">
                            <h2 class="text-lg font-bold text-gray-900 break-words"><?= $produk->prod_nama ?></h2>
                        </div>

                        <!-- Form update qty -->
                        <form action="keranjang.php" method="post" class="flex items-center justify-center space-x-2 mb-3 md:mb-0 w-full md:w-auto">
                            <input type="hidden" name="prod_id" value="<?= $value['prod_id'] ?>">
                            <input type="number" name="qty" class="input input-bordered w-20 qty-input"
                                value="<?= $value['qty'] ?>" min="1" max="<?= $prod_stok ?>" data-stok="<?= $prod_stok ?>">
                            <button type="submit" name="update_qty" class="btn bg-slate-300">Update</button>
                        </form>

                        <!-- Harga dan tombol hapus -->
                        <div class="flex items-center justify-center space-x-4 w-full md:w-auto">
                            <div class="text-center md:text-left">
                                <?php if ($voucher) : ?>
                                    <p class="text-sm text-gray-500 line-through">
                                        Rp<?= number_format($harga * $value['qty']) ?>
                                    </p>
                                    <p class="text-lg font-bold text-red-600">
                                        Rp<?= number_format($hargaPerProduk) ?>
                                    </p>
                                    <span class="ml-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-md">
                                        -<?= $voucher['diskon'] ?>%
                                    </span>
                                <?php else : ?>
                                    <p class="text-lg font-semibold">
                                        Rp<?= number_format($hargaPerProduk) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <form action="keranjang.php" method="post">
                                <input type="hidden" name="prod_id" value="<?= $value['prod_id'] ?>">
                                <button class="hapus_keranjang" type="submit" name="hapus">
                                    <i style="font-size: 1.5rem;" class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
    <?php
        }
    } ?>
    <style>
        .total table {
            width: 100%;
            max-width: 500px;
            border-top: 3px solid slategrey;
        }
    </style>
    <div class="total flex w-full justify-center">
        <table>
            <tr class="flex items-center justify-between mx-4">
                <td class="font-semibold">Total</td>
                <td class="font-semibold float-end">Rp<?= number_format($total) ?></td>
            </tr>
        </table>
    </div>
    <?php if ($total != 0 and isset($_SESSION['UserLogin'])) {

        $dataplg = $db->user->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['UserLogin'][0]['id'])]);
        $terverifikasi = $dataplg->terverifikasi;
        if ($terverifikasi):
    ?>
            <button onclick="checkout.showModal()" class="btn_checkout btn bg-slate-300 w-1/3 mt-2">CHECKOUT</button>
        <?php else: ?>
            <button onclick="alert('Silahkan verifikasi email anda terlebih dahulu')" class="btn_checkout btn bg-slate-300 w-1/3 mt-2">CHECKOUT</button>
        <?php endif; ?>
        <!-- modal pembayaran -->
        <dialog id="checkout" class="modal modal-bottom sm:modal-middle">
            <form autocomplete="off" class="modal-box flex flex-col gap-4" target="_blank" action="bayar.php" method="post">
                <h3 class="font-bold text-lg mb-2">Pembayaran</h3>
                <input type="text" name="penerima" placeholder="Nama Penerima" value="<?= $dataplg->user_nama ?>" class="input input-sm input-bordered input-md w-full" />
                <input type="number" value="<?= $dataplg->plg_hp ?>" name="no_hp" placeholder="No. Yang Bisa Dihubungi" class="input input-sm input-bordered input-md w-full" />

                <input readonly type="text" name="provkab" value="Asahan, Sumatera Utara" class="input input-sm input-bordered input-md w-full" />
                <select name="kecamatan" class="select select-sm select-bordered w-full">
                    <option value="" disabled selected>--Pilih Kecamatan--</option>
                </select>
                <select name="kelurahan" id="kelurahan" class="select select-sm select-bordered w-full">
                    <option value="" disabled selected>--Pilih Desa/Kelurahan--</option>
                </select>
                <input type="text" name="alamat" value="<?= $dataplg->plg_alamat ?>" placeholder="Masukkan alamat pengiriman" class="input input-sm input-bordered input-md w-full" />

                <input type="hidden" name="total" value="<?= $total ?>" readonly>
                <input type="hidden" name="total_diskon" value="<?= $total ?>" readonly>
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Total Belanja</span>
                    </label>
                    <div class="input-group w-full">
                        <input type="text" name="total_vis" placeholder="Total Belanja" class="input input-sm input-bordered w-full" readonly />
                        <?php if ($dataplg->plg_poin > 0): ?> <button type="button" name="pakaipoin" class="btn btn-sm bg-slate-300 w-full">Gunakan <?= $dataplg->plg_poin ?> Poin</button><?php endif; ?>
                    </div>
                </div>

                <label class="text-sm">Ongkos Kirim:</label>
                <input readonly type="text" name="ongkir" placeholder="Ongkos kirim" class="input input-sm input-bordered input-md w-full" />

                <input type="hidden" name="kec_hd">
                <input type="hidden" name="kel_hd">
                <input type="hidden" name="kodepos" id="kodepos">
                <input type="hidden" name="ongkir_hd">
                <input type="hidden" name="pointerpakai" value="0">
                <input type="hidden" name="orderid" value="<?= uniqid('TRX-') ?>">

                <div class="divider">Total Bayar</div>
                <input type="hidden" name="token" id="token">
                <input type="hidden" name="total_bayar_hd">
                <input type=" text" name="total_bayar" class="input text-center input-bordered" placeholder="TOTAL BAYAR" readonly>
                <input value="Bayar" type="submit" name="bayar" id="bayar" class="btn bg-slate-300" />
            </form>
            <form method="dialog" class="modal-backdrop">
                <button id="tutup">close</button>
            </form>
        </dialog>
    <?php } ?>
</div>

<!-- mencegah user menginput qty lebih dari stok yang tersedia -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qtyInputs = document.querySelectorAll('.qty-input');

        qtyInputs.forEach(function(input) {
            // fungsi validasi
            function validateQty(el) {
                const maxStok = parseInt(el.dataset.stok);
                let currentQty = parseInt(el.value);

                if (isNaN(currentQty) || currentQty < 1) {
                    el.value = 1;
                } else if (currentQty > maxStok) {
                    alert("Jumlah melebihi stok tersedia! Maksimal: " + maxStok);
                    el.value = maxStok;
                }
            }

            // cek saat user mengetik
            input.addEventListener('input', function() {
                validateQty(this);
            });

            // cek juga saat user klik tombol step (up/down)
            input.addEventListener('change', function() {
                validateQty(this);
            });
        });
    });
    document.getElementById("kelurahan").addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex];
        const kodePos = selectedOption.getAttribute('data-kodepos');

        document.getElementById("kodepos").value = kodePos;
    });
</script>