<?php

function acquire_mysqli_connection() {
    
    include('db_config.php');
    return new mysqli($db_config['server'], $db_config['login'], $db_config['password'], $db_config['database']);
}
?>