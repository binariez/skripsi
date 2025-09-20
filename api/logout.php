<?php
require_once 'functions/SessionHandlerInterface.php';
session_start();
session_destroy();
header("Location: index.php");
