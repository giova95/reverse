<?php

    /**
     * functions.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Helper functions.
     */

    require_once("constants.php");

    /**
     * Apologizes to user with message.
     */
    function apologize($message = "Sorry, some error occurred.")
    {
        global $render_ops;
        
        $render_ops["message"] = $message;

        render("apology.php", $render_ops);
        exit;
    }
    
    /**
    * Get the level of the user.
    * RETURN:   the level of the user.
    * ERROR:    simply return false in case of error.
    */
    function get_level()
    {
        // query the database
        $result = query("SELECT level FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
        if ($result === false)
        {
            // there's some error, return false
            return false;
        }

        // return the level
        return $result[0]["level"];
    }
    
    /**
    * Get the name of the user.
    * RETURN:   the username of the user.
    * ERROR:    simply return false in case of error.
    */
    function get_name()
    {
        // query the database
        $result = query("SELECT username FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
        if ($result === false)
        {
            // there's some error, return false
            return false;
        }

        // return the name
        return $result[0]["username"];
    }
    
        /**
    * Get the total points of the user.
    * RETURN:   the total ponts of the user.
    * ERROR:    simply return false in case of error.
    */
    function get_points()
    {
        // query the database
        $result = query("SELECT points FROM " . USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
        if ($result === false)
        {
            // there's some error, return false
            return false;
        }

        // return the number of points
        return $result[0]["points"];
    }
    
    /**
    * Get a word from the database of the specified length
    * RETURN:   the word
    * ERROR:    return false in case of error
    */
    function get_word($length)
    {
        // query the database
        $result = query("SELECT word FROM " . WORDS_TABLE . " WHERE length = ? ORDER BY RAND() LIMIT 1", $length);
        if ($result === false)
        {
            // some error occured
            return false;
        }
        
        
        // return the word
        return $result[0]["word"];
    }
    
    /**
     * Increase/decrease the points of the user according to the length of the
     * word and the rightness of the input // TODO "and the time he spent to answer".
     * PARAMETERS:
     *      - $length:  the length of the word the user reversed
     *      - $right: true if the user input is correct, false otherwise
     *      - (NOT IMPLEMENTED) $time:    the time the user spent to reverse the word
     *
     * RETURN:
     *      - In case of success: an array containing the level of the user an the point gained/taken out
            - false, in case of failure;
     */
    function upgrade_points($length, $right) // TODO: pass "time" as a parameter and upgrade points according to it
    {
        $amount = 0;
        
        // decrease/increase according to $right
        if ($right === true)
        {
            // amount of point to add
            $amount = $length;
        }
        else     
        {
            // amount of point to take away
            $amount = (integer) ( -$length/3 ); // cast to int to have an integer amount
        }
        
        // update the points in the database
        // use "and points + 1 > 0" to avoid to go under zero and the +1 is for when, at the start, points = 0.
        $result = query("UPDATE " . USERS_TABLE . "
                        SET points = points + ? WHERE id = ? AND points + 1 > 0", $amount, $_SESSION["id"]);
        
        if ($result === false)
        {
            // something got wrong
            return false;
        }
        
        $level = upgrade_level();
        if ($level === false)
        {
            // something got wrong
            return false;
        }
        
        // that's all, return
        return array($level, $amount);
    }
    
    /**
     * Increase/decrease the level of the user according to the number of his points.
     * The thresholds of points are the powers of ten:  10 -> first level,
                                                        100 -> second level,
                                                        1000, ...
     * PARAMETERS:
     *      none;
     *
     * RETURN:
     *      - In case of success: the new level;
            - In case of failure: false;
     */
    function upgrade_level()
    {
        // get actual points and level of the user (maybe we have to upgrade the level)
        $result = query("SELECT points, level FROM ". USERS_TABLE . " WHERE id = ?", $_SESSION["id"]);
        if ($result === false or count($result[0]) != 2)
        {
            // something got wrong
            return false;
        }
        $points = $result[0]["points"];
        $level = $result[0]["level"];
        
        // change: 0 for no change, -1 to decrease, 1 to increase.
        $change = 0;
        
        // if the number of points exceed 10^(level + 1).
        if ($points >= pow(10, $level + 1))
        {
            $change = +1;
        }
        // if the number of points dropped under 10^level
        else if ($points < pow(10, $level))
        {
            $change = -1;
        }
    
        // Execute the query only if we have to update the level
        if ($change != 0)
        {
            // update the level
            $result = query("UPDATE " . USERS_TABLE . " SET level = level + ? WHERE id = ?", $change, $_SESSION["id"]);
            // if we managed to update the level
            if ($result === false)
            {
                return false;
            }
        }
        
        // return the updated level
        $level = $level + $change;
        return $level;
    }
        
    /**
     * Logs out current user, if any.  Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     */
    function logout()
    {
        // unset any session variables
        $_SESSION = [];

        // expire cookie
        if (!empty($_COOKIE[session_name()]))
        {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }

    /**
     * Returns a stock by symbol (case-insensitively) else false if not found.
     */
    function lookup($symbol)
    {
        // reject symbols that start with ^
        if (preg_match("/^\^/", $symbol))
        {
            return false;
        }

        // reject symbols that contain commas
        if (preg_match("/,/", $symbol))
        {
            return false;
        }

        // headers for proxy servers
        $headers = [
            "Accept" => "*/*",
            "Connection" => "Keep-Alive",
            "User-Agent" => sprintf("curl/%s", curl_version()["version"])
        ];

        // open connection to Yahoo
        $context = stream_context_create([
            "http" => [
                "header" => implode(array_map(function($value, $key) { return sprintf("%s: %s\r\n", $key, $value); }, $headers, array_keys($headers))),
                "method" => "GET"
            ]
        ]);
        $handle = @fopen("http://download.finance.yahoo.com/d/quotes.csv?f=snl1&s={$symbol}", "r", false, $context);
        if ($handle === false)
        {
            // trigger (big, orange) error
            trigger_error("Could not connect to Yahoo!", E_USER_ERROR);
            exit;
        }
 
        // download first line of CSV file
        $data = fgetcsv($handle);
        if ($data === false || count($data) == 1)
        {
            return false;
        }

        // close connection to Yahoo
        fclose($handle);

        // ensure symbol was found
        if ($data[2] === "N/A" || $data[2] === "0.00")
        {
            return false;
        }

        // return stock as an associative array
        return [
            "symbol" => $data[0],
            "name" => $data[1],
            "price" => floatval($data[2])
        ];
    }

    /**
     * Executes SQL statement, possibly with parameters, returning
     * an array of all rows in result set or false on (non-fatal) error.
     */
    function query(/* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        // try to connect to database
        static $handle;
        if (!isset($handle))
        {
            try
            {
                // connect to database
                $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

                // ensure that PDO::prepare returns false when passed invalid SQL
                $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
            }
            catch (Exception $e)
            {
                // trigger (big, orange) error
                trigger_error($e->getMessage(), E_USER_ERROR);
                exit;
            }
        }

        // prepare SQL statement
        $statement = $handle->prepare($sql);
        if ($statement === false)
        {
            // trigger (big, orange) error
            trigger_error($handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }

        // execute SQL statement
        $results = $statement->execute($parameters);

        // return result set's rows, if any
        if ($results !== false)
        {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return false;
        }
    }

    /**
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */
    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^https?:\/\//", $destination))
        {
            header("Location: " . $destination);
        }

        // handle absolute path
        else if (preg_match("/^\//", $destination))
        {
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else
        {
            // adapted from http://www.php.net/header
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: $protocol://$host$path/$destination");
        }

        // exit immediately since we're redirecting anyway
        exit;
    }

    /**
     * Renders template, passing in values.
     */
    function render($template, $values = [])
    {
        // if template exists, render it
        if (file_exists("../templates/$template"))
        {
            // extract variables into local scope
            extract($values);

            // render header
            require("../templates/header.php");

            // render template
            require("../templates/$template");

            // render footer
            require("../templates/footer.php");
        }

        // else err
        else
        {
            trigger_error("Invalid template: $template", E_USER_ERROR);
        }
    }
?>
