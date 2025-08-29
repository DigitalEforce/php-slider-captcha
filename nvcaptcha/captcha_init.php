<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['slider_captcha']) || time() - $_SESSION['slider_captcha']['created'] > 120) {
    $_SESSION['slider_captcha'] = [
        'target' => rand(25, 75),                // target position (percent)
        'token'  => bin2hex(random_bytes(16)),   // CSRF-like token
        'created'=> time(),
        'fails'  => 0                            // track attempts
    ];
}
