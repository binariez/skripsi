<?php
require_once "functions/Template.php";
require_once "functions/Connection.php";

if (isset($_POST['txtusername']) && isset($_POST['txtpassword'])) {
    $uname = strtolower($_POST['txtusername']);
    $pwd = $_POST['txtpassword'];

    if (NSessionHandler::cekAuth($uname, $pwd)) {
        $datau = NSessionHandler::getDataU($uname);
        foreach ($datau as $key => $val) {
            $id = $val['id'];
            $nama = $val['nama'];
            $uname = $val['uname'];
            $email = $val['email'];
            $alamat = $val['alamat'];
            $role = $val['role'];
            $pfp = $val['pfp'];
        }
        NSessionHandler::setLogin($id, $nama, $uname, $email, $alamat, $role, $pfp);

        if ($role !== 'PLG')
            echo "
        <script>
            alert('SUKSES. role: $role');
            window.location.href='admin';
        </script>";
        else {
            echo "
        <script>
            alert('SUKSES. role: $role');
            window.location.href='index.php';
        </script>";
        }
    } else {
        echo "
        <script>
            alert('GAGAL');
            window.location.href='index.php';
        </script>";
    }
} else {
}
