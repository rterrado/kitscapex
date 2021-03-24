<?php

  /**
  * Basic Routing Structure
  * NOTE: This routing structure is built for prototyping purposes only.
  * Revision should be made.
  */
  $server = $_SERVER["DOCUMENT_ROOT"];
  $url = explode("/", $_SERVER['REQUEST_URI']);
  if (isset($url[1])) {
    if ($url[1] == "testonly") {
        $assetsPath = $server."/version/cupcake/public/themes/muffins/assets";
        require_once $assetsPath."/".$url[2];
    }
    else {
        $pagePath = $server."/eng/Builder.eng.php";
        if (file_exists($pagePath)) {
          require_once $pagePath;
          $theme = new Builder($url[1]);


          /**
          * NOTE: The set_builder parameters are directly embedded as shown below.
          * In the upcoming revisions, this SHOULD populated by the query parameters
          * in the url
          *
          * EXAMPLE:
          * www.example.com/index.php?id=54525658
          */

          $theme->set_builder("id", "54525658");
          $theme->display();
        }
        else {
          echo "404: Not Found";
        }
    }
  }
  else {
      echo "404: Not Found";
  }


?>
