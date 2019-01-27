<?php
    function add_item($name, $quantity) {
        
        @add_to_items_if_not_present($name);
        $itemId = @get_id_of_item($name);
        $nextPosition = @get_max_position_in_items() + 1;
        @insert_to_list($itemId, $nextPosition, $quantity);
    }

    function remove_item($name) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();

        try {
            if 
            ($connection->connect_error ||
            !($stmt = $connection->prepare('DELETE FROM list 
                                            WHERE item_id = (SELECT id FROM items AS i WHERE i.name = (?))')) ||
            !$stmt->bind_param('s', $name) ||
            !$stmt->execute()) {
                throw new Exception("Unable to delete item from the list in external DB.");
            }
        } catch (Exception $e) {
            error(500, $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    function fetch_list() {
        
        $query = "SELECT i.name, i.id, l.amount, l.position as id, l.position
                  FROM `list` AS l
                  INNER JOIN `items` AS i ON l.item_id = i.id
                  ORDER BY l.position";
        return @fetch($query);
    }

    function fetch_items_list_excluded() {
        
        $query = "SELECT i.name, i.id, l.amount, l.position as id 
                  FROM `items` AS i LEFT JOIN `list` AS l
                  ON i.id = l.item_id 
                  WHERE l.item_id IS NULL";
        return @fetch($query);
    }

    function insert_to_list($itemId, $position, $amount) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();
        
        try {
            if 
            ($connection->connect_error ||
            !($stmt = $connection->prepare(
                "INSERT INTO `list` (`amount`,`item_id`,`position`)
                 VALUES (?,?,?)")) ||
            !$stmt->bind_param('iii', $amount, $itemId, $position) ||
            !$stmt->execute()) {
                throw new Exception("Unable to insert new item into the list in external DB.");
            }
        } finally {
            $connection->close();
        }
    }

    function add_to_items_if_not_present($name) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();
        
        try {

        if
        ($connection->connect_error ||
        !($stmt = $connection->prepare(
            "SELECT COUNT(*) AS cnt 
             FROM `items` AS i 
             WHERE i.name = ?")) ||
        !$stmt->bind_param('s', $name) ||
        !$stmt->execute()) {
            throw new Exception("Unable to fetch data from items in external DB.");
        }

        if ($stmt->get_result()->fetch_array()['cnt'] != 0) {
            return;
        }

        if
        ($connection->connect_error ||
        !($stmt = $connection->prepare(
            "INSERT INTO `items` (`name`) 
             VALUES (?)")) ||
        !$stmt->bind_param('s', $name) ||
        !$stmt->execute()) {
            throw new Exception("Unable to insert item into items in external DB.");
        }

        } finally {
            $connection->close();
        }
    }

    function get_id_of_item($name) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();
    
        if
        ($connection->connect_error ||
        !($stmt = $connection->prepare("SELECT * 
                                        FROM `items` 
                                        WHERE `name` = (?)")) ||
        !$stmt->bind_param('s', $name) ||
        !$stmt->execute()) {
            $connection->close();
            throw new Exception();
        }
        
        $id = $stmt->get_result()->fetch_array()['id'];
        $connection->close();
        return $id;
    }

    function get_max_position_in_items() {

        try {

        $connection = acquire_mysqli_connection();
        if ($connection->connect_error || 
            !($result = $connection->query(
                "SELECT MAX(l.position) AS position
                FROM `list` AS l"))) {
                
            throw new Exception("Unable to fetch data from items in external DB.");
        } else {
            $row = $result->fetch_assoc();
            return $row['position'];
        }

        } finally {
            $connection->close();
        }
    }

    function fetch($query) {

        include_once(__DIR__ . '/db_connection.php');
        include_once(__DIR__ . '/../item.php');
    
        $errors = [];
        $items = [];
    
        $connection = acquire_mysqli_connection();
        $hasErrors = false;
        
        if (!$connection->connect_error && ($result = $connection->query($query))) {
    
            while($row = $result->fetch_assoc()) {
                $items[] = new Item($row['name'], $row['amount'], $row['position'], $row['id']);
            }
        } else {
            $hasErrors = true;
        }   
    
        if ($hasErrors) {
            http_response_code(500);
            $errors[] = "Unable to fetch list of items to the shopping cart.";
            $items = [];
        }
        
        $result = [];
        $result['items'] = $items;
        $result['errors'] = $errors;
        return $result;
    }

    function edit_amount($name, $quantity) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();

        try {
            if 
            ($connection->connect_error ||
            !($stmt = $connection->prepare('UPDATE list 
                                            SET amount = (?) 
                                            WHERE item_id = (SELECT id FROM items AS i WHERE i.name = (?))')) ||
            !$stmt->bind_param('is', $quantity, $name) ||
            !$stmt->execute()) {
                throw new Exception("Unable to edit item amount for item in list in external DB.");
            }
        } catch (Exception $e) {
            error(500, $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    function swap_items($firstName, $secondName) {

        include_once(__DIR__ . '/db_connection.php');
        $connection = acquire_mysqli_connection();

        $positions = [];
        $errorMessage = "Unable to swap item positions in external DB.";

        try {
            if 
            ($connection->connect_error ||
            !($stmt = $connection->prepare('SELECT item_id AS id, position 
                                            FROM list 
                                            WHERE item_id = (SELECT id FROM items WHERE (?) = name)')) ||
            !$stmt->bind_param('s', $firstName) ||
            !$stmt->execute()) {
                throw new Exception($errorMessage);
            } else {
                $row = $stmt->get_result()->fetch_array();
                $positions[0] = [];
                $positions[0]['id'] = $row['id'];
                $positions[0]['position'] = $row['position'];
            }

            if 
            ($connection->connect_error ||
            !($stmt = $connection->prepare('SELECT item_id AS id, position 
                                            FROM list 
                                            WHERE item_id = 
                                            (SELECT id FROM items WHERE (?) = name)')) ||
            !$stmt->bind_param('s', $secondName) ||
            !$stmt->execute()) {
                throw new Exception($errorMessage);
            } else {
                $row = $stmt->get_result()->fetch_array();
                $positions[1] = [];
                $positions[1]['id'] = $row['id'];
                $positions[1]['position'] = $row['position'];
            }

            if
            ($connection->connect_error ||
            !($stmt = $connection->prepare('UPDATE list SET position = CASE
                                            WHEN item_id = (?) THEN (?) 
                                            WHEN item_id = (?) THEN (?)
                                            END WHERE item_id IN ((?), (?))')) ||
            !$stmt->bind_param('iiiiii', $positions[0]['id'], $positions[1]['position'],
                                         $positions[1]['id'], $positions[0]['position'],
                                         $positions[0]['id'], $positions[1]['id']) ||
            !$stmt->execute()) {
                throw new Exception($errorMessage);
            }
        }
        catch (Exception $e) {
            error(500, $e->getMessage());
        } finally {
            $connection->close();
        }
    }
?>