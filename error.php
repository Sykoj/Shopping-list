<?php  
    class Error {

        public $message;

        function __construct($message) {
            $this->message = $message;
        } 
    }

    function error($code, $message) {
        http_response_code($code);
        echo json_encode(new Error($message));
    }

    function not_found() {
        error(404,'Not found');
    }
?>