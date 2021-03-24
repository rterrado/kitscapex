<?php

    require_once 'eng/autoloader.eng.php';

    class Builder  {

        private $template;
        private $builderData;
        private $themePath;

        public function __construct($pageName){
            $theme = "muffins";
            $indexPath = $_SERVER["DOCUMENT_ROOT"]."/version/cupcake/public/themes/".$theme."/index.html";
            $themePath = $_SERVER["DOCUMENT_ROOT"]."/version/cupcake/public/themes/".$theme;
            $this->themePath = $themePath;
            if (file_exists($indexPath)) {
                $this->template = file_get_contents($indexPath);
            }
        }

        public function set_builder($dataKey, $data){
            $this->builderData[$dataKey] = $data;
        }

        public function display(){
            $template= new Template($this->themePath);
            if ($template->render($this->builderData, $this->template) == "ok"){

            }
            else {
                echo "<!--nodata-->";
            }
        }

    }
