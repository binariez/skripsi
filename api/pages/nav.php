<?php
// require_once "regist.php";
// require_once "login.php";
?>

<nav class="sticky top-0 z-30">
    <div class=" w-full">
        <div class="px-8 w-full bg-slate-200 shadow-sm">
            <div class="border-b-[1px] py-0">
                <div class="flex items-center justify-between gap-3 md:gap-0">
                    <a href="../api/" class="flex items-center text-2xl font-bold">
                        <img class="mix-blend-multiply" src="../public/logo.png" width="170" alt="NFS" />
                    </a>

                    <!-- navbar -->
                    <?php
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $pages = ['produk.php', 'promosi.php', 'kontak.php'];

                    // kalau halaman sekarang bukan salah satu dari $pages, set default ke produk.php
                    if (!in_array($current_page, $pages)) {
                        $current_page = 'produk.php';
                    }
                    ?>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <a href="produk.php"
                                class="rounded-md px-3 py-2 text-sm font-medium 
           <?php echo ($current_page == 'produk.php')
                ? 'bg-gray-900 text-white'
                : 'text-black hover:bg-gray-700 hover:text-white'; ?>">
                                Produk
                            </a>

                            <a href="promosi.php"
                                class="rounded-md px-3 py-2 text-sm font-medium 
           <?php echo ($current_page == 'promosi.php')
                ? 'bg-gray-900 text-white'
                : 'text-black hover:bg-gray-700 hover:text-white'; ?>">
                                Promosi
                            </a>

                            <a href="kontak.php"
                                class="rounded-md px-3 py-2 text-sm font-medium 
           <?php echo ($current_page == 'kontak.php')
                ? 'bg-gray-900 text-white'
                : 'text-black hover:bg-gray-700 hover:text-white'; ?>">
                                Kontak
                            </a>
                        </div>
                    </div>


                    <!-- search -->
                    <div class="hidden md:block">
                        <form class="join" action="produk.php" method="get">
                            <div>
                                <div>
                                    <input name="keyword" class="input input-bordered join-item" placeholder="Cari produk..." required />
                                </div>
                            </div>

                            <div class="indicator">
                                <input value="Cari" type="submit" class="btn join-item">
                            </div>
                        </form>
                    </div>
                    <div class="flex items-center gap-8 md:gap-12">
                        <?php
                        if (isset($_SESSION['UserLogin'])) {
                            foreach ($_SESSION['UserLogin'] as $key) {
                                $nama = $key['nama'];
                                $uname = $key['uname'];
                                $email = $key['email'];
                                $alamat = $key['alamat'];
                                $pfp = $key['pfp'];
                                $role = $key['role'];
                            }
                        } else {
                            $role = 'false';
                        }
                        ?>

                        <a href="admin/" class="<?php if ($role == 'admin') echo 'block';
                                                else echo 'hidden' ?>">
                            <div class="p-1 text-gray-800 transition-scale ease-out hover:scale-105 duration-100 cursor-pointer">
                                <i class="fa-solid fa-screwdriver-wrench" style="font-size: 2rem;"></i>
                            </div>
                        </a>
                        <a href="keranjang.php">
                            <div class="p-1 text-gray-800 transition-scale ease-out hover:scale-105 duration-100 cursor-pointer">
                                <i class="fa-solid fa-cart-shopping" style="font-size: 2rem;"></i>
                            </div>
                        </a>
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="avatar m-1 transition-scale ease-out hover:scale-105 duration-200">
                                <div class="rounded-full w-11"><img src="<?php echo (isset($_SESSION['UserLogin']) ? '../public/user_gambar/' . $pfp : '../public/pfp.png') ?>"></div>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <?php
                                if (isset($_SESSION['UserLogin'])) {

                                ?>
                                    <li>
                                        <p><?= ucfirst($nama) ?> | <?= $role ?></p>
                                    </li>
                                    <a href="riwayat.php">
                                        <li><button>Riwayat</button></li>
                                    </a>
                                    <!-- modal SETTING -->
                                    <li><button onclick="setting.showModal()">Setting</button></li>
                                    <dialog id="setting" class="modal modal-bottom sm:modal-middle">
                                        <div class="modal-box flex flex-col gap-4">
                                            <h3 class="font-bold text-lg mb-2">Setting</h3>
                                            <form autocomplete="off" class="flex flex-col gap-4 items-center" action="index.php" method="post" enctype="multipart/form-data">

                                                <div class="avatar">
                                                    <div class="w-32 rounded-full">
                                                        <img src="../public/user_gambar/<?= $pfp ?>" />
                                                    </div>
                                                </div>

                                                <h1 class="text-xl font-bold"><?= ucfirst($nama) ?></h1>

                                                <?php if ($role != 'admin'):
                                                    $poin = $db->user->findOne(['user_uname' => $uname])->plg_poin; ?>

                                                    <label>Username: <?= $uname ?></label>
                                                    <label>Email: <?= $email ?></label>
                                                    <label>Alamat: <?= $alamat ?></label>
                                                    <label>Poin: <?= $poin ?></label>
                                                    <!-- <input type="text" name="alamat" value="<?= $alamat ?>" placeholder="Alamat" class="input input-bordered input-md w-full" required /> -->
                                                    <input type="password" name="passwordlama" placeholder="Password Sekarang" class="input input-bordered input-md w-full" required />
                                                    <input onkeyup="onChange();" type="password" name="passwordbaru" placeholder="Password Baru" class="input input-bordered input-md w-full" required />
                                                    <input onkeyup="onChange();" type="password" name="konfirmasi" placeholder="konfirmasi Password Baru" class="input input-bordered input-md w-full" required />
                                                    <button type="submit" name="updatepwd" class="btn bg-slate-300">Simpan</button>
                                                    <script>
                                                        function onChange() {
                                                            let password = document.querySelector('input[name=passwordbaru]');
                                                            let konfirmasi = document.querySelector('input[name=konfirmasi]');
                                                            if (konfirmasi.value === password.value) {
                                                                konfirmasi.setCustomValidity('');
                                                            } else {
                                                                konfirmasi.setCustomValidity('Password tidak sama.');
                                                            }
                                                        }
                                                    </script>
                                                <?php else: ?>
                                                    <span>
                                                        <p>Masuk ke halaman admin untuk mengganti password admin</p>
                                                    </span>
                                                <?php endif; ?>
                                            </form>
                                        </div>

                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
                                    <hr>
                                    <li><a href="logout.php">Logout</a></li>
                                <?php } else {
                                ?>
                                    <!-- modal login -->
                                    <li><button onclick="login.showModal()">Login</button></li>
                                    <dialog id="login" class="modal modal-bottom sm:modal-middle">
                                        <div class="modal-box flex flex-col gap-4">
                                            <form class="flex flex-col gap-4" action="index.php" method="post" enctype="multipart/form-data">
                                                <h3 class="font-bold text-lg mb-2">Login</h3>
                                                <input type="text" name="txtusername" placeholder="Username" class="input input-bordered input-md w-full" required />
                                                <input type="password" name="txtpassword" placeholder="Password" class="input input-bordered input-md w-full" required />
                                                <button type="submit" name="login" class="btn bg-slate-300">Login</button>
                                            </form>

                                            <form method="dialog">
                                                <p>Belum punya akun? <button onclick="daftar.showModal()">Daftar disini</button></p>
                                            </form>
                                        </div>

                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>

                                    <hr>
                                    <!-- modal daftar -->
                                    <li><button onclick="daftar.showModal()">Daftar</button></li>
                                    <dialog id="daftar" class="modal modal-bottom sm:modal-middle">
                                        <div class="modal-box flex flex-col gap-4">
                                            <form class="flex flex-col gap-4" action="" method="post" enctype="multipart/form-data">
                                                <h3 class="font-bold text-lg mb-2">Daftar</h3>
                                                <input type="text" name="nama" placeholder="Nama" class="input input-bordered input-md w-full" required />
                                                <select name="jk" class="select select-bordered w-full" required>
                                                    <option disabled selected value="">Jenis Kelamin</option>
                                                    <option value="Pria">Pria</option>
                                                    <option value="Wanita">Wanita</option>
                                                </select>
                                                <input type="text" name="alamat" placeholder="Alamat" class="input input-bordered input-md w-full" required />
                                                <input type="text" name="username" placeholder="Username" class="input input-bordered input-md w-full" required />
                                                <input type="email" name="email" placeholder="E-mail" class="input input-bordered input-md w-full" required />
                                                <input type="password" name="password" placeholder="Password" class="input input-bordered input-md w-full" required />
                                                <input type="tel" name="nohp" placeholder="No. HP" class="input input-bordered input-md w-full" required />
                                                <label class="form-control w-full max-w-xs">
                                                    <div class="label">
                                                        <span class="label-text">Foto Profil</span>
                                                    </div>
                                                    <input type="file" accept="image/png, image/jpeg" name="pfp" id="gambar" class="file-input file-input-bordered w-full max-w-xs" required />
                                                </label>

                                                <button type="submit" name="daftar" class="btn bg-slate-300">Daftar</button>
                                            </form>
                                            <form method="dialog">
                                                <p>Sudah punya akun? <button onclick="login.showModal()">Login disini</button></p>
                                            </form>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>