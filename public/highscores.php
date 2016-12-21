<?php

    // common configuration
    require("../includes/common.php");
    
    // set title
    $render_ops["title"] = "Highscores";
    
    // retrieve scores from database
    $others = query("SELECT username, level, points, typed, misspelled FROM " . USERS_TABLE . " WHERE public = 1 and id != ? ORDER BY  points DESC, misspelled ASC", $_SESSION["id"]);
    $user   = query("SELECT username, level, points, typed, misspelled FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
    if ($others !== false and $user !== false)
    {
        $render_ops["scores"] = $others;  
        $render_ops["user_scores"] = $user[0]; 
    }
    else
    {
        // something got wrong
        apologize("Sorry, could not retrieve highscores");
    }
    
    // render the page
    render("highscores.php", $render_ops);
?>
