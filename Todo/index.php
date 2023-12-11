<?php
include '../conn.php';
require_once '../function.php';
header('Content-Type: application/json');

http_response_code(200);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_COOKIE['User'])) {
    $decCookie = decryptCookie($_COOKIE['User'], 'SemogaBerkah');
    $decCookie = explode('-', $decCookie);
    $get = mysqli_query($conn, "SELECT id FROM user WHERE email = '$decCookie[0]'");
    $id = "";
    if ($get) {
        $id_data = mysqli_fetch_assoc($get);
        $id = $id_data['id'];
        mysqli_free_result($get);
    } else {
        echo "Error in query: " . mysqli_error($conn);
    }

    if (isset($_POST['req']) && $_POST['req'] == 'getData') {
        $query = mysqli_query($conn, "SELECT id,data,status FROM todo_list WHERE user_id = $id");
        $data = mysqli_fetch_all($query, MYSQLI_ASSOC);

        echo json_encode(['Data' => $data], JSON_PRETTY_PRINT);
    } elseif (isset($_POST['req']) && $_POST['req'] == 'updateData' && isset($_POST['idTodo'])) {
        $stmt;
        if (isset($_POST['data'])) {
            $stmt = mysqli_prepare($conn, "UPDATE todo_list SET data = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $_POST['data'], $_POST['idTodo']);
        }else{
            $stmt = mysqli_prepare($conn, "UPDATE todo_list SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $_POST['status'], $_POST['idTodo']);
        }

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil edit todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal mengedit todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        }

        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['req']) && $_POST['req'] == 'deleteData' && isset($_POST['idTodo'])) {
        $stmt = mysqli_prepare($conn, "DELETE FROM todo_list WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'd', $_POST['idTodo']);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil menghapus todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal menghapus todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    } elseif (isset($_POST['req']) && $_POST['req'] == 'addData' &&isset($_POST['data']) && isset($_POST['status'])) {
        $stmt = mysqli_prepare($conn, "INSERT INTO todo_list (user_id,data,status) VALUE (?,?,?)");
        mysqli_stmt_bind_param($stmt, 'dss', $id, $_POST['data'], $_POST['status']);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil menambahkan todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal menambahkan todo list');
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
}else{
    echo json_encode(['Error' => 'Request Error '], JSON_PRETTY_PRINT);
}