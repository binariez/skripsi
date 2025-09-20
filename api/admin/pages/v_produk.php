<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Manajemen Produk</h3>
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
                            <?php if ($_GET['status'] === 'deleted'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Produk berhasil dihapus.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'added'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Produk berhasil ditambahkan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'updated'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Produk berhasil diperbarui.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'kode_exist'): ?>
                                <div class="alert alert-warning alert-dismissible fade show">⚠️ Kode produk sudah digunakan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger alert-dismissible fade show">❌ Terjadi kesalahan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="my-2">Daftar Produk</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                                + Tambah Produk
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped display nowrap" style="width:100%" id="tblProduk">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th>Harga</th>
                                        <th>Voucher</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $data = $db->produk->find();
                                    foreach ($data as $d):
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($d['prod_gambar'])): ?>
                                                    <img src="../../../public/produk_gambar/<?= htmlspecialchars($d['prod_gambar']) ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;">
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($d['prod_kode']) ?></td>
                                            <td><?= htmlspecialchars($d['prod_nama']) ?></td>
                                            <td>
                                                <?php
                                                $deskripsi = htmlspecialchars($d['prod_deskripsi']);
                                                $panjang_maksimal = 35; // Ganti angka ini sesuai dengan panjang karakter yang Anda inginkan

                                                if (strlen($deskripsi) > $panjang_maksimal) {
                                                    $deskripsi_terpotong = substr($deskripsi, 0, $panjang_maksimal) . '...';
                                                    echo $deskripsi_terpotong;
                                                } else {
                                                    echo $deskripsi;
                                                }
                                                ?>
                                            </td>
                                            <td>Rp<?= number_format($d['prod_harga']) ?></td>
                                            <td>
                                                <?php
                                                if (!empty($d['id_voucher'])) {
                                                    $voucher = $db->voucher->findOne(['_id' => $d['id_voucher']]);
                                                    echo $voucher ? $voucher['kode_voucher'] . " ({$voucher['diskon']}%)" : '-';
                                                } else {
                                                    echo "-";
                                                }
                                                ?>
                                            </td>
                                            <td><?= intval($d['prod_stok']) ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-warning btn-sm btnEditProduk"
                                                    data-id="<?= $d['_id']; ?>"
                                                    data-kode="<?= htmlspecialchars($d['prod_kode']) ?>"
                                                    data-nama="<?= htmlspecialchars($d['prod_nama']) ?>"
                                                    data-desk="<?= htmlspecialchars($d['prod_deskripsi']) ?>"
                                                    data-harga="<?= htmlspecialchars($d['prod_harga']) ?>"
                                                    data-voucher="<?= isset($d['id_voucher']) ? (string)$d['id_voucher'] : '' ?>"
                                                    data-stok="<?= htmlspecialchars($d['prod_stok']) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditProduk">
                                                    Edit
                                                </button>
                                                <a href="index.php?hapus_produk=<?= $d['_id']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                                    class="btn btn-danger btn-sm">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL TAMBAH PRODUK -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?tambah_produk=1" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kode Produk</label>
                    <input type="text" name="prod_kode" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="prod_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="prod_deskripsi" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="prod_harga" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Pilih Voucher (opsional)</label>
                    <select name="id_voucher" id="edit_prod_voucher" class="form-control">
                        <option value="">-- Tanpa Voucher --</option>
                        <?php
                        $vouchers = $db->voucher->find();
                        foreach ($vouchers as $v) {
                            echo "<option value='{$v['_id']}'>{$v['kode_voucher']} - {$v['diskon']}%</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Stok</label>
                    <input type="number" name="prod_stok" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Gambar</label>
                    <input type="file" name="prod_gambar" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PRODUK -->
<div class="modal fade" id="modalEditProduk" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?edit_produk=1" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="prod_id" id="edit_prod_id">
                <div class="mb-3">
                    <label>Kode Produk</label>
                    <input type="text" name="prod_kode" id="edit_prod_kode" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="prod_nama" id="edit_prod_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="prod_deskripsi" id="edit_prod_deskripsi" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="prod_harga" id="edit_prod_harga" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Pilih Voucher (opsional)</label>
                    <select name="id_voucher" id="edit_prod_voucher" class="form-control">
                        <option value="">-- Tanpa Voucher --</option>
                        <?php
                        $vouchers = $db->voucher->find();
                        foreach ($vouchers as $v) {
                        ?><option value="<?= (string)$v['_id'] ?>" data-diskon="<?= $v['diskon'] ?>">
                                <?= $v['kode_voucher'] ?> - <?= $v['diskon'] ?>%
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Stok</label>
                    <input type="number" name="prod_stok" id="edit_prod_stok" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Gambar (opsional)</label>
                    <input type="file" name="prod_gambar" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.btnEditProduk').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_prod_id').value = this.dataset.id;
            document.getElementById('edit_prod_kode').value = this.dataset.kode;
            document.getElementById('edit_prod_nama').value = this.dataset.nama;
            document.getElementById('edit_prod_deskripsi').value = this.dataset.desk;
            document.getElementById('edit_prod_harga').value = this.dataset.harga;
            document.getElementById('edit_prod_stok').value = this.dataset.stok;

            // === Auto select voucher ===
            let voucherSelect = document.getElementById('edit_prod_voucher');
            let voucherId = this.dataset.voucher;

            if (voucherId && voucherSelect.querySelector(`option[value="${voucherId}"]`)) {
                voucherSelect.value = voucherId; // pilih sesuai data
            } else {
                voucherSelect.value = ""; // fallback ke "Tanpa Voucher"
            }
        });
    });

    $(document).ready(function() {
        $('#tblProduk').DataTable({
            responsive: true,
            order: [
                [2, 'asc']
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>