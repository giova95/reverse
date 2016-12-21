<?php

    // configuration
    require("../includes/config.php"); 

    // if user reached page via GET
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // there's nothing to do here
        redirect("/");
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $opts = array('http' =>
		    array(
			    'method'  => 'GET',
			    'header'  => 'Referer: http://translate.google.com/\nUser-Agent: stagefright/1.2 (Linux;Android 5.0)' .
			                   '\nHost: translate.google.com\nConnection: keep-alive'
		    )
	    );
	    
        $context  = stream_context_create($opts);
        $audiofile = "http://speechutil.com/convert/ogg?text=Hello+world";
        $filename = tempnam("audio", "say");
        
        // try to use google translate
        if ($soundfile = @file_get_contents('http://translate.google.com/translate_tts?tl=en&client=t&q=Hello+world', false, $context))
        {
		    file_put_contents($filename,$soundfile);
		}
		else 
		if ($soundfile = @file_get_contents('http://speechutil.com/convert/ogg?text=Hello+world'))
		{
		    file_put_contents($filename,$soundfile);
		}
		
        rename($filename, $filename . ".ogg");
        $filename .= ".ogg";
        $filepath = "/audio/" . basename($filename);
        chmod("./" . $filepath, 644);
        $audiofile = $_SERVER["SERVER_NAME"] . $filepath;
        echo $audiofile;
    }
