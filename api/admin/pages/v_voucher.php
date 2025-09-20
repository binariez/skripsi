<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Manajemen Voucher Diskon</h3>
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
                                <div class="alert alert-success alert-dismissible fade show">✅ Kode berhasil dihapus.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'added'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Voucher berhasil ditambahkan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php elseif ($_GET['status'] === 'updated'): ?>
                                <div class="alert alert-success alert-dismissible fade show">✅ Voucher berhasil diperbarui.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger alert-dismissible fade show">❌ Terjadi kesalahan.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php
                        $collection = $db->voucher;

                        // Tambah Voucher
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
                            $insertData = [
                                "voucher_judul" => trim($_POST['voucher_judul']),
                                "kode_voucher" => strtoupper(trim($_POST['kode_voucher'])),
                                "diskon" => (int)$_POST['diskon']
                            ];
                            $collection->insertOne($insertData);
                            echo "<script>alert('Voucher berhasil ditambahkan'); window.location.href='?i=vouch&status=added';</script>";
                        }

                        // Edit Voucher
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
                            $id = new MongoDB\BSON\ObjectId($_POST['id']);
                            $updateData = [
                                "voucher_judul" => trim($_POST['voucher_judul']),
                                "kode_voucher" => strtoupper(trim($_POST['kode_voucher'])),
                                "diskon" => (int)$_POST['diskon']
                            ];
                            $collection->updateOne(["_id" => $id], ['$set' => $updateData]);
                            echo "<script>alert('Voucher berhasil diupdate'); window.location.href='?i=vouch&status=updated';</script>";
                        }
                        ?>

                        <!-- Form Tambah Voucher -->
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="voucher_judul" class="form-label">Judul Voucher</label>
                                    <input type="text" id="voucher_judul" name="voucher_judul" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="kode_voucher" class="form-label">Kode Voucher</label>
                                    <input type="text" id="kode_voucher" name="kode_voucher" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="diskon" class="form-label">Diskon (%)</label>
                                    <input type="number" id="diskon" name="diskon" class="form-control" min="1" max="100" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Tambah Voucher</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <!-- Tabel Daftar Voucher -->
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Judul Voucher</th>
                                        <th>Kode Voucher</th>
                                        <th>Diskon (%)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $vouchers = $collection->find([], ['sort' => ['_id' => -1]]);
                                    foreach ($vouchers as $row) {
                                        $id = (string)$row['_id'];
                                        echo "<tr>
                                            <td>{$row['voucher_judul']}</td>
                                            <td>{$row['kode_voucher']}</td>
                                            <td>{$row['diskon']}%</td>
                                            <td>
                                                <button class='btn btn-sm btn-warning' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#editModal'
                                                    data-id='{$id}'
                                                    data-judul='{$row['voucher_judul']}'
                                                    data-kode='{$row['kode_voucher']}'
                                                    data-diskon='{$row['diskon']}'>
                                                    Edit
                                                </button>
                                                <a href='index.php?hapus_vouch=1&id={$id}' class='btn btn-sm btn-danger' onclick=\"return confirm('Hapus voucher ini?')\">Hapus</a>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Edit Voucher -->
                        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Voucher</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" id="edit_id" name="id">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Voucher</label>
                                            <input type="text" id="edit_judul" name="voucher_judul" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kode Voucher</label>
                                            <input type="text" id="edit_kode" name="kode_voucher" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Diskon (%)</label>
                                            <input type="number" id="edit_diskon" name="diskon" class="form-control" min="1" max="100" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Script isi modal edit -->
                        <script>
                            var editModal = document.getElementById('editModal')
                            editModal.addEventListener('show.bs.modal', function(event) {
                                var button = event.relatedTarget
                                document.getElementById('edit_id').value = button.getAttribute('data-id')
                                document.getElementById('edit_judul').value = button.getAttribute('data-judul')
                                document.getElementById('edit_kode').value = button.getAttribute('data-kode')
                                document.getElementById('edit_diskon').value = button.getAttribute('data-diskon')
                            })
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>