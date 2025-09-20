<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Manajemen Admin</h3>
        </div>
    </div>
</div>

<section id="basic-vertical-layouts" class="flex-shrink-0">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <!-- Ganti Password Owner -->
                        <h5 class="mb-3">Ubah Password Owner</h5>
                        <form action="" method="POST">
                            <input type="hidden" name="user_uname" value="owner">
                            <div class="mb-3">
                                <label>Password Baru Owner</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button name="update_admin" type="submit" class="btn btn-primary">Update Password</button>
                        </form>

                        <hr>

                        <!-- Ganti Password Admin -->
                        <h5 class="mb-3">Ubah Password Admin</h5>
                        <form action="" method="POST">
                            <input type="hidden" name="user_uname" value="admin">
                            <div class="mb-3">
                                <label>Password Baru Admin</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button name="update_admin" type="submit" class="btn btn-warning">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>