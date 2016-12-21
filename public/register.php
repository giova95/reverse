<?php

    // configuration
    require("../includes/config.php");

    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // else render form
        render("register_form.php", ["title" => "Register"]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["username"]))
        {
            apologize("You must provide your username.");
        }
        else if (empty($_POST["password"]))
        {
            apologize("You must provide your password.");
        }
        else if ($_POST["password"] !== $_POST["confirmation"])
        {
            apologize("Passwords do not match");
        }
        
        // query database to insert the new user
        $rows = query("INSERT INTO " . USERS_TABLE . " (username, hash) VALUES(?, ?)", $_POST["username"], crypt($_POST["password"]));
       
        // if the registration succeed
        if ($rows !== false)
        {
            // remember that user's now logged in by storing user's ID in session
            $ids = query("SELECT LAST_INSERT_ID() AS id");
            $_SESSION["id"] = $ids[0]["id"];
            
            // redirect to portfolio
            redirect("/");
        }
        // otherwise username already exist
        else
        {
            apologize("Username already exist: please, change your username");
        }
    }

?>
