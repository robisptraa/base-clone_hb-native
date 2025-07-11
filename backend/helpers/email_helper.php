<?php
require_once __DIR__ . '/constants.php';

function sendVerificationEmail($email, $verificationCode) {
    $subject = "Verifikasi Email Anda";
    $verificationLink = BASE_URL . "/verify.php?code=$verificationCode";
    
    $message = "
        <html>
        <body>
            <h2>Verifikasi Email</h2>
            <p>Silakan klik link berikut untuk verifikasi email:</p>
            <a href='$verificationLink'>Verifikasi Sekarang</a>
            <p>Atau copy link ini ke browser Anda:</p>
            <p>$verificationLink</p>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@hbndesign.com" . "\r\n";

    return mail($email, $subject, $message, $headers);
}
?>