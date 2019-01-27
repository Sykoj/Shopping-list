<?php
    include(__DIR__ . '/../db/db_commons.php');
    include(__DIR__ . '/../error.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = json_decode(file_get_contents('php://input'), true);

        if (isset($_POST['name']) && isset($_POST['amount'])) {
            
            $name = $_POST['name'];
            $amount = intval($_POST['amount']);    
            if (!preg_match('/^[0-9]+$/', $_POST['amount']) || $amount < 1) {
                error(400, 'Property \'amount\' is not positive integer.');
            } else {
                edit_amount($name, $amount);
            }
        } 
        else {
            not_found();
        }
    } else {
        not_found();
    }
    die();
?>