<?php 

    class Item {

        public $name;
        public $position;
        public $item_id;
        public $amount;

        function __construct($name, $amount, $position, $item_id) {
            $this->name = $name;
            $this->amount = $amount;
            $this->position = $position;
            $this->item_id = $item_id;
        }
    }
?>