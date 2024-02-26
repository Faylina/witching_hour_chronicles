<?php
#*************************************************************************#

				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#

				require_once('./include/config.inc.php');
                require_once('./include/form.inc.php');
                require_once('./include/db.inc.php');

#*************************************************************************#

				
				#****************************************#
				#********* INITIALIZE VARIABLES *********#
				#****************************************#

                $userFirstName  = NULL;
                $userLastName   = NULL;

#*************************************************************************#

				
				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#

                #************ PREPARE SESSION ***********#

                session_name('wwwwitchinghourchroniclescom');


                #************ START / CONTINUE SESSION ***********#

                session_start();

if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_SESSION);					
if(DEBUG_A)	    echo "</pre>";

                #****************************************#
				#******** CHECK FOR VALID LOGIN *********#
				#****************************************#

                if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
                    // error
if(DEBUG)	        echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Login could not be validated! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                    #************ DENY PAGE ACCESS ***********#

                    // 1. Delete session file
                    session_destroy();

                    // 2. Redirect to homepage
                    header('LOCATION: index.php');

                    // 3. Fallback in case of an error: end processing of the script
                    exit();

                #************ VALID LOGIN ***********#
                } else {
                    // success
if(DEBUG)	        echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Valid login. <i>(" . basename(__FILE__) . ")</i></p>\n";

                    session_regenerate_id(true);

                    $userID = $_SESSION['ID'];
                }           

#*************************************************************************#

				
				#****************************************#
				#******** PROCESS URL PARAMETERS ********#
				#****************************************#

                #******** PREVIEW URL PARAMETERS ********#
/*
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_GET);					
if(DEBUG_A)	    echo "</pre>";
*/


                // Step 1 URL: Check whether the parameters have been sent

                if( isset($_GET['action']) === true ) {
if(DEBUG)		    echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The URL-parameter 'action' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";				
                    // Step 2 URL: Read, sanitize and output URL data
                    
if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The URL parameters are being read and sanitized... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    $action = sanitizeString($_GET['action']);
                    
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    // Step 3 URL: Branching
                    
                    #*************** LOGOUT **************#
                    
                    if( $action === 'logout') {
                    
if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The user is being logged out... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                        // Step 4 URL: processing data
                    
if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Processing data... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                        // 1. Delete session file
                        session_destroy();
                    
                        // 2. Reload homepage
                        header('LOCATION: index.php');

                        // 3. Fallback in case of an error: end processing of the script
                        exit();
                                        
                    } // LOGOUT END
                    
                } // PROCESS URL PARAMETERS END
				
#*************************************************************************#
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="./css/images/favicon.ico">
        <title>Witching Hour Chronicles - Homepage</title>
        <link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
    </head>
    <body>

        <!-- ------------- NAVIGATION BEGIN --------------------------- -->
        <nav class="navigation">

            <!-- ------------- NAV LINKS BEGIN ---------------------------- -->
                <a class="link" href="./index.php"><< Homepage</a>
                <a class="link" href="?action=logout">Logout >></a>

            <!-- ------------- NAV LINKS END ------------------------------ -->

        </nav>
    <!-- ------------- NAVIGATION END ----------------------------- -->


    <!-- ------------- HEADER BEGIN ------------------------------- -->
        <header>

            <img class="logo" src="./css/images/logo.png" alt="Parchment paper with a teal quill, a full moon in the background">
            <div class="title">
                <h1>Witching Hour Chronicles</h1>
                <div class="active-user">Happy writing, <?= $userFirstName ?> <?= $userLastName ?>!</div>
            </div>

        </header>
    <!-- ------------- HEADER END ---------------------------------- -->


    <!-- ------------- ARTICLE FORM BEGIN -------------------------- -->
        
    <!-- ------------- ARTICLE FORM END ---------------------------- -->


    <!-- ------------- CATEGORY FORM BEGIN ------------------------- -->

    <!-- ------------- CATEGORY FORM END --------------------------- -->


    <!-- ------------- FOOTER BEGIN -------------------------------- -->
        <footer>
            <div class="footer-container">
                <ul>
                    <li>Copyright</li> 
                    <li>&copy;</li> 
                    <li>Faylina 2024</li>
                </ul>
                <div><strong>Disclaimer:</strong> All images, apart from the logo and background, were generated by AI.</div>
            </div>
        </footer>
    <!-- ------------- FOOTER END ---------------------------------- -->
    
    </body>
</html>