<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_unset();
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setFlash('info', 'Anda telah berhasil keluar dari sistem.');
header('Location: login.php');
exit;
