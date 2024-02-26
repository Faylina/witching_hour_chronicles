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

                #********* USER VARIABLES ***************#
                $userFirstName      = NULL;
                $userLastName       = NULL;

                #********* ARTICLE VARIABLES ************#
                $title              = NULL;
                $article            = 'Your article...';

                #********* ERROR VARIABLES **************#
                $errorTitle         = NULL;
                $errorImage         = NULL;
                $errorArticle       = NULL;
                $errorCategory      = NULL;

                #********* GENERATE LIST OF ALLOWED MIME TYPES *********#

                $allowedMIMETypes   = implode(', ', array_keys(IMAGE_ALLOWED_MIME_TYPES));
                $mimeTypes          = strtoupper( str_replace( array('image/jpeg, ', 'image/'), '', $allowedMIMETypes));

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

        <div class="forms">

            <!-- ------------- ARTICLE FORM BEGIN -------------------------- -->

            <form class="article-form" action="" method="POST" enctype="multipart/form-data">
                <div class="form-heading">Write a new blog article</div>
                <br>
                <input type="hidden" name="articleForm">


                <!-- ------------- Category ------------- -->
                <label for="b1">Choose a category</label>
                <select name="b1" id="b1" class="form-text">
                    <option value="category 1">Category 1</option>
                    <option value="category 2">Category 2</option>
                </select>

                <br>
                <!-- ------------- Title ---------------- -->
                <label for="b2">Write the title of your article</label>
                <div class="error"><?= $errorTitle ?></div>
                <input type="text" class="form-text" name="b2" id="b2" placeholder="Title" value="<?= $title ?>">

                <br>
                <!-- ------------- Image Upload ---------- -->
                <fieldset>
                    <legend>Upload an image</legend>
                    <!-- ------------- Image Info Text ---------- -->
                    <p class="image-info">
                        You may upload an image of the type <?= $mimeTypes ?>. <br>
                        The width of the image may not exceed <?= IMAGE_MAX_WIDTH ?> pixels. <br>
                        The height of the image may not exceed <?= IMAGE_MAX_HEIGHT ?> pixels. <br>
                        The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024 ?> kB.
                    </p>
                    <br>
                    <div class="error"><?= $errorImage ?></div>
                    <input type="file" name="image">
                    <br>
                    <br>
                    <label for="b3">Choose the alignment of the image</label>
                    <br>
                    <select name="b3" id="b3" class="form-select">
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </fieldset>
                <br>

                <!-- ------------- Article ------------------ -->
                <label for="b4">Write your article</label>
                <div class="error"><?= $errorArticle ?></div>
                <textarea name="b4" id="b4" class="textarea" cols="30" rows="25"><?= $article ?></textarea>
                <br>
                <input type="submit" class="form-button" value="Publish">
            </form>
                
            <!-- ------------- ARTICLE FORM END ---------------------------- -->


            <!-- ------------- CATEGORY FORM BEGIN ------------------------- -->

            <form class="category-form" action="" method="POST">

                <div class="form-heading">Create a new category</div>
                
                <input type="hidden" name="categoryForm">
                <br>
                <label for="b5">Name the new category</label>
                <div class="error"><?= $errorCategory ?></div>
                <input type="text" class="form-text" name="b5" id="b5" placeholder="Category name">
                <br>
                <input type="submit" class="form-button" value="Create category">
            </form>

            <!-- ------------- CATEGORY FORM END --------------------------- -->
            
        </div>         

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