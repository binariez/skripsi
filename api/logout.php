<?php
require_once 'functions/SessionHandlerInterface.php';
session_start();
if (session_destroy()) {
    header("Location: index.php");
} else {
    echo "logout gagal";
}
