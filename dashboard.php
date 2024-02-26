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
                $category           = NULL;
                $title              = NULL;
                $article            = NULL;
                $imagePath          = NULL;

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
/*
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_SESSION);					
if(DEBUG_A)	    echo "</pre>";
*/

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
				
				#*****************************************************#
				#******** FETCH USER DATA FROM DB FOR GREETING *******#
				#*****************************************************#

                #****************************************#
				#************ DB OPERATIONS *************#
				#****************************************#

                // Step 1 DB: Connect to database

                $PDO = dbConnect('blogprojekt');

                #************ FETCH DATA FROM DB *************#
if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                $sql = 'SELECT userFirstName, userLastName FROM users WHERE userID = :userID';

                $placeholders = array('userID' => $userID);

                // Step 3 DB: Prepared Statement

                try {
                    // Prepare: prepare the SQL-Statement
                    $PDOStatement = $PDO -> prepare($sql);
                    
                    // Execute: execute the SQL-Statement and include the placeholder
                    $PDOStatement -> execute($placeholders);
                    // showQuery($PDOStatement);
                    
                } catch(PDOException $error) {
if(DEBUG) 		    echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection
                $dbUserArray = $PDOStatement -> fetch(PDO::FETCH_ASSOC);

                // close DB connection
                dbClose($PDO, $PDOStatement);
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$dbUserArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($dbUserArray);					
if(DEBUG_A)	echo "</pre>";
*/


                // create variables for greeting

                $userFirstName  = $dbUserArray['userFirstName'];
                $userLastName   = $dbUserArray['userLastName'];

#*************************************************************************#
				
				#***************************************************#
				#******** PROCESS URL PARAMETERS FOR LOGOUT ********#
				#***************************************************#

                #******** PREVIEW URL PARAMETERS *******************#
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

				#****************************************#
				#****** FETCH CATEGORIES FROM DB ********#
				#****************************************#

				#****************************************#
				#************* DB OPERATION *************#
				#****************************************#

                //// Step 1 DB: Connect to database

                $PDO = dbConnect('blogprojekt');

                #************ FETCH DATA FROM DB *************#
if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                $sql = 'SELECT catID, catLabel FROM categories';

                $placeholders = array();

                // Step 3 DB: Prepared Statement

                try {
                    // Prepare: prepare the SQL-Statement
                    $PDOStatement = $PDO -> prepare($sql);
                    
                    // Execute: execute the SQL-Statement and include the placeholder
                    $PDOStatement -> execute($placeholders);
                    // showQuery($PDOStatement);
                    
                } catch(PDOException $error) {
if(DEBUG) 		    echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection
                $categoryArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection
                dbClose($PDO, $PDOStatement);

if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($categoryArray);				
if(DEBUG_A)	echo "</pre>";



#*************************************************************************#

				#****************************************#
				#******** PROCESS CATEGORY FORM *********#
				#****************************************#

                #******** PREVIEW POST ARRAY ************#
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($_POST);					
if(DEBUG_A)	echo "</pre>";
*/

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['categoryForm']) === true ) {
if(DEBUG)		    echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The form 'categoryForm' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";									

                    // Step 2 FORM: Read, sanitize and output form data

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The form data is being read and sanitized... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $newCategory = sanitizeString($_POST['b5']);

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$newCategory: $newCategory <i>(" . basename(__FILE__) . ")</i></p>\n";

                    // Step 3 FORM: Field validation

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $errorCategory = validateInputString( $newCategory );

                    // Final form validation

                    if( $errorCategory !== NULL ) {
                        // error
if(DEBUG)	            echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The form contains errors! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                        $errorCategory = 'Please enter a category of up to 256 characters.';

                    } else {
                        // success
if(DEBUG)	            echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The form is formally free of errors. <i>(" . basename(__FILE__) . ")</i></p>\n";

                        // Step 4 FORM: data processing

                        #****************************************#
				        #************ DB OPERATIONS *************#
				        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect('blogprojekt');

                        #************ 1. CHECK WHETHER CATEGORY ALREADY EXISTS IN DB *************#

if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Checking whether the category exists in the database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                        // Step 2 DB: Create the SQL-Statement and a placeholder-array

                        $sql = 'SELECT COUNT(catLabel) FROM categories WHERE catLabel = :catLabel';

                        $placeholders = array('catLabel' => $newCategory );

                        // Step 3 DB: Prepared Statements

                        try {
                            // Prepare: prepare the SQL-Statement
                            $PDOStatement = $PDO->prepare($sql);
                            
                            // Execute: execute the SQL-Statement and include the placeholder
                            $PDOStatement->execute($placeholders);
                            // showQuery($PDOStatement);
                            
                        } catch(PDOException $error) {
if(DEBUG) 		            echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                        }

                        // Step 4 DB: evaluate the DB-operation 

                        $count = $PDOStatement -> fetchColumn();

if(DEBUG_V)	            echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$count: $count <i>(" . basename(__FILE__) . ")</i></p>\n";

                        if( $count !== 0 ) {
                            // error
if(DEBUG)	                echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The catefory already exists in the database! <i>(" . basename(__FILE__) . ")</i></p>\n";

                            $errorCategory = 'This category already exists.';

                        } else {
                            // success
if(DEBUG)	                echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The category does not exist in the database. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                            #************ 2. SAVE THE CATEGORY TO DB *************#

if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Saving category to the database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                            // Step 2 DB: Create the SQL-Statement and a placeholder-array

                            $sql = 'INSERT INTO categories (catLabel) VALUES (:catLabel)';

                            $placeholders = array('catLabel' => $newCategory);

                            // Step 3 DB: Prepared Statements

                            try {
                                // Prepare: prepare the SQL-Statement
                                $PDOStatement = $PDO->prepare($sql);
                                
                                // Execute: execute the SQL-Statement and include the placeholder
                                $PDOStatement->execute($placeholders);
                                // showQuery($PDOStatement);
                                
                            } catch(PDOException $error) {
if(DEBUG) 		                echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                            }

                            // Step 4 DB: evaluate the DB-operation

                            $rowCount = $PDOStatement -> rowCount();

if(DEBUG_V)	                echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";

                            if( $rowCount !== 1 ) {
                                // error
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Error when attempting to save $rowCount category! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                $dbError = 'The category could not be saved. Please try again later.'
                                ;
                                // TODO: Log to error log

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount category was saved to the database. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                $dbSuccess = 'The new category has been saved.';

                                #************ 3. UPDATE THE CATEGORY SELECTION *************#

                                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                                $sql = 'SELECT catID, catLabel FROM categories';

                                $placeholders = array();

                                // Step 3 DB: Prepared Statements

                                try {
                                    // Prepare: prepare the SQL-Statement
                                    $PDOStatement = $PDO->prepare($sql);
                                    
                                    // Execute: execute the SQL-Statement and include the placeholder
                                    $PDOStatement->execute($placeholders);
                                    // showQuery($PDOStatement);
                                    
                                } catch(PDOException $error) {
if(DEBUG) 		                echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                                }

                                // Step 4 DB: evaluate the DB-operation and close the DB connection

                                $categoryArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                                // close the DB connection
                                dbClose($PDO, $PDOStatement);

if(DEBUG_A)	                    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	                    print_r($categoryArray);					
if(DEBUG_A)	                    echo "</pre>";


                            } //2. SAVE THE CATEGORY TO DB

                        } // 1. CHECK WHETHER CATEGORY ALREADY EXISTS IN DB

                    } // FINAL FORM VALIDATION

                } // PROCESS CATEGORY FORM END


#*************************************************************************#

				#****************************************#
				#******** PROCESS ARTICLE FORM **********#
				#****************************************#

                #******** PREVIEW POST ARRAY ************#
/*
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_POST);					
if(DEBUG_A)	    echo "</pre>";
*/


                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['articleForm']) === true ) {
if(DEBUG)		    echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The form 'articleForm' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";									
                    
                    // Step 2 FORM: Read, sanitize and output form data

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Reading and sanitizing form data... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $category   = sanitizeString($_POST['b1']);
                    $title      = sanitizeString($_POST['b2']);
                    $alignment  = sanitizeString($_POST['b3']);
                    $article    = sanitizeString($_POST['b4']);

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$category: $category <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$title: $title <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$alignment: $alignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$article: $article <i>(" . basename(__FILE__) . ")</i></p>\n";

                    // Step 3 FORM: Field validation

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $errorCategory  = validateInputString( $category );
                    $errorTitle     = validateInputString( $title );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment = validateInputString( $alignment, minLength:4, maxLength:5 );
                    $errorArticle   = validateInputString( $article );

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorCategory  !== NULL OR 
                        $errorTitle     !== NULL OR 
                        $errorAlignment !== NULL OR
                        $errorArticle   !== NULL ) 
                    {
                        // error
if(DEBUG)	            echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION 1: The form contains errors! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                    } else {
                        // success
if(DEBUG)	            echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION 1: The form is formally free of errors. <i>(" . basename(__FILE__) . ")</i></p>\n";

                        #****************************************#
				        #************ IMAGE UPLOAD **************#
				        #****************************************#

                        #************ PREVIEW IMAGE ARRAY **************************#
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($_FILES);					
if(DEBUG_A)	echo "</pre>";
*/

                        #************ CHECK IF IMAGE UPLOAD IS ACTIVE **************#

                        if( $_FILES['image']['tmp_name'] === '') {
                            // image upload is not active
if(DEBUG)	                echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Image upload is NOT active! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                        } else {
                            // image upload is active
if(DEBUG)	                echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Image upload is active. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                            #************ VALIDATE IMAGE UPLOAD ********************#

                            $validatedImageArray = validateImageUpload( $_FILES['image']['tmp_name'] );
/*
if(DEBUG_A)	                echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validatedImageArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	                print_r($validatedImageArray);					
if(DEBUG_A)	                echo "</pre>";
*/

                            if( $validatedImageArray['imageError'] !== NULL ) {
                                // error
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: ERROR with image upload: $validatedImageArray[imageError] <i>(" . basename(__FILE__) . ")</i></p>\n";	
                                $errorImage = $validatedImageArray['imageError'];

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Image was successfully uploaded to $validatedImageArray[imagePath]. <i>(" . basename(__FILE__) . ")</i></p>\n";		

                                $imagePath = $validatedImageArray['imagePath'];

                            } // VALIDATE IMAGE UPLOAD

                        } // IMAGE UPLOAD END

                        #**************** FINAL FORM VALIDATION 2 (IMAGE UPLOAD VALIDATION) *****************#

                        if( $errorImage !== NULL ) {
                            // error
if(DEBUG)	                echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION 2: The form contains errors! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                        } else {
                            // success
if(DEBUG)	                echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION 2: The form is completely free of errors. <i>(" . basename(__FILE__) . ")</i></p>\n";	


                            // Step 4 FORM: data processing
if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The form data is being further processed... <i>(" . basename(__FILE__) . ")</i></p>\n";
                            #**************** UPLOAD DATA TO DATABASE *****************#
if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Uploading form data to database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                            #****************************************#
				            #************ DB OPERATION **************#
				            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect('blogprojekt');

                            // Step 2 DB: Create the SQL-Statement and a placeholder-array

                            $sql = 'INSERT INTO blogs 
                                    (blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
                                    VALUES
                                    (:blogHeadline, :blogImagePath, :blogImageAlignment, :blogContent, :catID, :userID)';

                            $placeholders = array(  'blogHeadline'          => $title, 
                                                    'blogImagePath'         => $imagePath, 
                                                    'blogImageAlignment'    => $alignment, 
                                                    'blogContent'           => $article, 
                                                    'catID'                 => $category, 
                                                    'userID'                => $userID );

                            // Step 3 DB: Prepared Statements

                            try {
                                // Prepare: prepare the SQL-Statement
                                $PDOStatement = $PDO -> prepare($sql);
                                
                                // Execute: execute the SQL-Statement and include the placeholder
                                $PDOStatement -> execute($placeholders);
                                // showQuery($PDOStatement);
                                
                            } catch(PDOException $error) {
if(DEBUG) 		                echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
                            }

                            // Step 4 DB: evaluate the DB-operation and close the DB connection

                            $rowCount = $PDOStatement -> rowCount();

if(DEBUG_V)	                echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";

                            if( $rowCount !== 1 ) {
                                // error
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The blog article could not be saved to the database! <i>(" . basename(__FILE__) . ")</i></p>\n";	
                                $dbError = 'The blog article could not be saved. Please contact your admin.';

                                // TODO: log to error log

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount blog article has been successfully saved to the database. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                $dbSuccess = 'Your blog article has been published.';

                                dbClose($PDO,$PDOStatement); 

                                $category   = NULL;
                                $title      = NULL;
                                $alignment  = NULL;
                                $article    = NULL;

                            } // UPLOAD DATA TO DATABASE

                        } // FINAL FORM VALIDATION 2 END

                    } // FINAL FORM VALIDATION 1 END

                } // PROCESS ARTICLE FORM END

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
                <!-- ------------- USER MESSAGES BEGIN ------------------------------- -->

                <?php if( isset($dbError) === true ): ?>
                    <h3 class="error"><?= $dbError?></h3>
                <?php elseif( isset($dbSuccess) === true ): ?>
                    <h3 class="success"><?= $dbSuccess?></h3>
                <?php endif ?>

                <!-- ------------- USER MESSAGES END --------------------------------- -->
            </div>

        </header>
    <!-- ------------- HEADER END ---------------------------------- -->

        <div class="forms">

            <!-- ------------- ARTICLE FORM BEGIN ------------------------- -->

            <form class="article-form" action="" method="POST" enctype="multipart/form-data">
                <div class="form-heading">Write a new blog article</div>
                <br>
                <input type="hidden" name="articleForm">


                <!-- ------------- Category ------------- -->
                <label for="b1">Choose a category</label>
                <select name="b1" id="b1" class="form-text">
                    <?php foreach( $categoryArray AS $value ): ?>
                        <option value="<?= $value['catID'] ?>" <?php if($value['catID'] == $category) echo 'selected'?>">
                            <?= $value['catLabel'] ?>
                        </option>
                    <?php endforeach ?>
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
                        <option value="left" <?php if( $alignment === 'left') echo 'selected' ?>>Left</option>
                        <option value="right" <?php if( $alignment === 'right') echo 'selected' ?>>Right</option>
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
            </div>
        </footer>
    <!-- ------------- FOOTER END ---------------------------------- -->
    
    </body>
</html>