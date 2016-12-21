<?php

     // common configuration
    require("../includes/common.php");
    
    // get points
    $points = get_points();
    if ($points === false)
    {
        // something got wrong
        apologize("Sorry, could not retrive points. If the error persists contact an administrator");
    }

    // retrieve 
    $result = query("SELECT typed, misspelled FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
    if ($result === false or count($result) != 1)
    {
        // something got wrong
        apologize("Sorry, could not retrieve typed and misspelled words. If the error persists contact an administrator");
    }
    
    $render_ops["points"] = $points;
    $render_ops["typed"] = $result[0]["typed"];
    $render_ops["misspelled"] = $result[0]["misspelled"];
    
    // render the page
    render("profile.php", $render_ops);
    
?>
