<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Transaksi & Pesanan</h3>
        </div>
    </div>
</div>

<section id="basic-vertical-layouts" class="flex-shrink-0">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">

                        <!-- ALERT STATUS -->
                        <?php if (isset($_GET['status'])): ?>
                            <?php if ($_GET['status'] === 'updated'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Status paket berhasil diperbarui.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'deleted'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Transaksi berhasil dihapus.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger alert-dismissible fade show">❌ Terjadi kesalahan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="trxTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pesanan-tab" data-bs-toggle="tab" href="#pesanan" role="tab">Pesanan Masuk</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="riwayat-tab" data-bs-toggle="tab" href="#riwayat" role="tab">Riwayat Transaksi</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">

                            <!-- Pesanan Masuk -->
                            <div class="tab-pane fade show active" id="pesanan" role="tabpanel">
                                <div class="mb-2 d-flex justify-content-end">
                                    <select id="filterStatusPesanan" class="form-select w-auto d-inline-block">
                                        <option value="">Semua Status</option>
                                        <option value="MENUNGGU">MENUNGGU</option>
                                        <option value="PROSES">PROSES</option>
                                        <option value="SEDANG DIKIRIM">SEDANG DIKIRIM</option>
                                    </select>
                                    <a href="../cleanup_transaksi.php"
                                        onclick="return confirm('Ingin batalkan semua transaksi kadaluarsa?')"
                                        class="btn btn-md btn-secondary">Batalkan Kadaluarsa
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped display nowrap" id="tblPesanan">
                                        <thead>
                                            <tr>
                                                <th>No. Faktur</th>
                                                <th>Username</th>
                                                <th>Tanggal</th>
                                                <th>Total</th>
                                                <th>Status Transaksi</th>
                                                <th>Status Paket</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $pesananMasuk = $db->transaksi->aggregate([
                                                [
                                                    '$match' => [
                                                        'trx_status' => ['$in' => ['PENDING', 'SETTLEMENT']],
                                                        'paket_status' => ['$in' => ['MENUNGGU', 'PROSES', 'SEDANG DIKIRIM']]
                                                    ]
                                                ],
                                                ['$lookup' => [
                                                    'from' => 'user',
                                                    'localField' => 'plg_id',
                                                    'foreignField' => '_id',
                                                    'as' => 'userData'
                                                ]]
                                            ]);
                                            foreach ($pesananMasuk as $trx):
                                                $total = $trx['trx_bldiskon'] + $trx['trx_ongkir'];
                                                $username = isset($trx['userData'][0]['user_uname']) ? $trx['userData'][0]['user_uname'] : 'Tidak Diketahui';
                                                $badgeClass = match ($trx['trx_status']) {
                                                    'PENDING' => 'bg-warning',
                                                    'SETTLEMENT' => 'bg-success',
                                                    default => 'bg-danger'
                                                };
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($trx['orderid']) ?></td>
                                                    <td><?= htmlspecialchars($username) ?></td>
                                                    <td><?= htmlspecialchars($trx['trx_tgl']) ?></td>
                                                    <td>Rp<?= number_format($total) ?></td>
                                                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($trx['trx_status']) ?></span></td>
                                                    <td>
                                                        <?php
                                                        $paketBadge = match ($trx['paket_status'] ?? '') {
                                                            'PENDING' => 'bg-warning',
                                                            'PROSES' => 'bg-primary',
                                                            'SEDANG DIKIRIM' => 'bg-info',
                                                            default => 'bg-secondary'
                                                        };
                                                        ?>
                                                        <span class="badge <?= $paketBadge ?>"><?= htmlspecialchars($trx['paket_status'] ?? '-') ?></span>
                                                    </td>

                                                    <td>
                                                        <a data-trx='<?= json_encode($trx) ?>' class="btnCetakTrx btn btn-sm btn-info">Cetak</a>
                                                        <button class="btn btn-sm btn-primary btnDetailTrx"
                                                            data-trx='<?= json_encode($trx) ?>'>Detail</button>
                                                        <button class="btn btn-sm btn-warning btnUbahStatus"
                                                            data-id="<?= $trx['_id'] ?>"
                                                            data-status="<?= $trx['trx_status'] ?>">Ubah Status</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Riwayat -->
                            <div class="tab-pane fade" id="riwayat" role="tabpanel">
                                <div class="mb-2 d-flex justify-content-end">
                                    <select id="filterStatusRiwayat" class="form-select w-auto d-inline-block">
                                        <option value="">Semua Status</option>
                                        <option value="SELESAI">SELESAI</option>
                                        <option value="BATAL">BATAL</option>
                                    </select>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped display nowrap" id="tblRiwayat">
                                        <thead>
                                            <tr>
                                                <th>No. Faktur</th>
                                                <th>Pelanggan</th>
                                                <th>Tanggal</th>
                                                <th>Total</th>
                                                <th>Status Transaksi</th>
                                                <th>Status Paket</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $riwayat = $db->transaksi->aggregate([
                                                ['$match' => ['paket_status' => ['$in' => ['SELESAI', 'BATAL']]]],
                                                ['$lookup' => [
                                                    'from' => 'user',
                                                    'localField' => 'plg_id',
                                                    'foreignField' => '_id',
                                                    'as' => 'userData'
                                                ]]
                                            ]);
                                            foreach ($riwayat as $trx):
                                                $total = $trx['trx_bldiskon'] + $trx['trx_ongkir'];
                                                $username = isset($trx['userData'][0]['user_uname']) ? $trx['userData'][0]['user_uname'] : 'Tidak Diketahui';
                                                $badgeClassTrx = $trx['trx_status'] === 'SETTLEMENT' ? 'bg-success' : 'bg-danger';
                                                $badgeClassStt = $trx['paket_status'] === 'SELESAI' ? 'bg-success' : 'bg-danger';
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($trx['orderid']) ?></td>
                                                    <td><?= htmlspecialchars($username) ?></td>
                                                    <td><?= htmlspecialchars($trx['trx_tgl']) ?></td>
                                                    <td>Rp<?= number_format($total) ?></td>
                                                    <td><span class="badge <?= $badgeClassTrx ?>"><?= htmlspecialchars($trx['trx_status']) ?></span></td>
                                                    <td><span class="badge <?= $badgeClassStt ?>"><?= htmlspecialchars($trx['paket_status']) ?></span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary btnDetailTrx"
                                                            data-trx='<?= json_encode($trx) ?>'>Detail</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="modalDetailTrx" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pesanan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h6>Data Pengiriman</h6>
                                        <p><strong>Penerima:</strong> <span id="detailPenerima"></span></p>
                                        <p><strong>Alamat:</strong> <span id="detailAlamat"></span></p>
                                        <p><strong>No HP:</strong> <span id="detailHP"></span></p>
                                        <hr>
                                        <h6>Item Belanja</h6>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detailTrxBody"></tbody>
                                        </table>
                                        <hr>
                                        <p><strong>Total Belanja:</strong> Rp<span id="detailBelanja"></span></p>
                                        <p><strong>Diskon:</strong> Rp<span id="detailDiskon"></span></p>
                                        <p><strong>Ongkir:</strong> Rp<span id="detailOngkir"></span></p>
                                        <p><strong>Total Keseluruhan:</strong> Rp<span id="detailTotal"></span></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Ubah Status -->
                        <div class="modal fade" id="modalUbahStatus" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="index.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Status Transaksi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="ubah_status_id" id="ubahStatusId">
                                            <select name="status_baru" class="form-select" required>
                                                <option value="MENUNGGU">MENUNGGU</option>
                                                <option value="PROSES">PROSES</option>
                                                <option value="SEDANG DIKIRIM">SEDANG DIKIRIM</option>
                                                <option value="SELESAI">SELESAI</option>
                                                <option value="BATAL">BATAL</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.querySelectorAll('.btnDetailTrx').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    let trx = JSON.parse(this.dataset.trx);
                                    document.getElementById('detailPenerima').innerText = trx.trx_penerima;
                                    document.getElementById('detailAlamat').innerText = trx.trx_alamat;
                                    document.getElementById('detailHP').innerText = trx.trx_hp;
                                    document.getElementById('detailBelanja').innerText = Number(trx.trx_belanja).toLocaleString();
                                    document.getElementById('detailDiskon').innerText = Number(trx.trx_pointerpakai).toLocaleString();
                                    document.getElementById('detailOngkir').innerText = Number(trx.trx_ongkir).toLocaleString();
                                    document.getElementById('detailTotal').innerText = Number(trx.trx_bldiskon + trx.trx_ongkir).toLocaleString();
                                    let tbody = document.getElementById('detailTrxBody');
                                    tbody.innerHTML = '';
                                    trx.items.forEach(it => {
                                        tbody.innerHTML += `
                                            <tr>
                                                <td>${it.nama_prod}</td>
                                                <td>${it.qty}</td>
                                                <td>Rp${Number(it.harga).toLocaleString()}</td>
                                                <td>Rp${Number(it.subtotal).toLocaleString()}</td>
                                            </tr>
                                        `;
                                    });
                                    new bootstrap.Modal(document.getElementById('modalDetailTrx')).show();
                                });
                            });

                            document.querySelectorAll('.btnUbahStatus').forEach(btn => {
                                let trxStatus = btn.dataset.status;
                                // Nonaktifkan tombol jika trx_status belum SETTLEMENT
                                if (trxStatus !== 'SETTLEMENT') {
                                    btn.disabled = true;
                                    btn.title = "Transaksi belum lunas, tidak bisa diubah";
                                } else {
                                    btn.addEventListener('click', function() {
                                        document.getElementById('ubahStatusId').value = this.dataset.id;
                                        document.querySelector('#modalUbahStatus select').value = this.dataset.status;
                                        new bootstrap.Modal(document.getElementById('modalUbahStatus')).show();
                                    });
                                }
                            });


                            $(document).ready(function() {
                                let tblPesanan = $('#tblPesanan').DataTable({
                                    responsive: true,
                                    order: [
                                        [2, 'desc']
                                    ],
                                    language: {
                                        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                                    }
                                });
                                let tblRiwayat = $('#tblRiwayat').DataTable({
                                    responsive: true,
                                    order: [
                                        [2, 'desc']
                                    ],
                                    language: {
                                        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                                    }
                                });

                                // Pesanan Masuk: filter berdasarkan paket_status
                                $('#filterStatusPesanan').on('change', function() {
                                    tblPesanan.column(5).search(this.value).draw();
                                });

                                // Riwayat Transaksi: filter juga berdasarkan paket_status
                                $('#filterStatusRiwayat').on('change', function() {
                                    tblRiwayat.column(5).search(this.value).draw();
                                });

                            });
                        </script>

                        <!-- CETAK BON FAKTUR -->
                        <script>
                            document.querySelectorAll('.btnCetakTrx').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    let trx = JSON.parse(this.dataset.trx);

                                    let printWindow = window.open('', '', 'width=800,height=600');
                                    printWindow.document.write(`
            <html>
            <head>
                <title>Order: ${trx.orderid}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                    h1, h2 { margin: 0; }
                    .header, .footer { text-align: center; margin-bottom: 20px; }
                    .invoice-info { margin-bottom: 20px; }
                    .invoice-info p { margin: 4px 0; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #333; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .total { text-align: right; margin-top: 10px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Nafisah Bread & Cake</h1>
                    <p>Jl. Budi Utomo Siumbut Baru, Kabupaten Asahan | 08137482712</p>
                    <hr>
                </div>

                <div class="invoice-info">
    <div class="info-row"><span class="label">No. Faktur</span><span class="sep">:</span> <span class="value">${trx.orderid}</span></div>
    <div class="info-row"><span class="label">Penerima</span><span class="sep">:</span> <span class="value">${trx.trx_penerima}</span></div>
    <div class="info-row"><span class="label">Alamat</span><span class="sep">:</span> <span class="value">${trx.trx_alamat}</span></div>
    <div class="info-row"><span class="label">No HP</span><span class="sep">:</span> <span class="value">${trx.trx_hp}</span></div>
</div>

<style>
.invoice-info {
    margin-bottom: 20px;
}
.info-row {
    display: flex;
    margin-bottom: 4px;
}
.label {
    width: 100px; /* Atur lebar label agar titik dua sejajar */
    font-weight: bold;
}
.sep {
    margin: 0 5px;
}
.value {
    flex: 1;
}
</style>


                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${trx.items.map(it => `
                        <tr>
                            <td>${it.nama_prod}</td>
                            <td>${it.qty}</td>
                            <td>Rp${Number(it.harga).toLocaleString()}</td>
                            <td>Rp${Number(it.subtotal).toLocaleString()}</td>
                        </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="total">
                    <p>Total Belanja: Rp${Number(trx.trx_belanja).toLocaleString()}</p>
                    <p>Diskon: Rp${Number(trx.trx_pointerpakai).toLocaleString()}</p>
                    <p>Ongkir: Rp${Number(trx.trx_ongkir).toLocaleString()}</p>
                    <p>Total Keseluruhan: Rp${Number(trx.trx_bldiskon + trx.trx_ongkir).toLocaleString()}</p>
                </div>

                <div class="footer">
                <hr>
                    <p>Terima kasih telah berbelanja di Nafisah Bread & Cake!</p>
                </div>
            </body>
            </html>
        `);
                                    printWindow.document.close();
                                    printWindow.print();
                                });
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>