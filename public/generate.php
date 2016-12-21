<?php
/**
* Generate a new word to display to the current user.
*
* OUTPUT:
*   a JSON array that contains the word that the user has to reverse.
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
        // generate the word
        $word = generate();
        
        // store the word in the session for later check
        $_SESSION["word"] = $word;
        
        // output the word (or false in case $word === false)
        echo json_encode(array($word));
    }
    
    
    /**
    * Decleare some addictional function
    */
    
    
    /**
    * Generate the word.
    * RETURN: the generated word
    * ERROR: return false in case of error
    */
    function generate()
    {
        // get the level of the user
        $level = get_level();
        if ($level === false)
        {
            return false;
        }
        
        // get the length of the word
        $length = next_length($level);
        
        // return the word (or false in case of error)
        return get_word($length);
    }
    
    // return the length of the next word
    function next_length($level)
    {
        // use the helper function to calculate the value
        return next_length_r($level + 3, 0);
    }
    
    /**
    * Starts from $level;
    * - generate a random number between -1 and 1 (inclusive) then
    * - if the number is 0, return $level, otherwise
    * - if the random number is same sign with direction go ahead and call
    *       recursive after update level of +1 or -1, otherwise
    * - return the $level
    *
    * We have these probability:
    * 1/3 to have a length of the size seed
    * 4/9 to have a length that deviates by one from the seed
    * 4/27 to have a length that deviates by two from the seed
    * ...
    * We have 1/3 for the same size and 4/(3^k+1) for a length that deviates by k from the level
    * 
    * If the length goes out the min (max) length, the min (max) length is returned
    */
    function next_length_r($seed, $direction)
    {
        if ($seed < MIN_LENGTH)
            return MIN_LENGTH;
        else if ($seed > MAX_LENGTH)
            return MAX_LENGTH;
        
        // get a random number between -1 and 1 (inclusive)
        $rand = mt_rand(-1,1);
        
        // if $rand is zero or $rand and $direction have opposite sign
        if ($rand === 0 or $rand + $direction === 0)
        {
            return $seed;
        }
        else
        {
            // calculate the next length
            return next_length_r($seed + $rand, $rand);
        }
    }

?>
