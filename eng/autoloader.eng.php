<?php

    spl_autoload_register('autoloader');

    function autoloader($className){
        $path = "eng/".$className.".eng.php";
        if (file_exists($path)) {
            require $path;
        }
        else {
            echo "Error: Path do not exists";
        }
    }
