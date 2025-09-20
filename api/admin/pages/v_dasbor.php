<?php

$totalCustomers = getTotalCustomers($db);
$totalRevenue = getTotalRevenue($db);
$totalProducts = getTotalProducts($db);
$reviews = getReview($db);
$transactions = getLatestTransactions($db);
?>

<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3 class="text-3xl">Dasbor Utama</h3>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>

<section id="dashboard-stats">
    <div class="row">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card text-center bg-green-400 text-white">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-title"><i class="bi bi-currency-dollar"></i> Total Pendapatan</div>
                        <h1 class="display-4 text-white">Rp<?= number_format($totalRevenue) ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card text-center bg-blue-500 text-white">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-title"><i class="bi bi-people-fill"></i> Total Pelanggan</div>
                        <h1 class="display-4 text-white"><?= $totalCustomers ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card text-center bg-rose-400 text-white">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-title"><i class="bi bi-box-seam"></i> Total Produk</div>
                        <h1 class="display-4 text-white"><?= $totalProducts ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="additional-stats" class="mt-4">
    <div class="row d-flex flex-wrap">

        <div class="col-xl-6 col-md-6 col-sm-12">
            <div class="card text-center bg-white text-black">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-title"><i class="bi bi-receipt"></i> Transaksi Terbaru</div>

                        <?php if (!empty($transactions)) : ?>
                            <?php foreach ($transactions as $trx) : ?>
                                <div class="review-container">
                                    <div class="review-header">
                                        <span class="reviewer-info">No. Faktur: <?= htmlspecialchars($trx['orderid']) ?></span>
                                        <span class="review-date">Tanggal: <?= htmlspecialchars($trx['trx_tgl']) ?></span>
                                    </div>
                                    <div class="review-product">
                                        Pelanggan: <?= htmlspecialchars($trx['plg_nama'] ?? 'Tidak diketahui') ?>
                                    </div>
                                    <div class="review-comment">
                                        <p>Total Belanja: Rp<?= number_format($trx['trx_belanja']) ?></p>
                                        <p>Ongkir: Rp<?= number_format($trx['trx_ongkir']) ?></p>
                                        <p>Status Transaksi: <?= htmlspecialchars($trx['trx_status']) ?> | Paket: <?= htmlspecialchars($trx['paket_status']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-gray-500">Belum ada transaksi terbaru.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 col-sm-12">
            <div class="card text-center bg-white text-black">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-title"><i class="bi bi-star-fill"></i> Review Terbaru</div>

                        <?php if (!empty($reviews)) : ?>
                            <?php foreach ($reviews as $review) : ?>
                                <div class="review-container">
                                    <div class="review-header">
                                        <span class="reviewer-info">Nama Reviewer: <?= htmlspecialchars($review['plg_nama']) ?></span>
                                        <span class="review-date">Tanggal Review: <?= htmlspecialchars($review['review_tgl']) ?></span>
                                    </div>
                                    <div class="review-product">Produk yang Direview: <?= htmlspecialchars($review['prod_nama']) ?></div>
                                    <div class="review-comment">
                                        <p><?= htmlspecialchars($review['review_isi']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-gray-500">Belum ada review terbaru.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>



    </div>
</section>

<style>
    .review-container {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }

    .reviewer-info {
        font-weight: bold;
        color: #007bff;
        font-size: 0.95em;
    }

    .review-date {
        font-size: 0.8em;
        color: #777;
    }

    .review-product {
        font-size: 1em;
        font-weight: bold;
        margin-bottom: 8px;
        color: #555;
    }

    .review-comment {
        line-height: 1.5;
        color: #444;
        font-size: 0.9em;
    }
</style>