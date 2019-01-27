<?php
    include(__DIR__ . '/db/db_commons.php');
    include_once(__DIR__ . '/templates/template.php');
    $result = fetch_list();
    $amount = '';
    $name = '';

    if (isset($_POST['name']) && isset($_POST['amount'])) {
            
        $amount = intval($_POST['amount']);    
        $name = $_POST['name'];

        $hasError = false;
        if (!preg_match('/^[0-9]+$/', $_POST['amount']) || $amount < 1) {
            $hasError = true;
        } else {
            try {
                add_item($name, $amount);
                header("Location: index.php");
            } catch(Exception $e) {
                $hasError = true;
            }
        }

        if ($hasError == true) {
            $result['errors'][] = "Unable to add item " . $name . " into the list.";
        }
    }
?>

<div id='shopping-cart'>

    <div id='shopping-cart-content'>
    
    <div id='items'>
    
        <?php foreach($result['items'] as $index => $item) {

            $itemTemplate = new Template("templates/item.html");
            $itemTemplate->set("name", htmlspecialchars($item->name));
            $itemTemplate->set("quantity", htmlspecialchars($item->amount));
            echo $itemTemplate->output();
        }
        ?>
        </div>

        <div id='add-item-form'>
        <?php 
            
            $formTemplate = new Template("templates/add_item_form.html");
            $formTemplate->set("name", htmlspecialchars($name));
            $formTemplate->set("amount", htmlspecialchars($amount));
            echo $formTemplate->output();
        ?>
        </div>
    </div>

    <div id='errors-wrap' data-empty=<?php if(count($result['errors']) == 0) echo '"true"'; else echo '"false"'; ?>>
        <input type=button id='clear-errors-button' value='Clear errors'/>
        <div id='errors'>
            <?php foreach($result['errors'] as $index => $error) {

                $errorTemplate = new Template("templates/error.html");
                $errorTemplate->set("error", htmlspecialchars($error));
                echo $errorTemplate->output();
            }
        ?>
        </div>
    </div>
</div>