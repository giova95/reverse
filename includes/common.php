<?php

    // configuration
    require("../includes/config.php");
    
    // define an array for the render options
    $render_ops = array();
    
    // if the user is logged in
    if (!empty($_SESSION["id"]))
    {
        /**
         * Yeah, there are the functions get_name and get_level, but this way we 
         * already have these values using only 1 query instead of 2.
         * We always need these values.
         */
        // extract username and level from the database
        $result = query("SELECT username, level FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
        if ($result === false or count($result[0]) != 2)
        {
            apologize("Something got wrong");
        }
        $username = $result[0]["username"];
        $level = $result[0]["level"];
        
        // add parameters to the render options array
        $render_ops["username"] = $username;
        $render_ops["level"]    = $level;
    }
    
?>
