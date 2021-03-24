<?php

    class Import {

        private $app;
        private $appError;
        private $requirePath;
        private $appName;

        public function __construct($appName) {
            $server = $_SERVER["DOCUMENT_ROOT"];
            $appPath = $server."/apps/".$appName."/import.php";
            if (file_exists($appPath)) {
                $this->appName = $appName;
                $this->requirePath = $appPath;
                $this->appError = false;
            }
            else {
                echo "Error: The ".$appName." app is either damaged or non-existent.";
                $this->appError = true;
            }
        }

        public function set($params){
            if ($this->appError  === false) {
                require_once $this->requirePath;
                //echo $this->requirePath;
                $import = new $this->appName($params);
                $this->app = $import;
            }
        }

        public function request($request, $params){
            if ($this->appError  === false) {
                return $this->app->$request($params);
            }
        }

    }
