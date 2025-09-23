<?php
// Ambil produk dari MongoDB (contoh produk terbaru)
$produkTerbaru = $db->produk->find([], ['limit' => 4, 'sort' => ['_id' => -1]]);
?>

<main class="flex-grow bg-slate-50">

    <!-- Hero Section -->
    <section class="relative bg-slate-800 text-white">
        <div class="h-[500px] bg-cover bg-center flex flex-col items-center justify-center text-center px-4"
            style="background-image: url('../public/landing.jpg'); background-blend-mode: multiply;">
            <div class="">
                <h1 class="tw text-4xl md:text-6xl font-bold mb-4 text-center">
                    Selamat Datang di <span class="text-sky-500">Nafisah Bread & Cake</span>
                </h1>
            </div>

            <style>
                .tw {
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 1);
                    background-color: rgba(0, 0, 0, 0.05);
                }
            </style>

            <p class="text-lg font-bold md:text-xl mb-6">Roti fresh setiap hari, rasa yang selalu menggoda.</p>
            <marquee behavior="scroll" direction="left" scrollamount="10">
                <p class="mb-6 text-2xl md:text-4xl text-black font-bold">Promo September Ceria: Beli 2 Bolu Gulung, Gratis 4 Donat</p>
            </marquee>
            <a href="produk.php" class="btn bg-emerald-400 hover:bg-emerald-300 text-black font-semibold px-6">Belanja Sekarang</a>
        </div>
    </section>

    <!-- Kategori -->
    <!-- <section class="py-12 px-6 md:px-20 bg-slate-100">
        <h2 class="text-2xl font-bold text-center mb-8 text-slate-800">Kategori Roti</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            $kategori = [
                ['nama' => 'Roti Manis', 'img' => 'manis.jpg'],
                ['nama' => 'Roti Tawar', 'img' => 'tawar.jpg'],
                ['nama' => 'Kue', 'img' => 'kue.jpg'],
                ['nama' => 'Selai', 'img' => 'jam.jpg'],
            ];
            foreach ($kategori as $k): ?>
                <div class="card bg-white border border-slate-200 shadow-sm hover:shadow-md transition">
                    <figure><img src="../public/kategori/<?= $k['img'] ?>" alt="<?= $k['nama'] ?>" class="h-32 object-cover w-full"></figure>
                    <div class="card-body items-center text-center">
                        <h3 class="font-semibold text-slate-700"><?= $k['nama'] ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section> -->

    <!-- Produk Terbaru -->
    <section class="py-12 px-6 md:px-20 bg-slate-50">
        <h2 class="text-2xl font-bold text-center mb-8 text-slate-800">Produk Terbaru</h2>
        <div class="flex flex-wrap justify-center gap-6">
            <?php foreach ($produkTerbaru as $d): ?>
                <?php
                $harga = $d['prod_harga'];
                $hargaSetelahDiskon = $harga;
                $voucher = null;
                if (!empty($d['id_voucher'])) {
                    $voucher = $db->voucher->findOne(['_id' => $d['id_voucher']]);
                    if ($voucher) {
                        $diskon = intval($voucher['diskon']);
                        $hargaSetelahDiskon = $harga - ($harga * $diskon / 100);
                    }
                }
                ?>
                <div class="card w-64 bg-white border border-slate-200 shadow-sm hover:shadow-md transition">
                    <a href="detail_produk.php?prod_id=<?= $d['_id'] ?>">
                        <figure><img src="https://img.nafisahcake.store/produk/<?= $d['prod_gambar'] ?>" alt="gambar" class="h-48 w-full object-cover rounded-t-lg"></figure>
                    </a>
                    <div class="card-body">
                        <h3 class="font-bold text-lg text-slate-800"><?= $d['prod_nama'] ?></h3>
                        <?php if ($voucher): ?>
                            <p class="text-gray-500 line-through text-sm">Rp<?= number_format($harga) ?></p>
                            <p class="text-red-600 font-bold">Rp<?= number_format($hargaSetelahDiskon) ?></p>
                            <span class="badge bg-red-100 text-red-600">-<?= $voucher['diskon'] ?>%</span>
                        <?php else: ?>
                            <p class="font-semibold text-slate-700">Rp<?= number_format($harga) ?></p>
                        <?php endif; ?>
                        <div class="card-actions justify-end mt-3">
                            <?php
                            if (!isset($_SESSION['UserLogin'])) {
                            ?>
                                <button onclick="login.showModal()" class="btn bg-slate-700 hover:bg-slate-600 text-white"><i class="fa-solid fa-cart-plus"></i></button>
                            <?php } else { ?>
                                <form action="keranjang.php" method="post">
                                    <input type="hidden" name="prod_id" value="<?= $d['_id'] ?>">
                                    <input type="hidden" name="prod_nama" value="<?= $d['prod_nama'] ?>">
                                    <input type="hidden" name="prod_harga" value="<?= $d['prod_harga'] ?>">
                                    <input type="hidden" name="prod_gambar" value="<?= $d['prod_gambar'] ?>">
                                    <button type="submit" name="tambah" class="btn bg-slate-700 hover:bg-slate-600 text-white"><i class="fa-solid fa-cart-plus"></i></button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Promo Banner -->
    <section class="py-12 px-6 md:px-20">
        <div class="bg-slate-800 rounded-2xl text-white text-center p-10 shadow-lg">
            <h2 class="text-3xl font-bold mb-4">Promo Spesial Kami!</h2>
            <p class="mb-6 text-slate-200">Nikmati diskon untuk produk pilihan. Jangan sampai kehabisan!</p>
            <a href="promosi.php" class="btn bg-emerald-400 hover:bg-emerald-300 text-black font-semibold">Lihat Promo</a>
        </div>
    </section>

</main>