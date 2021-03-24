<?php

    /**
    * NOTE: This is a prototype Users import. Imports should be designed to retrieved data using the
    * parameter passed to the __construct.
    */

    class Users {

        private $userdata;

        public function __construct($userID){

            //some query logic in here


            $this->userdata = array (
                "userid"=>"587745",
                "first_name"=>"Ken",
                "last_name"=>"Terrado",
                "address"=>array(
                    "city"=>"Cebu"
                ),
                "posts"=>array(
                    "612361231",
                    "8717238123",
                    "716236123"
                )
            );

        }

        public function get(){
            //return "nodata";
            return $this->userdata;
        }

    }
