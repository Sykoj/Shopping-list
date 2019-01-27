<?php
    include(__DIR__ . '/../error.php');
    include(__DIR__ . '/../db/db_commons.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['name']) && isset($_POST['amount'])) {
            
            $amount = intval($_POST['amount']);    
            $name = $_POST['name'];

            if (!preg_match('/^[0-9]+$/', $_POST['amount']) || $amount < 1) {
                error(400, 'Property \'amount\' is not a positive integer.');
            } else {
                try {
                    add_item($name, $amount);
                } catch(Exception $e) {
                    $errorMessage = $e->getMessage() != '' ? 
                        $e->getMessage() : 'Failed to execute query on external DB.';
                    error(500, $errorMessage);
                }
            }
        } else {
            error(400, 'Parameters in body missing.');
        }

    } else {
        not_found();
    }
    die();
?>