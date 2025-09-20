<?php
require_once 'functions/SessionHandlerInterface.php';
session_start();
if (session_destroy()) {
    echo "logout sukses";
} else {
    echo "logout gagal";
}
// header("Location: index.php");
