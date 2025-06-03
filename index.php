<?php

declare(strict_types=1);

session_start();

// Use correct path for vendor/autoload.php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Use correct path for auth.php
if (file_exists(__DIR__ . '/FYP/User/app/auth.php')) {
    require_once __DIR__ . '/FYP/User/app/auth.php';
} else if (file_exists(__DIR__ . '/User/app/auth.php')) {
    require_once __DIR__ . '/User/app/auth.php';
}

// Check if user is already authenticated
if (class_exists('Auth') && Auth::check()) {
    // User is logged in, redirect to home page
    header('Location: User/HomePage/homePage.php');
    exit;
} else {
    // User is not logged in, redirect to login page
    header('Location: FYP/User/login/login.php');
    exit;
} 