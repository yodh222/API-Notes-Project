<?php
include '../conn.php';
require_once '../function.php';
header('Content-Type: application/json');

http_response_code(200);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['req'] == 'signup') {
        if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password'])) {
            echo json_encode(['Error' => 'Missing Insert Value Parameter'], JSON_PRETTY_PRINT);
        } else {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $pass = $_POST['password'];

            // Check if email is unique
            if(!email($email)) {
                // Insert Data
                $stmt1 = mysqli_prepare($conn, "INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, 'sss', $username, $email, $pass);
                if (!$stmt1) {
                    die('Error in prepare statement: ' . mysqli_error($conn));
                }
                mysqli_stmt_execute($stmt1);

                if (mysqli_stmt_affected_rows($stmt1) > 0) {
                    echo json_encode(['Info' => 'User berhasil ditambahkan'], JSON_PRETTY_PRINT);
                } else {
                    echo json_encode(['Info' => 'Gagal menambahkan user'], JSON_PRETTY_PRINT);
                }
                mysqli_stmt_close($stmt1);  // Close the correct statement
            }else{
                echo json_encode(['Info' => 'Email telah tersedia'],JSON_PRETTY_PRINT);
            }
        }
    }else if($_POST['req'] == 'login'){
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            echo json_encode(['Error' => 'Missing Insert Value Parameter'], JSON_PRETTY_PRINT);
        } else {
            $email = $_POST['email'];
            $pass = $_POST['password'];

            $stmt = mysqli_prepare($conn, "SELECT IF((SELECT COUNT(*) FROM user WHERE email = ? AND password = ?) = 1, 'Anda Berhasil Login', 'Anda Gagal Login')");
            mysqli_stmt_bind_param($stmt, 'ss', $email, $pass);

            if (!$stmt) {
                die(json_encode(['Error' => 'Error in prepare statement: ' . mysqli_error($conn)], JSON_PRETTY_PRINT));
            }

            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $res);
            mysqli_stmt_fetch($stmt);

            if ($res == 'Anda Berhasil Login') {
                $session = encryptCookie($email, $pass, 'SemogaBerkah');
                echo json_encode(['Success' => 'User login successful','Session' => $session], JSON_PRETTY_PRINT);
            } else {
                echo json_encode(['Session' => 'User login Failed'], JSON_PRETTY_PRINT);
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        echo json_encode(['Error' => 'Argument Error'], JSON_PRETTY_PRINT);
    }

}
else {
    echo json_encode(['Error' => 'Request Error'], JSON_PRETTY_PRINT);
}
