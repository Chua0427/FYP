<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=verosports',  'root',  '');
    echo "数据库连接成功！";
} catch (PDOException $e) {
    die("连接失败: " . $e->getMessage());
}