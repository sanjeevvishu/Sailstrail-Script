<?php

    function isPost() {
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    function isGet() {
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }
?>