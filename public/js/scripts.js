/**
 * scripts.js
 *
 * Computer Science 50
 * Final Project
 *
 * Global JavaScript
 */
 
/**
* Functions are at the end of the file.
*/
// on document load
$(function() {
    // when the user click on the start button
    $("#start").click(function first_time( event ) {
        $(this).text("Skip");
        $(this).removeClass("btn-primary").addClass("btn-default");
        // enable the button "check"
        $("#check").prop("disabled", false).removeClass("disabled");
        
        // start the game getting the next word
        next();
        
        $(this).unbind("click", first_time);
        
        // the next time we have to execute only this
        $("#start").click(function ( event ) {
            clean();
            next();
        });
    });
    
    // submit the form
    $("#check").click(function(event) {
        $("#reverse").submit();
    });
    
    // avoid submitting of the form and manage the submission manually
    $("#reverse").submit(function(event) {
        $("#reverse input:first").prop("disabled", true);
    
        // check the word
        check();
        
        // prevent default
        event.preventDefault();
    });
    
    
    /***** CSS adjustment *****/
    // let the bottom be always in the bottom of the page
    $(".container").css("min-height", $(window).height() - $("#bottom").outerHeight(true));
});



/**
* FUNCTIONS
*/

function check() {
        // TODO: manage timer functionality (stop timer?)
        $.ajax({
            method: "POST",
            url:    "check.php",
            data:   {word: $("#reverse input:first").val()}, // TODO: send time information?
            cache:  false,
            datatype:   "json"
        })
        .done(function(j_data) {
            console.log("'check' returned: ", j_data);
            data = JSON.parse(j_data); // TODO: check if the parse was successfull
            
            // duration of the animation
            var duration = 800;
            
            // act according to the correctness 
            if (data[0] === true) {
                right(duration, data[2]);
            } else {
                wrong(duration, data[2]);
            }
            
            // show the right level to the user
            up_level(data[1]);
                
            window.setTimeout(function() {
                // clean the textbox
                clean();
                
                // get the next word and show it to the user
                next();
            }, duration);
            
        })
        .fail(function(data) {
            
        });
}

function right(duration, points)
{
    $(".points")
                .text(points)
                .addClass("points-added")
                .animate({fontSize: "30px"}, "slow");
    
    
    // animate the textbox
    $("#reverse input:first").addClass("success").delay(duration).queue(function() {
        $(".points")
                    .text("")
                    .removeClass("points-added")
                    .css("font-size", "");
        
        $(this).removeClass("success").dequeue();
    });
}

function wrong(duration, points)
{
    $(".points")
                .text(points)
                .addClass("points-removed")
                .animate({fontSize: "30px"}, "slow");
    
    // animate the textbox
    $("#reverse input:first").addClass("failure").delay(duration).queue(function() {
        $(".points")
                    .text("")
                    .removeClass("points-removed")
                    .css("font-size", "");
        
        $(this).removeClass("failure").dequeue();
    });
}

// clear the text input
function clean() {
    $("#reverse input:first").val("");
    // TODO: timer?
}

// get the next word and return it
function next() {
    // TODO: ajax call to generate
    $.ajax({
            method: "POST",
            url:    "generate.php",
            cache:  false,
            datatype:   "json"
        })
        .done(function(j_data) {
            // parse the data
            data = JSON.parse(j_data); // TODO: check if the parse was successfull
            
            // if it is in the expected format show the word
            if (data[0] !== undefined) {
                show(data[0]);
            } else {
                console.log("Not in the expected format");
                return false;
            }
        })
        .fail(function(data) {
            console.log("Failed ajax request");
            return false;
        });
}

// show the word to the user
function show(word) {
    console.log("word: ", word);
    
    if (word != false)
    {
        // enable the textbox and set the focus on it
        $("#rword").prop("disabled", false)
                    .focus();
        
        $span = $("#word span");
        
        // ensure the span is hidden
        $span.hide();
        
        // set text of the span
        $span.text(word);
        
        // show the span
        $span.fadeIn(400)
            .delay(1000)
            .fadeOut(400)
            .queue(function() {
                $span.text("");
                $(this).dequeue();
            });
            
    } else {
        console.log("Error");
    }
}

/**
 * Update the level showed to the user
 */
function up_level(level) {
    $lvl_span = $("#level");
    if ($lvl_span.text() != level)
    {
        /* It is required to separate removeClass and addClass for make the animation working */
        $badge = $(".badge");
        $badge.removeClass("level-animation");
        $badge.width($badge.width()); // -> triggering reflow. DO NOT REMOVE THIS LINE, the animation will not work.
        $badge.addClass("level-animation");
        
        // update the text to show the correct level
        $lvl_span.text(level);
    }
    
}
