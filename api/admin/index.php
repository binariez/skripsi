<?php
// Nyalakan output buffering supaya aman dari output sebelum header
ob_start();

require_once __DIR__ . '/../functions/Sessions.php';
require_once __DIR__ . '/func/CRUD.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['UserLogin'])) {
    header('Location: ../');
    exit;
}

$role = ($_SESSION['UserLogin'][0]['role']);
// Cek apakah role user pelanggan. Jika iya, alihkan ke halaman utama.
if ($role === 'PLG') {
    header('Location: ../');
    exit;
}

// HAPUS VOUCHER
if (isset($_GET['hapus_vouch'])) {
    hapusVouch($_GET['id']);
}

// UPDATE PASSWORD ADMIN
if (isset($_POST['update_admin'])) {
    updateAdmin();
}

// LAPORAN
if (isset($_GET['i']) && $_GET['i'] == 'lap'):
    function getTransaksi($periode, $tanggal, $koleksi)
    {
        $start = null;
        $end = null;

        switch ($periode) {
            case 'harian':
                $start = new MongoDB\BSON\UTCDateTime(strtotime($tanggal . ' 00:00:00') * 1000);
                $end = new MongoDB\BSON\UTCDateTime(strtotime($tanggal . ' 23:59:59') * 1000);
                $filter = ['created_at' => ['$gte' => $start, '$lte' => $end], 'paket_status' => 'SELESAI'];
                break;
            case 'mingguan':
                $tgl = strtotime($tanggal);
                $start = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d', strtotime('last sunday', $tgl))) * 1000);
                $end = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d', strtotime('next saturday', $tgl))) * 1000);
                $filter = ['created_at' => ['$gte' => $start, '$lte' => $end], 'paket_status' => 'SELESAI'];
                break;
            case 'bulanan':
                $year = date('Y', strtotime($tanggal));
                $month = date('m', strtotime($tanggal));
                $start = new MongoDB\BSON\UTCDateTime(strtotime("$year-$month-01 00:00:00") * 1000);
                $end = new MongoDB\BSON\UTCDateTime(strtotime(date("Y-m-t 23:59:59", strtotime($tanggal))) * 1000);
                $filter = ['created_at' => ['$gte' => $start, '$lte' => $end], 'paket_status' => 'SELESAI'];
                break;
            default:
                $filter = [];
        }

        return $koleksi->find($filter, ['sort' => ['created_at' => -1]]);
    }

    // Ambil filter periode
    $periode = $_GET['periode'] ?? 'harian';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $koleksi = $db->transaksi;

    if (isset($_GET['ajax'])) {
        $periode = $_GET['periode'] ?? 'harian';
        $tanggal = $_GET['tanggal'] ?? date('d-m-Y');
        $transaksi = getTransaksi($periode, $tanggal, $koleksi);

        $rows = '';
        $no = 1;
        $totalSemua = 0;
        $totalQty = 0; // <-- untuk akumulasi qty semua produk

        foreach ($transaksi as $trx) {
            $userFind = $db->user->findOne(['_id' => $trx->plg_id]);
            $user = ucwords($userFind['user_nama']);

            foreach ($trx->items as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                $totalSemua += $subtotal;
                $totalQty += $item['qty']; // tambahin jumlah pcs ke total

                // simpan ke ringkasan produk
                if (!isset($produkSummary[$item['nama_prod']])) {
                    $produkSummary[$item['nama_prod']] = 0;
                }
                $produkSummary[$item['nama_prod']] += $item['qty'];

                $rows .= '<tr>';
                $rows .= '<td>' . $no++ . '</td>';
                $rows .= '<td>' . date('d-m-Y H:i', strtotime($trx->trx_tgl)) . '</td>';
                $rows .= '<td>' . $trx->orderid . '</td>';
                $rows .= '<td>' . ucwords($user) . '</td>';
                $rows .= '<td>' . ucwords($item['nama_prod']) . '</td>';
                $rows .= '<td class="text-end">' . $item['qty'] . '</td>';
                $rows .= '<td class="text-end">Rp' . number_format($item['harga']) . '</td>';
                $rows .= '<td class="text-end">Rp' . number_format($subtotal) . '</td>';
                $rows .= '</tr>';
            }
        }

        if ($no == 1) {
            $rows = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
        } else {
            // tambahkan row total di footer
            $rows .= '<tr class="table-dark">';
            $rows .= '<td colspan="5" class="text-end"><b>Total</b></td>';
            $rows .= '<td class="text-end"><b>' . $totalQty . '</b></td>'; // total pcs
            $rows .= '<td></td>';
            $rows .= '<td class="text-end"><b>Rp' . number_format($totalSemua) . '</b></td>'; // total nominal
            $rows .= '</tr>';
        }


        // buat ringkasan list produk
        $ringkasan = '<p><b>Ringkasan Produk Terjual:</b></p><ol>';
        if (!empty($produkSummary)) {
            // urutkan dari yang paling laris
            arsort($produkSummary);
            foreach ($produkSummary as $nama => $qty) {
                $ringkasan .= '<li>' . ucwords($nama) . ' : <b>' . $qty . ' pcs</b></li>';
            }
        } else {
            $ringkasan .= '<li>Tidak ada produk terjual</li>';
        }
        $ringkasan .= '</ol>';

        echo json_encode([
            'rows' => $rows,
            'ringkasan' => $ringkasan
        ]);
        exit;
    }


endif;
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin</title>
    <link rel="icon" href="../../public/logo.png">
    <!-- data tables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <!-- DATEPICKER -->
    <link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.id.min.js" charset="UTF-8"></script>
</head>

<body>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <a class="ms-5" href="index.php">
                    <span><img style="width: 150px; height: 147px;" src="../../public/logo.png" alt="Logo"></span>
                </a>
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>

                <!-- sidebar -->
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>
                        <li class="sidebar-item">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <?php if ($role === 'admin'): ?>
                            <li class="sidebar-item">
                                <a href="?i=trans" class='sidebar-link'>
                                    <i class="bi bi-check-square-fill"></i>
                                    <span>Transaksi & Pesanan</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="?i=prod" class='sidebar-link'>
                                    <i class="bi bi-cake-fill"></i>
                                    <span>Manajemen Produk</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="?i=vouch" class='sidebar-link'>
                                    <i class="bi bi-gift-fill"></i>
                                    <span>Manajemen Voucher</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="?i=live" class='sidebar-link'>
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span>Live Chat</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="?i=plg" class='sidebar-link'>
                                    <i class="bi bi-person-fill"></i>
                                    <span>Pelanggan</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="?i=lap" class='sidebar-link'>
                                    <i class="bi bi-table"></i>
                                    <span>Laporan</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($role === 'owner'): ?>
                            <li class="sidebar-item">
                                <a href="?i=adm" class='sidebar-link'>
                                    <i class="bi bi-lock-fill"></i>
                                    <span>Manajemen Admin</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="sidebar-item">
                            <a href="../logout.php" class='sidebar-link'>
                                <i class="bi bi-arrow-left-square-fill"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end sidebar -->

            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <?php
                if (!isset($_GET['i'])) {
                    include_once "pages/v_dasbor.php";
                } else {
                    switch ($_GET['i']) {
                        case "plg":
                            include_once "pages/v_pelanggan.php";
                            break;
                        case "prod":
                            include_once "pages/v_produk.php";
                            break;
                        case "trans":
                            include_once "pages/v_transaksi.php";
                            break;
                        case "vouch":
                            include_once "pages/v_voucher.php";
                            break;
                        case "lap":
                            include_once "pages/v_lap.php";
                            break;
                        case "adm":
                            include_once "pages/v_admin.php";
                            break;
                        case "live":
                            include_once "pages/v_livechatAdmin.php";
                            break;
                        default:
                            header("Location: index.php");
                            exit;
                    }
                }
                ?>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted text-center">
                    <p>2025 &copy; Skripsi Azhar</p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/js/app.js"></script>
</body>

</html>
<?php
ob_end_flush();
?>