<?php
    include(__DIR__ . '/../db/db_commons.php');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (isset($_GET['excludeList']) && $_GET['excludeList'] == "true") {
            echo json_encode(fetch_items_list_excluded());
        } else {
            echo json_encode(fetch_list());
        }
        header('Content-Type: application/json');
    } else {
        not_found();
    }
    die();
?>