<?php

    /*
    KITSCAPE TEMPLATE ENGINE Version 1.0.0
    Author: Ken Terrado
    */

    require_once 'eng/autoloader.eng.php';

    class Template {

        private $template = array();

        private $themePath;
        // This variable should be sent by the Builder Engine.

        static $dataPassedByBuilderEngine;
        private $templateValues;
        private $dataStoredByTemplateEngine;
        private $importRequestResponseStatus;

        public function __construct($themePath){
          $this->themePath = $themePath;
          $this->importRequestResponseStatus = "ready";
        }



        /*
        =====================================================================
        HERE: RENDER METHODS
        These methods are vital for rendering templates. Here's how they work:
            render() -> this method will be the START and END of the Template
                        Engine rendering. This method contains the "echo" part
                        signifying everything is ready to be printed out.
            render_include_temporarily() -> this method renders the content of
                        an INCLUDED file, converts the Regular Expressions into
                        values accordingly, before replacing the Regular
                        Expression with the content generated.
            render_loop_temporarily() -> this method renders the content of
                        an LOOPED file, converts the Regular Expressions into
                        values accordingly, compiles and loops until the end
                        of the array passed on with the "using" statement,
                        before replacing the Regular Expression.

            VARIABLE DEFINITIONS:
            $actualDataToBePassedToApp -> Actual data to be sent for app __construct
                        method.
            $dataKeyPassedToBeParsedAndSentToApp -> Key reference of the $actualDataToBePassedToApp
                        and template reference.


        ====================================================================
        */

        public function render($actualDataToBePassedToApp, $template){
            $this->dataStoredByTemplateEngine["presets"] = $actualDataToBePassedToApp;
            $this->template["presets"] = $template;
            $this->findEx("presets");
            echo $this->template["presets"];
            return $this->importRequestResponseStatus;
        }


        private function render_include_temporarily($actualDataToBePassedToApp, $dataKeyPassedToBeParsedAndSentToApp, $template){
            $this->dataStoredByTemplateEngine[$dataKeyPassedToBeParsedAndSentToApp] = $actualDataToBePassedToApp;
            $this->template[$dataKeyPassedToBeParsedAndSentToApp] = $template;
            return $this->findEx($dataKeyPassedToBeParsedAndSentToApp);
        }


        private function render_loop($actualDataToBePassedToApp, $expressionKeywordsInArray, $includedTemplateData){
            $dataKeyPassedToBeParsedAndSentToApp = $expressionKeywordsInArray."::".$actualDataToBePassedToApp;
            return $this->findEx($dataKeyPassedToBeParsedAndSentToApp);
        }




        /*
        =====================================================================

        HERE: The following private functions are series of events/commands wherein
        the Template Engine will loop throught the page template looking
        for regular expression contained in {{ }} and replace it with the
        appropriate data until all the regular preg_match_results are replaced.

        ====================================================================
        */

        /*
        METHOD 1: Looking for Regular Expression {{ }}
        */
        private function findEx($dataKeyPassedToBeParsedAndSentToApp){
            /*
            First, we will delcare the isExpressionFound variable which is placed
            stop the While loop from looping endlessly. We will assume the
            isExpressionFound = true;
            Also, the While loop stops if an import command will return "nodata",
            meaning, the imported app responded no data upon inquiry.
            */
            $isExpressionFound = true;
            while ($isExpressionFound == true && !($this->importRequestResponseStatus == "nodata")) {
                // Looking for any regular expression through preg_match function
                preg_match('#\{{(.*?)\}}#', $this->template[$dataKeyPassedToBeParsedAndSentToApp], $preg_match_results);
                if ($preg_match_results == null) {
                    /*
                    If there is no regular expression in the template, then
                    just simply print the template out.
                    */
                    $isExpressionFound = false;
                    return $this->template[$dataKeyPassedToBeParsedAndSentToApp];
                }
                else {
                    /*
                    For any valid expression found in the actual template, it will
                    be dissected and stored in $expressionKeywordsInArray variable.
                    Then, the head will be evaluated using function evalRegExHead as
                    it will determine what the Template engine needs to do with the expression.
                    */
                    $expressionKeywordsInArray = explode(" ", trim($preg_match_results[1]));
                    $this->evalRegExHead($expressionKeywordsInArray[0], $expressionKeywordsInArray, $preg_match_results, $dataKeyPassedToBeParsedAndSentToApp);

                }
            }
        }




        /*
        METHOD 2: Evaluating the instruction through head {{ }}
        */
        private function evalRegExHead($head, $expressionKeywordsInArray, $fullExpressionExtracted, $dataKeyPassedToBeParsedAndSentToApp){
            if (trim($head) == "@import") {
                /*
                CASE 1: Head @import
                In this case, the expression tells the engine to import a certain app,
                i.e., importing the app's properties and methods.
                */

                // BLOCK CODE: CONNECTING TO THE APP
                $this->importRequestResponseStatus = "connecting";
                $app = new Import(trim($expressionKeywordsInArray[1]));
                // ABOVE: The Template Engine attempts to connect to the app.

                if ($expressionKeywordsInArray[2] == "using") {
                    /*
                    HERE: Import command is "Using". It instructs the Template Engine
                    to use the data with the PRECEEDING keyword passed towards by the
                    Builder engine upon rendering ( calling the method render() ).
                    */

                    if ($dataKeyPassedToBeParsedAndSentToApp == "presets") {
                        $dataRetrievedFromUsingKeyword = $this->extract_data_using_keyword(trim("presets::".$expressionKeywordsInArray[3]));
                    }
                    else {
                        $dataRetrievedFromUsingKeyword = $this->extract_data_using_keyword(trim($dataKeyPassedToBeParsedAndSentToApp));
                    }
                    /*
                    ABOVE: Template Engine is extracting data from its own data pool which is
                    variable $dataStoredByTemplateEngine.
                    */

                    if (!($dataRetrievedFromUsingKeyword == null)) {
                        /*
                        HERE: Template engine wants to make sure that the data key
                        preceeded by the command using has a value from the $dataPassedByBuilderEngine
                        variable.
                        */

                        // BLOCK CODE: PASSING PARAMETER TO THE APP IMPORTED
                        $this->importRequestResponseStatus = "connected";
                        $app->set($dataRetrievedFromUsingKeyword);

                        if (isset($expressionKeywordsInArray[4])) {
                            /*
                            HERE: Template engine is trying to see if there is additional
                            instructions after importing and passing data to the application.
                            It is determined by the keyword "then, and, & but".
                            */
                            if ($expressionKeywordsInArray[4] == "then") {
                                /*
                                The then keyword will instruct the engine to call a method
                                in the application set by its preceding keyword.
                                */

                                // BLOCK CODE: APP REQUEST
                                $requestApp = $app->request($expressionKeywordsInArray[5], "");
                                /*
                                All app methods are called via single request method. Apps are
                                required to return a response to the Template engine.
                                */

                                if ($requestApp == "nodata") {
                                    /*
                                    If app returns a no data response, it will be saved in replacement
                                    of the importRequestResponseStatus variable.
                                    */
                                    $this->importRequestResponseStatus = "nodata";
                                }
                                else {
                                    /*
                                    If the data returns a response NOT no data, the Template Engine
                                    will save the data as array inside the variable:

                                    dataStoredByTemplateEngine["data_name declared in the expression"]
                                    @import app using $dataPassedByBuilderEngine then app_method data_name
                                    */
                                    $this->dataStoredByTemplateEngine[$expressionKeywordsInArray[6]] = $requestApp;
                                    $this->importRequestResponseStatus = "ok";
                                }

                                // BLOCK CODE: APP REQUEST
                                $this->expression_replace($fullExpressionExtracted, "", $dataKeyPassedToBeParsedAndSentToApp);
                                // ABOVE: The expression headed by @import will be replaced as empty string;
                            }
                        }
                        else {
                            /*
                            HERE: No addtional instruction after importing the app and passing data to it.
                            */
                        }
                    }
                    else {
                        /*
                        HERE: The data referred in the import expression to be passed to the app
                        cannot be found, not non-existent in the $dataPassedByBuilderEngine array.
                        */
                        $this->expression_replace($fullExpressionExtracted, "ERROR: Cannot determine import preset data.", $dataKeyPassedToBeParsedAndSentToApp);
                    }
                }
                else {
                    /*
                    HERE: The instruction for import (i.e "using") is invalid.
                    */
                    $this->expression_replace($fullExpressionExtracted, "ERROR: Cannot determine import instruction.", $dataKeyPassedToBeParsedAndSentToApp);
                }

            }


            elseif (trim($head) == "include") {
                /*
                CASE 2: Head include
                In this case, the expression tells that Template Engine should include the file
                indentified in the preceeding keyword.
                */

                $includedFileError = false;

                /*
                BLOCK CODE: Final Include Render variable is initialized as empty string. This
                            variable will act as the container for the rendered Include file.
                */
                $finalincludeToBeRendered = "";


                if (isset($expressionKeywordsInArray[1])) {
                    /*
                    BLOCK CODE: Setting Up the Path of the section or snippet that needs
                                to be included in the main render.
                    */
                    $includedFilePath = $this->themePath."/".$expressionKeywordsInArray[1];

                    if (file_exists($includedFilePath)) {
                        /*
                        HERE: The Template Engine wants to make sure that there is a file that
                        corresponds to the INCLUDE instruction.
                        */
                        $includedFileData = file_get_contents($includedFilePath);

                        if (isset($expressionKeywordsInArray[2])) {
                            /*
                            HERE: The Template Engine checks if there is additional instruction
                            other than just including the file, i.e., passing data through the
                            "using" keyword.
                            */
                            if (isset($expressionKeywordsInArray[3])) {
                                $includeDataToRender[$expressionKeywordsInArray[3]] = $this->extract_data_using_keyword(trim($expressionKeywordsInArray[3]));
                                if ($includeDataToRender[$expressionKeywordsInArray[3]] == "<!--nodata-->") {
                                    /*
                                    HERE: The Template Engine makes sure that the variable that will be passed
                                    to the included file is existing.
                                    */
                                    $includedFileError = true;
                                    $includesFileErrorMessage = "ERROR: Thrown data is non-existent.";
                                }
                                else {
                                    $finalincludeToBeRendered = $this->render_include_temporarily($includeDataToRender, $expressionKeywordsInArray[3], $includedFileData);
                                }
                            }
                            else {
                                $includedFileError = true;
                                $includesFileErrorMessage = "ERROR: Included file lacks the neccessary data to operate.";
                            }
                        }
                        else {
                            /*
                            HERE: The Template Engine sees no additional instruction. Passes the template
                            right away to the render_include_temporarily function.
                            */
                            $finalincludeToBeRendered = $this->render_include_temporarily(null, "bin", $includedFileData);
                        }
                    }
                    else {
                        $includedFileError = true;
                        $includesFileErrorMessage = "ERROR: Included file ".$expressionKeywordsInArray[1]." is either damaged or missing.";
                    }
                }
                else {
                    $includedFileError = true;
                    $includesFileErrorMessage = "";
                }

                if ($includedFileError == true) {
                    $this->expression_replace($fullExpressionExtracted, $includesFileErrorMessage, "presets");
                }
                else {
                    $this->expression_replace($fullExpressionExtracted, $finalincludeToBeRendered, "presets");
                }
            }


            elseif (trim($head) == "loop") {
                /*
                CASE 3: Head loop
                In this case, the expression tells that Template Engine should loop through the file
                indentified in the preceeding keyword.
                */

                $includedTemplateError = false;

                /*
                BLOCK CODE: Final Include Render variable is initialized as empty string. This
                            variable will act as the container for the rendered Include file.
                */
                $finalLoopToBeRendered = "";
                if (isset($expressionKeywordsInArray[1])) {
                    /*
                    BLOCK CODE: Setting Up the Path of the section or snippet that needs
                                to be included in the main render.
                    */
                    $includedTemplatePath = $this->themePath."/".$expressionKeywordsInArray[1];
                    if (file_exists($includedTemplatePath)) {
                        /*
                        HERE: The Template Engine wants to make sure that there is a file that
                        corresponds to the LOOP instruction.
                        */
                        if (isset($expressionKeywordsInArray[2])) {
                            if ($expressionKeywordsInArray[2] == "using") {
                                /*
                                HERE: The Template Engine checks if there is additional instruction
                                other than just loop the file, i.e., passing data through the
                                "using" keyword.
                                Note: For loop command, it is a MUST to have a data to be iterated
                                against
                                */
                                if (isset($expressionKeywordsInArray[3])) {
                                    $dataToBeLooped = $this->extract_data_using_keyword($expressionKeywordsInArray[3]);
                                    if ($dataToBeLooped == "<!--nodata-->") {
                                        /*
                                        HERE: The Template Engine makes sure that the variable that will be passed
                                        to the looped file is existing.
                                        */
                                        $includedTemplateError = true;
                                        $includedTemplateMessage = "ERROR: Thrown data for loop is non-existent.";
                                    }
                                    else {
                                        foreach ($dataToBeLooped as $individualDataToBeLooped) {
                                            /*
                                            HERE: The loop will be counted against the number of elements in the array variable.
                                            */

                                            /*
                                            BLOCK CODE: The following code block converts :: to _ to ensure that parsing in the
                                            later part of this code would be correct.
                                            */
                                            $dataStorageKeyForLoopArray = explode("::", $expressionKeywordsInArray[3]);
                                            $dataStorageKeyForLoop = implode("_", $dataStorageKeyForLoopArray);
                                            $this->dataStoredByTemplateEngine[$dataStorageKeyForLoop][$individualDataToBeLooped] = $individualDataToBeLooped;

                                            /*
                                            BLOCK CODE: Actual rendering of the loop template file
                                            */
                                            $includedTemplateData = file_get_contents($includedTemplatePath);
                                            $this->template[$dataStorageKeyForLoop."::".$individualDataToBeLooped] = $includedTemplateData;
                                            $finalLoopToBeRendered = $finalLoopToBeRendered." ".$this->render_loop($individualDataToBeLooped, $dataStorageKeyForLoop, $includedTemplateData);

                                        }
                                    }
                                }
                                else {
                                    $includedTemplateError = true;
                                    $includedTemplateMessage = "ERROR: Thrown data for loop is non-existent.";
                                }
                            }
                            else {
                                $includedTemplateError = true;
                                $includedTemplateMessage = "ERROR: Invalid loop command.";
                            }
                        }
                        else {
                            /*
                            HERE: The Template Engine sees no additional instruction.
                            For LOOP command, there should be a corresponding data, an array
                            which will determine how many times the file should be looped.
                            */
                            $includedTemplateError = true;
                            $includedTemplateMessage = "ERROR: Cannot loop through ".$expressionKeywordsInArray[1].", needs iteration.";
                        }
                    }
                    else {
                        $includedTemplateError = true;
                        $includedTemplateMessage = "ERROR: Looped file ".$expressionKeywordsInArray[1]." is either damaged or missing.";
                    }
                }
                else {
                    $includedTemplateError = true;
                    $includedTemplateMessage = "";
                }

                /*
                HERE: LOOP ERROR HANDLING:
                */
                if ($includedTemplateError == true) {
                    $this->expression_replace($fullExpressionExtracted, $includedTemplateMessage, "presets");
                }
                else {
                    $this->expression_replace($fullExpressionExtracted, $finalLoopToBeRendered, "presets");
                }

            }



            elseif (trim($head) == "assets") {
                /*
                CASE 4: Head assets
                In this case, the expression tells that Template Engine should loop through the file
                indentified in the preceeding keyword.
                */
                if (isset($expressionKeywordsInArray[1])) {
                    $assetsPath = $this->themePath."/assets/".$expressionKeywordsInArray[1];
                    if (file_exists($assetsPath)) {
                        $assetsContents = file_get_contents($assetsPath);
                        $this->expression_replace($fullExpressionExtracted, $assetsContents, "presets");
                    }
                    else {
                        $this->expression_replace($fullExpressionExtracted, "<!--Error: Assets ".$expressionKeywordsInArray[1]." could not be found!-->", "presets");
                    }
                }
                else {

                    $this->expression_replace($fullExpressionExtracted, " ", "presets");
                }
            }


            else {
                /*
                HERE: If the head expression is not among the list above, then
                the Template Engine assumes that the request is to display data from
                $allAvailabledataStoredByTemplateEngine.
                */
                $this->expression_replace($fullExpressionExtracted, $this->extract_data_using_keyword($head), $dataKeyPassedToBeParsedAndSentToApp);
            }
        }



        /*
        METHOD 3: Extracting data with the "using" keyword. It returns data requested
        */
        private function extract_data_using_keyword($arrayKeyPathToRequestedData){
            $dataInquiredAsExtractedFromExpression = explode("::", trim($arrayKeyPathToRequestedData));
            $allAvailabledataStoredByTemplateEngine = $this->dataStoredByTemplateEngine;
            $isKeyPresentInDataStored = true;
            foreach ($dataInquiredAsExtractedFromExpression as $dataKey) {
                if (isset($allAvailabledataStoredByTemplateEngine[$dataKey])) {
                    $allAvailabledataStoredByTemplateEngine = $allAvailabledataStoredByTemplateEngine[$dataKey];
                }
                else {
                    $isKeyPresentInDataStored = false;
                }
            }
            if ($isKeyPresentInDataStored == true) {
                if (is_array($allAvailabledataStoredByTemplateEngine)) {
                    return $allAvailabledataStoredByTemplateEngine;
                }
                else {
                    return " ".$allAvailabledataStoredByTemplateEngine;
                }
            }
            else {
                return "<!--nodata-->";
            }
        }

        /*
        METHOD 4: Expression replacement. Most of the expression replacement is done by
        this method.
        */
        private function expression_replace($fullExpressionExtracted, $value, $dataKeyPassedToBeParsedAndSentToApp){
            $this->template[$dataKeyPassedToBeParsedAndSentToApp] = str_replace($fullExpressionExtracted, $value, $this->template[$dataKeyPassedToBeParsedAndSentToApp]);
        }

    }
