<?php
function encryptCookie($value1,$value2, $key)
{
    $cipher = "AES-256-CBC";
    $options = 0;
    $value =  $value1 .'-'. $value2;
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted = openssl_encrypt($value, $cipher, $key, $options, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptCookie($value, $key)
{
    $cipher = "AES-256-CBC";
    $options = 0;
    $value = base64_decode($value);
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = substr($value, 0, $iv_length);
    $encrypted = substr($value, $iv_length);
    return openssl_decrypt($encrypted, $cipher, $key, $options, $iv);
}
function email($email){
    include 'conn.php';
    $stmt = mysqli_prepare($conn, 'SELECT email FROM user WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);  // Store the result to check the number of rows
    if (mysqli_stmt_num_rows($stmt) > 0) {
        return true;
    }
    return false;
}