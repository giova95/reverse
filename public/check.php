<?php
/**
* Check the correctness of the typed word.
* Accept a POST parameter, word. The script reverse the parameter and check
* if it is equals to the word we previously stored in $_SESSION.
*
* OUTPUT:
*   a JSON array (for future improvements) composed in this way: [correct, level]
*   correct:    - true if the user's input is right;
*               - false if the user's input is wrong (or there's no the parameter "word")
*
*   level:      - the level of the user in case of success;
*               - false otherwise
*/

    // configuration
    require(__DIR__ . "/../includes/config.php");
    
    
    // if user reached page via GET
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // this page is not maked to interface directly to the user
        apologize("Sorry, nothing to see here");
    }

    // else if user reached page via POST (as by a script)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // set default output to false
        $output = array(false);
        
        // check if the parameter exist
        if (isset($_POST["word"]) and isset($_SESSION["word"]))
        {
            // get the length of the word
            $word_length = strlen($_SESSION["word"]);
            
            // default set rightness to false
            $right = false;
            // check the correctenss of the typed string
            if (strrev(strtolower(trim($_POST["word"]))) === $_SESSION["word"])
            {
                $right = true;
            }
            
            // delete the stored word. This prevent gaining point from multiple request
            unset($_SESSION["word"]);
            
            // upgrade typed and misspelled counters
            $result = query("UPDATE " . USERS_TABLE . " 
                            SET typed = typed + 1, misspelled = misspelled + ? WHERE id = ?", ($right === false) ? 1 : 0, $_SESSION["id"]);
            
            if ($result !== false)
            {
                // upgrade points
                $result = upgrade_points($word_length, $right);
                if ($result !== false)
                {
                    list($level, $points) = $result;
                        
                    // set output correctly
                    $output = array($right, $level, $points);
                }
            }
        }
        
        echo json_encode($output);
        
    }
?>
