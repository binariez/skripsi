<?php
require_once __DIR__ . '/functions/Sessions.php';
NSessionHandler::logout();
header("Location: index.php");
