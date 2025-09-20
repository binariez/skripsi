<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Laporan Penjualan</h3>
        </div>
    </div>
</div>

<section id="basic-vertical-layouts" class="flex-shrink-0">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row g-3 mb-3 no-print">
                            <div class="col-auto">
                                <select id="periode" class="form-select">
                                    <option value="harian">Harian</option>
                                    <option value="mingguan">Mingguan</option>
                                    <option value="bulanan">Bulanan</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <input type="date" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-auto">
                                <button id="btnTampil" class="btn btn-primary">Tampilkan</button>
                                <button type="button" onclick="window.print()" class="btn btn-success">Cetak</button>
                            </div>
                        </div>

                        <!-- HEADER CETAK -->
                        <div class="kop-surat d-none d-print-block mb-3">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 100px; text-align: center;">
                                        <img src="../../public/logo.png" alt="Logo Nafisah Bread" style="height: 100px;">
                                    </td>
                                    <td style="text-align: center;">
                                        <p class="text-black fs-5" style="margin: 0; font-size: 22px;">Nafisah Bread & Cake</p>
                                        <p style="margin: 0; font-size: 12px;">Jl. Budi Utomo Siumbut Baru, Kabupaten Asahan | 08137482712</p>
                                        <p style=" margin: 0; font-size: 12px;">Laporan Penjualan |
                                            Periode: <span id="periodeText"></span></p>
                                    </td>
                                </tr>
                            </table>
                            <hr style="border: 2px solid #000; margin-top: 10px;">
                        </div>


                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>No. Faktur</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <tr>
                                    <td colspan="8" class="text-center">Silakan pilih periode dan klik Tampilkan</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Ringkasan -->
                        <div id="ringkasan" class="mt-3"></div>
                        <style>
                            @media print {

                                /* Reset body dan html agar tidak ada margin/padding yang mendorong isi */
                                body,
                                html {
                                    margin: 0;
                                    padding: 0;
                                    height: auto;
                                }

                                /* Sembunyikan elemen yang tidak dicetak */
                                .no-print {
                                    display: none !important;
                                }

                                /* Reset semua container Flex/Bootstrap agar tabel dan kop surat langsung di atas */
                                .row,
                                .col-12,
                                .card,
                                .card-content,
                                .card-body,
                                section,
                                .page-title {
                                    display: block !important;
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    float: none !important;
                                }

                                /* Kop surat + logo */
                                .kop-surat {
                                    display: block !important;
                                    position: relative;
                                    /* bisa diubah ke absolute jika ingin menempel top halaman */
                                    top: 0;
                                    margin-bottom: 10px;
                                    /* jarak ke tabel */
                                    text-align: center;
                                }

                                .kop-surat img {
                                    height: 120px;
                                    /* atur tinggi logo */
                                }

                                /* Tabel laporan */
                                table {
                                    display: table !important;
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-size: 11px;
                                    /* ukuran font tabel cetak */
                                    margin-top: 0;
                                    /* langsung di bawah kop surat */
                                }

                                table th,
                                table td {
                                    padding: 4px 6px !important;
                                    white-space: nowrap;
                                    /* cegah teks turun ke baris berikut */
                                    text-align: left;
                                }

                                /* Ringkasan produk */
                                #ringkasan {
                                    font-size: 12px;
                                    line-height: 1.3;
                                    display: block !important;
                                    margin-top: 5px;
                                }

                                #ringkasan ul {
                                    padding-left: 20px;
                                    margin: 0;
                                }

                                /* Footer cetak */
                                .d-print-block.text-end {
                                    font-size: smaller;
                                    display: block !important;
                                    text-align: right;
                                    margin-top: 20px;
                                }

                                /* Pastikan tabel, kop surat, ringkasan tampil */
                                .table,
                                #ringkasan,
                                .kop-surat {
                                    display: block !important;
                                    visibility: visible;
                                }
                            }
                        </style>

                        <?php

                        use Carbon\Carbon;

                        setlocale(LC_TIME, 'id_ID');
                        Carbon::setLocale('id');

                        $tanggal = Carbon::now()->translatedFormat('l, d F Y');
                        ?>

                        <!-- FOOTER CETAK -->

                        <div style="display: flex; justify-content: flex-end;">
                            <div class=" mt-4 d-none d-print-block" style="font-size: smaller; ">
                                <div style=" text-align: left;">
                                    <p style="margin: 0;">Kisaran, <?= $tanggal ?></p>
                                    <p style="margin: 0;">Pemilik</p>
                                    <br><br>
                                    <p style="margin: 0;">_________________________</p>
                                    <p style="margin: 0;">Riko Prasetyo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .card,
        .card * {
            visibility: visible;
        }

        .no-print {
            display: none !important;
        }

        table {
            font-size: 11px;
            /* perkecil font tabel */
        }

        table th,
        table td {
            padding: 4px 6px !important;
            /* rapatkan sel */
            white-space: nowrap;
            /* cegah teks turun ke baris berikut */
        }
    }
</style>

<script>
    document.getElementById('btnTampil').addEventListener('click', function() {
        let periode = document.getElementById('periode').value;
        let tanggal = document.getElementById('tanggal').value;

        // format tanggal ke d-m-Y
        function formatTanggal(tgl) {
            if (!tgl) return "";
            let d = new Date(tgl);
            let day = String(d.getDate()).padStart(2, '0');
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        let tglFormatted = formatTanggal(tanggal);

        fetch(`index.php?i=lap&ajax=1&periode=${periode}&tanggal=${tanggal}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('table-body').innerHTML = data.rows;
                document.getElementById('ringkasan').innerHTML = data.ringkasan;

                // Update header cetak
                let periodeText = "";
                switch (periode) {
                    case "harian":
                        periodeText = "Harian (" + tglFormatted + ")";
                        break;
                    case "mingguan":
                        periodeText = "Mingguan (minggu dari " + tglFormatted + ")";
                        break;
                    case "bulanan":
                        let bulanTahun = tanggal.substr(0, 7).split("-");
                        periodeText = "Bulanan (" + bulanTahun[1] + "-" + bulanTahun[0] + ")";
                        break;
                }
                document.getElementById('periodeText').innerText = periodeText;
            })
            .catch(err => console.error(err));

    });
</script>