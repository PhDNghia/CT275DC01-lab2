<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_administrator(string $user = "me"): bool
{
    return isset($_SESSION['user']) && $_SESSION['user'] === $user;
}

function ensure_admin_access(): bool
{
    if (is_administrator()) {
        return true;
    }

    http_response_code(403);

    return false;
}

function get_database_connection()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'pgsql:host=localhost;port=5432;dbname=ct275_lab2;';
        $username = 'postgres';
        $password = '1029384756'; // Thay bằng mật khẩu tài khoản postgres của bạn khi cài đặt

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Kết nối CSDL thất bại: ' . $e->getMessage());
        }
    }

    return $pdo;
}

function html_escape(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
}

function render_page_header(): void
{
    $title = defined('TITLE') ? TITLE : 'Trang các Trích dẫn';
?>
    <!doctype html>
    <html lang="vi">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" media="all" href="css/style.css">
        <title><?= html_escape($title) ?></title>
    </head>

    <body>
        <div id="container">
            <h1>Trang các Trích dẫn</h1>
            <br>
            <!-- BEGIN CHANGEABLE CONTENT. -->
        <?php
    }
