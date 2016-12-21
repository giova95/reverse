<?php
    
    // common configuration
    require("../includes/common.php");
    
    // set the title
    $render_ops["title"] = "settings";
    // default form: the user didn't choose what action to take, yet
    $render_ops["form"] = NULL;
    
    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        if (isset($_GET["q"])) 
        {
            // if the user wants to change the password
            if ($_GET["q"] == "password")
            {
                $render_ops["form"] = "password";
            }
            // if the user wants to change the username
            else if ($_GET["q"] == "username")
            {
                $render_ops["form"] = "username";
            }
            // if the user wants to reset his stats
            else if ($_GET["q"] == "reset")
            {
                $render_ops["form"] = "reset";
            }
            // if the user wants to change his public settings
            else if ($_GET["q"] == "public")
            {
                $render_ops["form"] = "public";
                
                // retrieve public settings
                $result = query("SELECT public FROM ". USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
                if ($result === false or count($result) != 1)
                {
                    // something got wrong
                    apologize("Sorry, couldn't connect to database to retrieve your public settings.");
                }
                else
                {
                    $render_ops["public"] = $result[0]["public"];
                }
            }
        }
    }
    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // if the user wants to change his password
        if ($_POST["form"] == "password")
        {
            // validate submission
            if (empty($_POST["current_password"]))
            {
                apologize("You must provide your current password");
            }
            else if (empty($_POST["password"]))
            {
                apologize("You must provide a new password.");
            }
            else if ($_POST["password"] !== $_POST["confirmation"])
            {
                apologize("Passwords do not match");
            }
            
            // check if the current_password is correct
            $check = query("SELECT hash FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
            if ($check === false)
            {
                apologize("An error occured trying to compare your current password. Contact an administrator");
            }
            else if ($check[0]["hash"] != crypt($_POST["current_password"], $check[0]["hash"]))
            {
                apologize("You provided a wrong current password");
            }
        
            // query database to update password
            $result = query("UPDATE " . USERS_TABLE . " SET hash = ? WHERE id = ?", crypt($_POST["password"]), $_SESSION["id"]);
            if ($result === false)
            {
                apologize("Some error occurred while changing your password. Contact an administrator.");
            }
        }
        // if the user wants to change the username
        else if ($_POST["form"] == "username")
        {
            // validate submission
            if (empty($_POST["password"]))
            {
                apologize("You must provide your  password");
            }
            else if (empty($_POST["new_username"]))
            {
                apologize("You must provide a new username.");
            }
            
            // check if the current_password is correct
            $check = query("SELECT hash FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
            if ($check === false)
            {
                apologize("An error occured trying to compare your current password. Contact an administrator");
            }
            else if ($check[0]["hash"] != crypt($_POST["password"], $check[0]["hash"]))
            {
                apologize("You provided a wrong current password");
            }
            
            // query database to update password
            $result = query("UPDATE " . USERS_TABLE . " SET username = ? WHERE id = ?", $_POST["new_username"], $_SESSION["id"]);
            if ($result === false)
            {
                apologize("Username not available.");
            }
            
            // if we reach this point everything went right
            $render_ops["updated"] = true;
        }
        // if the user wants to reset all of his stats
        else if ($_POST["form"] == "reset")
        {
            // validate submission
            if (empty($_POST["password"]))
            {
                apologize("You must provide your  password");
            }
            if (empty($_POST["sure"]))
            {
                apologize("You must check the \"I'm sure\" checkbox");
            }
            
            // check if the current_password is correct
            $check = query("SELECT hash FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
            if ($check === false)
            {
                apologize("An error occured trying to compare your current password. Contact an administrator");
            }
            else if ($check[0]["hash"] != crypt($_POST["password"], $check[0]["hash"]))
            {
                apologize("You provided a wrong current password");
            }
            
            $result = query("UPDATE " . USERS_TABLE . "
                            SET level = 0, points = 0, typed = 0, misspelled = 0
                            WHERE id = ?", $_SESSION["id"]);
                            
            if ($result === false)
            {
                apologize("Sorry, an error occurred. Couldn't reset your stats.");
            }
            
            // if we reach this point everything went right
            $render_ops["updated"] = true;
        }
        else if ($_POST["form"] == "public")
        {
            // get new user preferences
            $public = (isset($_POST["public"]) and $_POST["public"] == true) ? 1 : 0;
            
            // update database with the new settings
            $result = query("UPDATE " . USERS_TABLE . " SET public = ? WHERE id = ?", $public, $_SESSION["id"]);
            if ($result === false)
            {
                apologize("Sorry. We have some problem on updating your settings");
            }
            
            // if we reach this point everything went right
            $render_ops["updated"] = true;
        }
        // else we don't know what the user wants. Redirect to the settings page.
        else 
        {
            redirect("settings.php");
        }
        
    } // end POST branch
    
    // that's all
    render("settings_form.php", $render_ops);
      
?>
