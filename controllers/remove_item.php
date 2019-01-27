<?php
    include(__DIR__ . '/../db/db_commons.php');
    include(__DIR__ . '/../error.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = json_decode(file_get_contents('php://input'), true);

        if (isset($_POST['name'])) {
            remove_item($_POST['name']);
        } else {
            not_found();
        }
    } else {
        not_found();
    }
    die();
?>