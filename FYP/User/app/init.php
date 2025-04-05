<?php
// app/init.php

// 定义基础路径
define('APP_ROOT', __DIR__ . '/..');

// 初始化日志系统
$log_dir = APP_ROOT . '/logs';
if (!file_exists($log_dir)) {
    try {
        // 创建日志目录
        if (!mkdir($log_dir, 0777, true) && !is_dir($log_dir)) {
            throw new Exception("无法创建日志目录: $log_dir");
        }
        
        // 创建空日志文件
        $files = ['payment.log', 'error.log'];
        foreach ($files as $file) {
            $file_path = "$log_dir/$file";
            if (file_put_contents($file_path, "") === false) {
                error_log("[ERROR] 无法创建日志文件: $file_path", 3, "$log_dir/error.log");
            }
        }
        
        // Windows权限设置
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $user = getenv('USERNAME') ?: 'Everyone';
            $cmd = 'icacls ' . escapeshellarg($log_dir) . " /grant $user:F";
            exec($cmd, $output, $returnCode);
            
            if ($returnCode !== 0) {
                error_log("[WARNING] Windows权限设置失败: " . implode("\n", $output), 3, "$log_dir/error.log");
            }
        }
        
        error_log("[INIT] 日志系统初始化成功\n", 3, "$log_dir/init.log");
        
    } catch (Exception $e) {
        error_log("[FATAL] 初始化失败: " . $e->getMessage(), 3, "$log_dir/error.log");
        die("系统初始化失败，请联系管理员");
    }
}
