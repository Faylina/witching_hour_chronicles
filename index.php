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

                $loggedIn   = false;
                $errorLogin = NULL; 

#*************************************************************************#

				
				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#

                #************ PREPARE SESSION ***********#

                session_name('wwwwitchinghourchroniclescom');


                #************ START / CONTINUE SESSION ***********#

                session_start();

if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$arrayName <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($array);					
if(DEBUG_A)	    echo "</pre>";

                #****************************************#
				#******** CHECK FOR VALID LOGIN *********#
				#****************************************#

                if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
                    // error
if(DEBUG)	echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Login could not be validated! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                    #************ DENY PAGE ACCESS ***********#

                    session_destroy();

                } else {
                    // success
if(DEBUG)	echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Valid login. <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $loggedIn = true;
                }


				
				

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

            <!-- toggle navigation depending on the login status -->
            <?php if ( $loggedIn === false ): ?>
            <!-- ------------- LOGIN FORM BEGIN --------------------------- -->
                <form action="" method="POST">
                    <input type="hidden" name="loginForm">

                    <fieldset>
                        <legend>Author Login</legend>
                        <div class="error"><?= $errorLogin ?></div>
                        <!-- security by obscurity: names are deliberately 
                            chosen to be obscure -->
                        <input class="login-field" type="text" name="b1" placeholder="Email">
                        <input class="login-field" type="password" name="b2" placeholder="Password">

                        <input class="submit-button" type="submit" value="Login">

                    </fieldset>
                </form>
            <!-- ------------- LOGIN FORM END ----------------------------- -->

            <?php else: ?>

            <!-- ------------- NAV LINKS BEGIN ---------------------------- -->
                <a class="link" href="./dashboard.php">Dashboard >></a>
                <a class="link" href="?action=logout">>> Logout</a>

            <!-- ------------- NAV LINKS END ------------------------------ -->
            <?php endif ?>

        </nav>
    <!-- ------------- NAVIGATION END ----------------------------- -->


    <!-- ------------- HEADER BEGIN ------------------------------- -->
        <header>

            <img class="logo" src="./css/images/logo.png" alt="Parchment paper with a teal quill, a full moon in the background">
            <h1>Witching Hour Chronicles</h1>

        </header>
    <!-- ------------- HEADER END ---------------------------------- -->


    <!-- ------------- BLOG BEGIN ---------------------------------- -->
        <div class="blog">

        </div>
    <!-- ------------- BLOG END ------------------------------------ -->


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