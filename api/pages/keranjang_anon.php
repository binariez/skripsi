<div class="flex flex-col items-center justify-center">
    <?php
    if (isset($_POST['hapus'])) {
        $prod_id = $_POST['prod_id'];
        unset($_SESSION['keranjang'][$prod_id]);
    }
    ?>

    <?php
    $total = 0;
    if (isset($_SESSION['keranjang'])) {
        foreach ($_SESSION['keranjang'] as $key => $subtotal) {
            $total += $subtotal['prod_harga'] * $subtotal['qty'];
        }
        $_SESSION['total'] = $total;
    }
    ?>
    <!-- isi keranjang -->
    <?php
    if (!isset($_SESSION['keranjang']) or $total == 0) {
    ?>
        <p class=" text-center">Keranjang masih kosong!</p>
        <?php
    } else {
        foreach ($_SESSION['keranjang'] as $key => $value) {
        ?>
            <div class="rounded-lg md:w-2/3">
                <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
                    <img src="../public/produk_gambar/<?= $value['prod_gambar'] ?>" alt="gambar" class="w-full rounded-lg sm:w-40" />
                    <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between sm:items-center">
                        <div class="mt-5 sm:mt-0">
                            <h2 class="text-lg font-bold text-gray-900"> <?= $value['prod_nama'] ?></h2>
                        </div>
                        <div class="mt-4 flex justify-between sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                            <div class="flex items-center space-x-4">
                                <p class="text-lg bg-base-300 px-2 rounded-sm font-semibold">Rp<?= number_format($value['prod_harga']) ?></p>
                                <form action="keranjang.php" method="post">
                                    <input type="hidden" name="prod_id" value="<?= $value['prod_id'] ?>">
                                    <button class="hapus_keranjang" type="submit" name="hapus"><i style="font-size: 1.5rem;" class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </div>
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
                <?php
                $total = 0;
                if (isset($_SESSION['keranjang'])) {
                    foreach ($_SESSION['keranjang'] as $key => $subtotal) {
                        $total += $subtotal['prod_harga'] * $subtotal['qty'];
                    }
                    $_SESSION['total'] = $total;
                }
                ?>
                <td class="font-semibold">Total</td>
                <td class="font-semibold float-end">Rp<?= number_format($total) ?></td>
            </tr>
        </table>
    </div>
    <?php if ($total != 0 and isset($_SESSION['UserLogin'])) {
    ?>
        <button onclick="checkout.showModal()" class="btn bg-slate-300 w-1/3 mt-2">CHECKOUT</button>
        <!-- modal pembayaran -->
        <dialog id="checkout" class="modal modal-bottom sm:modal-middle">
            <form autocomplete="off" class="modal-box flex flex-col gap-4" target="_blank" action="php/bayar.php" method="post">
                <h3 class="font-bold text-lg mb-2">Pembayaran</h3>
                <input type="text" name="penerima" placeholder="Nama Penerima" class="input input-sm input-bordered input-md w-full" />
                <input type="text" name="alamat" placeholder="Alamat Pengiriman" class="input input-sm input-bordered input-md w-full" />
                <input type="number" name="no_hp" placeholder="No. Yang Bisa Dihubungi" class="input input-sm input-bordered input-md w-full" />

                <select name="provinsi" class="select select-sm select-bordered w-full">
                    <option disabled selected>Provinsi</option>
                </select>
                <select name="kota" class="select select-sm select-bordered w-full">
                    <option disabled selected>Kabupaten/Kota</option>
                </select>
                <select name="kurir" class="select select-sm select-bordered w-full">
                    <option disabled selected>Kurir</option>
                </select>
                <select name="tarif" class="select select-sm select-bordered w-full">
                    <option disabled selected>Tarif</option>
                </select>


                <input type="hidden" name="total_berat" value="1200">
                <input type="hidden" name="nama_provinsi">
                <input type="hidden" name="nama_kota">
                <input type="hidden" name="kode_pos">
                <input type="hidden" name="nama_kurir" placeholder="nmkurir">
                <input type="hidden" name="paket">
                <input type="hidden" name="ongkir">
                <input type="hidden" name="etd">
                <input type="hidden" name="total" value="<?= $_SESSION['total'] ?>" class="input input-bordered">

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
    <?php }
    if (!isset($_SESSION['id']) and $total != 0) {
    ?>
        <button onclick="login.showModal()" class="btn bg-slate-300 w-1/3 mt-2">LOGIN UNTUK CHECKOUT</button>
    <?php } ?>
</div>