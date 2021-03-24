<?php

/**
* NOTE: This is a prototype Users import. Imports should be designed to retrieved data using the
* parameter passed to the __construct.
*/

    class Posts {

        private $postdata;

            //some query logic in here

        public function __construct($postID){
            if ($postID == 8717238123) {
                $this->postdata = array (
                    "post_id"=>$postID,
                    "post_title"=>"Lorem ipsum dolor sit amet",
                    "post_thumbnail"=>"https://images.unsplash.com/photo-1616464598523-b66b1cd98ffb?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1283&q=80",
                    "post_description"=>"Vestibulum elementum nunc vel nisl volutpat, non maximus dolor consequat. Mauris tincidunt euismod mollis. Suspendisse non erat eget massa accumsan elementum. Donec at egestas lectus, ut interdum odio. Mauris urna sapien, lacinia vitae enim in, sagittis scelerisque tellus. In facilisis rutrum malesuada.",
                    "post_date"=>"February 21, 2021",
                    "post_address"=>array(
                        "city"=>"From Cagayan de Oro"
                    )
                );
            }
            elseif ($postID == 612361231) {
                $this->postdata = array (
                    "post_id"=>$postID,
                    "post_title"=>"Nulla luctus nisi nec feugiat volutpat",
                    "post_thumbnail" => "https://images.unsplash.com/photo-1616405419181-c3b0eca0b433?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80",
                    "post_description"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin molestie vulputate velit, ut fermentum turpis varius at. Fusce varius sollicitudin est, at ultrices ante commodo in. Nam vitae orci eu nulla commodo vehicula. Pellentesque auctor, libero vitae varius tincidunt, libero elit blandit diam, ut pellentesque justo ex et nulla.",
                    "post_date"=>"January 16, 2021",
                    "post_address"=>array(
                        "city"=>"From Cagayan de Oro"
                    )
                );
            }
            else {
                $this->postdata = array (
                    "post_id"=>$postID,
                    "post_title"=>"Aenean elementum viverra neque",
                    "post_thumbnail"=>"https://images.unsplash.com/photo-1593642532842-98d0fd5ebc1a?ixid=MXwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80",
                    "post_description"=>"Curabitur ipsum mi, sollicitudin vel eros a, luctus molestie nunc. Mauris iaculis vulputate laoreet. Vivamus at est pellentesque, efficitur nulla a, sagittis mi.",
                    "post_date"=>"January 28, 2021",
                    "post_address"=>array(
                        "city"=>"From Cagayan de Oro"
                    )
                );
            }

        }

        public function get(){
            //return "nodata";
            return $this->postdata;
        }

    }
