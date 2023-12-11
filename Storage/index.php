<?php
include '../conn.php';
require_once '../function.php';
header('Content-Type: application/json');

http_response_code(200);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_COOKIE['User'])) {
    $decCookie = decryptCookie($_COOKIE['User'], 'SemogaBerkah');
    $decCookie = explode('-',$decCookie);
    $get = mysqli_query($conn, "SELECT id FROM user WHERE email = '$decCookie[0]'");
    $id = "";
    if ($get) {
        $id_data = mysqli_fetch_assoc($get);
        $id = $id_data['id'];
        mysqli_free_result($get);
    } else {
        echo "Error in query: " . mysqli_error($conn);
    }
    // mysqli_close($conn);

    if (isset($_POST['req']) && $_POST['req'] == 'getData') {
        $query = mysqli_query($conn, "SELECT id,judul,data,prev_data FROM note WHERE user_id = $id");
        $data = mysqli_fetch_all($query,MYSQLI_ASSOC);

        echo json_encode(['Data'=>$data],JSON_PRETTY_PRINT);
    }elseif (isset($_POST['req']) && $_POST['req'] == 'updateData' && isset($_POST['judul']) && isset($_POST['data']) && isset($_POST['idNote'])) {
        $stmt = mysqli_prepare($conn, "UPDATE note SET judul = ?, data = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssd', $_POST['judul'], $_POST['data'], $_POST['idNote']);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil edit note');
            echo json_encode($response,JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal mengedit note');
            echo json_encode($response,JSON_PRETTY_PRINT);
        }

        mysqli_stmt_close($stmt);
    }elseif (isset($_POST['req']) && $_POST['req'] == 'deleteData' && isset($_POST['idNote'])) {
        $stmt = mysqli_prepare($conn,"DELETE FROM note WHERE id = ?");
        mysqli_stmt_bind_param($stmt,'d', $_POST['idNote']);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil menghapus note');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal menghapus note');
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }elseif (isset($_POST['req']) && $_POST['req'] == 'addData' && isset($_POST['judul']) && isset($_POST['data'])) {
        $stmt = mysqli_prepare($conn,"INSERT INTO note (user_id,judul,data) VALUE (?,?,?)");
        mysqli_stmt_bind_param($stmt,'dss',$id, $_POST['judul'], $_POST['data']);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('Info' => 'Berhasil menambahkan note');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $response = array('Info' => 'Gagal menambahkan note');
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
} else {
    echo json_encode(['Error' => 'Request Error '], JSON_PRETTY_PRINT);
}