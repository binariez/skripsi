<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Manajemen Pelanggan</h3>
        </div>
    </div>
</div>

<section id="basic-vertical-layouts" class="flex-shrink-0">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (isset($_GET['status'])): ?>
                                <?php if ($_GET['status'] === 'deleted'): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        ✅ Pelanggan berhasil dihapus.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php elseif ($_GET['status'] === 'error'): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        ❌ Terjadi kesalahan saat menghapus pelanggan.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <h4 class="my-2">Daftar Pelanggan</h4>
                            <table class="table table-striped display nowrap" style="width:100%" id="tblPelanggan">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Alamat</th>
                                        <th>No. HP</th>
                                        <th>Poin</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filter = ['user_role' => 'PLG'];
                                    $data = $db->user->find($filter);

                                    foreach ($data as $d) {
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($d['user_pfp'])) { ?>
                                                    <img src="../../../public/user_gambar/<?= htmlspecialchars($d['user_pfp']) ?>" alt="Foto" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td><?= htmlspecialchars($d['user_uname']) ?></td>
                                            <td><?= htmlspecialchars($d['user_nama']) ?></td>
                                            <td><?= htmlspecialchars($d['plg_jk']) ?></td>
                                            <td><?= htmlspecialchars($d['plg_alamat']) ?></td>
                                            <td><?= htmlspecialchars($d['plg_hp']) ?></td>
                                            <td><?= intval($d['plg_poin']) ?></td>
                                            <td>
                                                <a href="index.php?i=plg&hapus_pelanggan=<?= $d['_id']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                                                    class="btn btn-danger btn-sm">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Pastikan tabel sudah ada di DOM
        if ($('#tblPelanggan').length) {
            $('#tblPelanggan').DataTable({
                responsive: true,
                order: [
                    [1, 'asc']
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });
        }
    });
</script>