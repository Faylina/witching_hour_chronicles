<?php
#*************************************************************************#

				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#

				require_once('./include/config.inc.php');
                require_once('./include/form.inc.php');
                require_once('./include/db.inc.php');
                require_once('./include/dateTime.inc.php');

#*************************************************************************#
				

				#****************************************#
				#********* INITIALIZE VARIABLES *********#
				#****************************************#

                #********* USER VARIABLES ***************#
                $userFirstName          = NULL;
                $userLastName           = NULL;

                #********* CATEGORY VARIABLES ***********#
                $newCategory            = NULL;

                #********* ARTICLE VARIABLES ************#
                $category               = NULL;
                $title                  = NULL;
                $alignment              = NULL;
                $content                = NULL;
                $imagePath              = NULL;

                #********* VIEW & EDIT VARIABLES ********#
                $showView               = false;
                $showEdit               = false;

                #********* ERROR VARIABLES **************#
                $errorTitle             = NULL;
                $errorImage             = NULL;
                $errorContent           = NULL;
                $errorCategory          = NULL;
                $dbError                = NULL;
                $dbSuccess              = NULL;
                $info                   = NULL;

                #********* GENERATE LIST OF ALLOWED MIME TYPES *********#

                $allowedMIMETypes       = implode(', ', array_keys(IMAGE_ALLOWED_MIME_TYPES));
                $mimeTypes              = strtoupper( str_replace( array('image/jpeg, ', 'image/'), '', $allowedMIMETypes));


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

                    #************ GENERATE NEW SESSION ID ***********#
                    session_regenerate_id(true);

                    $userID         = $_SESSION['ID'];
                    $userFirstName  = $_SESSION['firstName'];
                    $userLastName   = $_SESSION['lastName'];

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userID: $userID <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userFirstName: $userFirstName <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userLastName: $userLastName <i>(" . basename(__FILE__) . ")</i></p>\n";
                }         
            

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

                    $errorCategory = validateInputString( $newCategory, minLength:1, maxLength:20 );

                    // FINAL FORM VALIDATION

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
if(DEBUG)	                echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The category already exists in the database! <i>(" . basename(__FILE__) . ")</i></p>\n";

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
                                $PDOStatement = $PDO -> prepare($sql);
                                
                                // Execute: execute the SQL-Statement and include the placeholder
                                $PDOStatement -> execute($placeholders);
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
                                
                                // error message for user
                                $dbError    = 'The category could not be saved. Please try again later.'
                                ;

                                // error message for admin
                                $logError   = 'Error trying to SAVE a CATEGORY to database.';

                                /******** WRITE TO ERROR LOG ******/

                                // create file

                                if( file_exists('./logfiles') === false ) {
                                    mkdir('./logfiles');
                                }

                                // create error message

                                $logEntry    = "\t<p>";
                                $logEntry   .= date('Y-m-d | h:i:s |');
                                $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                $logEntry   .= '<i>' . $logError . '</i>';
                                $logEntry   .= "</p>\n";

                                // write error message to log

                                file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);


                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount category was saved to the database. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                $dbSuccess = "The new category $newCategory has been saved.";

                                $newCategory = NULL;

                            } // 2. SAVE THE CATEGORY TO DB END

                        } // 1. CHECK WHETHER CATEGORY ALREADY EXISTS IN DB END

                        // close the DB connection
                        dbClose($PDO, $PDOStatement);

                    } // FINAL FORM VALIDATION END

                } // PROCESS CATEGORY FORM END


#*************************************************************************#


				#****************************************#
				#****** FETCH CATEGORIES FROM DB ********#
				#****************************************#

				#****************************************#
				#************* DB OPERATIONS ************#
				#****************************************#

if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Begin database operation to fetch categories for the form... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 1 DB: Connect to database

                $PDO = dbConnect('blogprojekt');

                #************ FETCH DATA FROM DB *************#
if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching category data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                $sql = 'SELECT catID, catLabel FROM categories';

                $placeholders = array();

                // Step 3 DB: Prepared Statements

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
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($categoryArray);				
if(DEBUG_A)	echo "</pre>";
*/


#*************************************************************************#


				#******************************************#
				#******** PROCESS BLOG POST FORM **********#
				#******************************************#

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
                    $content    = sanitizeString($_POST['b4']);

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$category: $category <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$title: $title <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$alignment: $alignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$content: $content <i>(" . basename(__FILE__) . ")</i></p>\n";

                    // Step 3 FORM: Field validation

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $errorCategory  = validateInputString( $category, minLength:1, maxLength:20 );
                    $errorTitle     = validateInputString( $title, minLength:1 );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment = validateInputString( $alignment, minLength:4, maxLength:5 );
                    $errorContent   = validateInputString( $content, minLength:1, maxLength:10000 );

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorCategory  !== NULL OR 
                        $errorTitle     !== NULL OR 
                        $errorAlignment !== NULL OR
                        $errorContent   !== NULL ) 
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

                            } // VALIDATE IMAGE UPLOAD END

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
				            #************ DB OPERATIONS *************#
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
                                                    'blogContent'           => $content, 
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
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The blog post could not be saved to the database! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                // error message for user
                                $dbError    = 'The blog post could not be saved. Please contact your admin.';

                                // error message for admin
                                $logError   = 'Error trying to SAVE a BLOG POST to database.';

                                /******** WRITE TO ERROR LOG ******/

                                // create file

                                if( file_exists('./logfiles') === false ) {
                                    mkdir('./logfiles');
                                }

                                // create error message

                                $logEntry    = "\t<p>";
                                $logEntry   .= date('Y-m-d | h:i:s |');
                                $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                $logEntry   .= '<i>' . $logError . '</i>';
                                $logEntry   .= "</p>\n";

                                // write error message to log

                                file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount blog article has been successfully saved to the database. <i>(" . basename(__FILE__) . ")</i></p>\n";

                                $dbSuccess = 'Your blog article has been published.'; 

                                // reset form
                                $category   = NULL;
                                $title      = NULL;
                                $alignment  = NULL;
                                $content    = NULL;

                            } // UPLOAD DATA TO DATABASE END

                            // close the DB connection
                            dbClose($PDO, $PDOStatement);

                        } // FINAL FORM VALIDATION 2 END

                    } // FINAL FORM VALIDATION 1 END

                } // PROCESS ARTICLE FORM END

#*************************************************************************#



                #****************************************#
				#********* PROCESS EDIT FORM ************#
				#****************************************#

                #********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_POST);					
if(DEBUG_A)	    echo "</pre>";
*/


                #************ FORM PROCESSING ***********#

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['editForm']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The form 'editForm' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";	
                    
                    // Step 2 FORM: Read, sanitize and output form data
                    
if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Reading and sanitizing form data... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    $editedCategory     = sanitizeString($_POST['b8']);
                    $editedTitle        = sanitizeString($_POST['b9']);
                    $editedAlignment    = sanitizeString($_POST['b10']);
                    $editedContent      = sanitizeString($_POST['b11']);
                    $editedBlogID       = sanitizeString($_POST['b12']);
                    $editedImagePath    = sanitizeString($_POST['b13']);

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedCategory: $editedCategory <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedTitle: $editedTitle <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedAlignment: $editedAlignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedContent: $editedContent <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedBlogID: $editedBlogID <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$editedImagePath: $editedImagePath <i>(" . basename(__FILE__) . ")</i></p>\n";

                    // Step 3 FORM: Field validation

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $errorCategory              = validateInputString( $editedCategory, minLength:1, maxLength:20 );
                    $errorTitle                 = validateInputString( $editedTitle, minLength:1 );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment             = validateInputString( $editedAlignment, minLength:4, maxLength:5 );
                    $errorContent               = validateInputString( $editedContent, minLength:1, maxLength:10000 );
                    $errorEditedBlogID          = validateInputString( $editedBlogID, minLength:1, maxLength:11 );
                    $errorEditedImagePath   = validateInputString( $editedImagePath, mandatory:false );

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorCategory              !== NULL OR 
                        $errorTitle                 !== NULL OR 
                        $errorAlignment             !== NULL OR
                        $errorContent               !== NULL OR
                        $errorEditedBlogID          !== NULL OR 
                        $errorEditedImagePath   !== NULL ) 
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

                                #*********** DELETE OLD IMAGE FROM SERVER ************#

                                if( @unlink( $editedImagePath) === false ) {
                                    // error
if(DEBUG)	                        echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Error when attempting to delete the old image at <i>'$editedImagePath'</i>! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                    // error message for admin
                                    $logError   = 'Error trying to DELETE an OLD IMAGE from server.';

                                    /******** WRITE TO ERROR LOG ******/

                                    // create file

                                    if( file_exists('./logfiles') === false ) {
                                        mkdir('./logfiles');
                                    }

                                    // create error message

                                    $logEntry    = "\t<p>";
                                    $logEntry   .= date('Y-m-d | h:i:s |');
                                    $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                    $logEntry   .= '<i>' . $logError . '</i>';
                                    $logEntry   .= "</p>\n";

                                    // write error message to log

                                    file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

                                } else {
                                    // success
if(DEBUG)	                        echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Old image at <i>'$editedImagePath'</i> successfully deleted. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                } // DELETE OLD IMAGE FROM SERVER END

                                $editedImagePath = $validatedImageArray['imagePath'];

                            } // VALIDATE IMAGE UPLOAD END

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

if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Updating blog post... <i>(" . basename(__FILE__) . ")</i></p>\n";


                            #****************************************#
				            #************ DB OPERATIONS *************#
				            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect('blogprojekt');

                            // Step 2 DB: Create the SQL-Statement and a placeholder-array

                            $sql = 'UPDATE blogs 
                                    SET blogHeadline        = :blogHeadline,
                                        blogImagePath       = :blogImagePath,
                                        blogImageAlignment  = :blogImageAlignment,
                                        blogContent         = :blogContent,
                                        catID               = :catID
                                    WHERE blogID            = :blogID';


                            $placeholders = array(  'blogHeadline'          => $editedTitle, 
                                                    'blogImagePath'         => $editedImagePath, 
                                                    'blogImageAlignment'    => $editedAlignment, 
                                                    'blogContent'           => $editedContent, 
                                                    'catID'                 => $editedCategory,
                                                    'blogID'                => $editedBlogID);

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
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The blog post could not be updated to the database! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                // error message for user
                                $dbError    = 'The blog post could not be updated. Please contact your admin.';

                                // error message for admin
                                $logError   = 'Error trying to UPDATE a BLOG POST to database.';

                                /******** WRITE TO ERROR LOG ******/

                                // create file

                                if( file_exists('./logfiles') === false ) {
                                    mkdir('./logfiles');
                                }

                                // create error message

                                $logEntry    = "\t<p>";
                                $logEntry   .= date('Y-m-d | h:i:s |');
                                $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                $logEntry   .= '<i>' . $logError . '</i>';
                                $logEntry   .= "</p>\n";

                                // write error message to log

                                file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount blog article has been successfully updated. <i>(" . basename(__FILE__) . ")</i></p>\n";

                                $dbSuccess = 'Your blog article has been updated.'; 

                            } // UPLOAD DATA TO DATABASE END

                            // close the DB connection
                            dbClose($PDO, $PDOStatement);

                        } // FINAL FORM VALIDATION 2 END

                    } // FINAL FORM VALIDATION 1 END

                } // PROCESS ARTICLE FORM END


#*************************************************************************#


                #****************************************#
				#******* FETCH BLOG DATA FROM DB ********#
				#****************************************#

				#****************************************#
				#************* DB OPERATIONS ************#
				#****************************************#

if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Begin database operation to fetch blog data... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 1 DB: Connect to database

                $PDO = dbConnect('blogprojekt');

                #************ FETCH DATA FROM DB *************#
if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching blog data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                $sql = 'SELECT userID, userFirstName, userLastName, userCity, blogID, blogHeadline, blogImagePath, blogImageAlignment, blogContent, blogDate, catID, catLabel
                        FROM blogs 
                        INNER JOIN users USING(userID)
                        INNER JOIN categories USING(catID)
                        ORDER BY blogDate DESC';

                $placeholders = array();

                // Step 3 DB: Prepared Statements

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

                $blogArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection

                dbClose($PDO, $PDOStatement);
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($blogArray);				
if(DEBUG_A)	echo "</pre>";
*/


#*************************************************************************#


                #********************************************#
				#******** PROCESS VIEW & EDIT FORM **********#
				#********************************************#

                #******** PREVIEW POST ARRAY ****************#
/*
if(DEBUG_A)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	print_r($_POST);					
if(DEBUG_A)	echo "</pre>";
*/

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['previousPostsForm']) === true ) {
if(DEBUG)		    echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The form 'previousPostsForm' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";									
                                            
                    // Step 2 FORM: Read, sanitize and output form data
                        
if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Reading and sanitizing form data... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    $chosenBlog     = sanitizeString($_POST['b6']);
                    $operation      = sanitizeString($_POST['b7']);

                    
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$chosenBlog: $chosenBlog <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$operation: $operation <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    // Step 3 FORM: Field validation
                    
if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    $errorChosenBlog    = validateInputString($chosenBlog, minLength:1, maxLength:11);
                    $errorOperation     = validateInputString($operation, minLength:4, maxLength:6);
                    
                    // FINAL FORM VALIDATION
                    
                    if( $errorChosenBlog !== NULL OR $errorOperation !== NULL ) {
                        // error
if(DEBUG)	            echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The edit form contains errors! <i>(" . basename(__FILE__) . ")</i></p>\n";	
                    
                    } else {
                        //success
if(DEBUG)	            echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The form is formally free of errors. <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                    
                        // Step 4 FORM: data processing
if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The form data is being further processed... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                        // PROCESS OPERATIONS

                        if( $operation === 'view' ) {

                            $showView = true; 

                        } elseif( $operation === 'edit' ) {

                            // retrieve the ID of the blog post's author

                            foreach( $blogArray AS $value ) {

                                // find the blog in the blogArray that was chosen for editing
                                if ( $value['blogID'] == $chosenBlog ) {
                                    // retrieve the user ID of the blog post to be edited
                                    $blogUserID = $value['userID'];
                                }
                            }

                            // check whether the user is the author of the blog post
                            if( $blogUserID !== $userID ) {
                                // the user is not the author of the chosen blog post -> editing is prevented

                                $info = 'You have no permission to edit this post.';

                            } else {
                                // the user is the author of the chosen blog post -> editing is allowed

                                $showEdit = true;
                            }


                        } elseif( $operation === 'delete' ) {
                    
                            #****************************************#
                            #************ DB OPERATIONS *************#
                            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect('blogprojekt');

                            #************ DELETE DATA FROM DB *************#
if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Deleting data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                            // Step 2 DB: Create the SQL-Statement and a placeholder-array

                            $sql = 'DELETE FROM blogs WHERE blogID = :blogID';

                            $placeholders = array('blogID' => $chosenBlog);

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
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Deletion failed! <i>(" . basename(__FILE__) . ")</i></p>\n";	
                    
                                // error message for user
                                $dbError = 'The blog post could not be deleted. Please contact your admin.';

                                // error message for admin
                                $logError   = 'Error trying to DELETE a BLOG POST to database.';

                                /******** WRITE TO ERROR LOG ******/

                                // create file

                                if( file_exists('./logfiles') === false ) {
                                    mkdir('./logfiles');
                                }
                    
                                // create error message

                                $logEntry    = "\t<p>";
                                $logEntry   .= date('Y-m-d | h:i:s |');
                                $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                $logEntry   .= '<i>' . $logError . '</i>';
                                $logEntry   .= "</p>\n";

                                // write error message to log

                                file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);
                    
                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $rowCount blog post has been successfully deleted. <i>(" . basename(__FILE__) . ")</i></p>\n";	
                    
                                $dbSuccess = 'The blog post has been successfully deleted.';
                    
                            }
                    
                            // close DB connection
                            dbClose($PDO, $PDOStatement);

                        } // PROCESS OPERATIONS END
                    
                    } // FINAL FORM VALIDATION END

                } // PROCESS VIEW & EDIT FORM END
                    
                    
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


        <!-- ------------- USER MESSAGE BEGIN ---------------------------------- -->

        <?php if( $dbError !== NULL OR $dbSuccess !== NULL OR $info !== NULL ): ?>
            <popupBox>
                <!-- Message -->
                <?php if( $dbError ):?>
                    <h3 class="popup-error"><?= $dbError ?></h3>
                <?php elseif( $dbSuccess ): ?>
                    <h3 class="popup-success"><?= $dbSuccess ?></h3>
                <?php elseif( $info ): ?>
                    <h3 class="popup-error"><?= $info ?></h3>
                <?php endif ?>

                <!-- Button -->
                <?php if( $dbError OR $dbSuccess OR $info ): ?>
                    <a class="button" onclick="document.getElementsByTagName('popupBox')[0].style.display = 'none'">Okay</a>
                <?php endif ?>
            </popupBox> 
        <?php endif ?>

        <!-- ------------- USER MESSAGE END ------------------------------------ -->


        <!-- ------------- MAIN CONTENT BEGIN ---------------------------------- -->

        <div class="forms">

            
            <?php if( $showView === true ): ?>

                <!-- ------------- BLOG POST BEGIN ---------------------------------- -->

                <div class="blog">

                    <!-- -------- Generate blog articles ---------- -->
                    <?php foreach( $blogArray AS $value): ?>

                        <?php if( $value['blogID'] == $chosenBlog ): ?>

                            <!-- Convert ISO time from DB to EU time and split into date and time -->
                            <?php $dateArray = isoToEuDateTime( $value['blogDate'] ) ?>

                            <!-- Link to create new post -->
                            <a href="dashboard.php"><< Write a new blog post</a>

                            <!-- Blog header -->
                            <div class="blog-category">Category: <?= $value['catLabel'] ?></div>
                            <div class="blog-title"><?= $value['blogHeadline'] ?></div>
                            <div class="blog-meta">
                                <?= $value['userFirstName'] ?> <?= $value['userLastName'] ?> (<?= $value['userCity'] ?>) 
                                wrote on <?= $dateArray['date'] ?> at <?= $dateArray['time'] ?> o'clock:
                            </div>

                            <!-- Blog content -->
                            <div class="container clearfix">
                                <!-- Prevent empty images from displaying --> 
                                <?php if( $value['blogImagePath'] !== NULL ): ?>
                                    <img class="<?= $value['blogImageAlignment']?>" src="<?= $value['blogImagePath']?>" alt="image for the blog article">
                                <?php endif ?>

                                <div class="blog-content"><?php echo nl2br( $value['blogContent'] ) ?></div>
                            </div>

                            <br>
                            <hr>
                            <br>

                        <?php endif ?>

                    <?php endforeach ?>
                </div>
                <!-- ------------- BLOG POST END ------------------------------------ -->
                        

            <?php elseif( $showEdit === true ): ?>

                <div class="article-form">   

                    <?php foreach( $blogArray AS $value): ?>

                        <?php if( $value['blogID'] == $chosenBlog ): ?>

                            <!-- ------------- EDIT FORM BEGIN ------------------------- -->

                            <form action="" class="edit-form" method="POST" enctype="multipart/form-data">

                                <!-- Link to create new post -->
                                <a href="dashboard.php"><< Write a new blog post</a>
                                <br>
                                <div class="form-heading">Edit blog post</div>
                                <br>
                                <input type="hidden" name="editForm">
                                <input type="hidden" name="b12" value="<?= $value['blogID'] ?>">
                                <input type="hidden" name="b13" value="<?= $value['blogImagePath']?>">

                                <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                                <!-- ------------- Category ------------- -->
                                <label for="b8">Choose a category</label>
                                <select name="b8" id="b8" class="form-text">
                                    <?= $value['catID'] ?>
                                    <?php foreach( $categoryArray AS $dbCategory ): ?>
                                        <option value="<?= $dbCategory['catID'] ?>" <?php if($dbCategory['catID'] == $value['catID']) echo 'selected'?>>
                                            <?= $dbCategory['catLabel'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>

                                <br>
                                <!-- ------------- Title ---------------- -->
                                <label for="b9">Write the title of your post</label>
                                <div class="error"><?= $errorTitle ?></div>
                                <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $value['blogHeadline'] ?>">

                                <br>
                                <!-- ------------- Image Upload ---------- -->
                                <fieldset>
                                    <legend>Upload an image</legend>

                                    <!-- ------------- Database Image ---------- -->

                                    <?php if( $value['blogImagePath'] !== NULL ): ?>
                                        <img class="left" src="<?= $value['blogImagePath']?>" alt="image for the blog article">
                                    <?php endif ?>

                                    <!-- ------------- Image Info Text ---------- -->
                                    <p class="image-info">
                                        You may upload an image of the type <?= $mimeTypes ?>. <br>
                                        The width of the image may not exceed <?= IMAGE_MAX_WIDTH ?> pixels. <br>
                                        The height of the image may not exceed <?= IMAGE_MAX_HEIGHT ?> pixels. <br>
                                        The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024/1000 ?> MB.
                                    </p>
                                    <br>
                                    <!-- ------------- Image Upload ---------- -->
                                    <div class="error"><?= $errorImage ?></div>
                                    <input type="file" name="image">
                                    <br>
                                    <br>
                                    <!-- ------------- Image Alignment ---------- -->
                                    <label for="b10">Choose the alignment of the image</label>
                                    <br>
                                    <select name="b10" id="b10" class="form-select">
                                        <option value="left" <?php if( $value['blogImageAlignment'] === 'left') echo 'selected' ?>>Left</option>
                                        <option value="right" <?php if( $value['blogImageAlignment'] === 'right') echo 'selected' ?>>Right</option>
                                    </select>
                                    <br>
                                </fieldset>
                                <br>

                                <!-- ------------- Content ------------------ -->
                                <label for="b11">Write your blog post</label>
                                <div class="error"><?= $errorContent ?></div>
                                <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $value['blogContent'] ?></textarea>
                                <br>
                                <input type="submit" class="form-button" value="Publish">
                            </form>
                            <!-- ------------- EDIT FORM END ---------------------------- -->

                        <?php endif ?>
                    <?php endforeach ?>
                </div>

            <?php else: ?>

                <!-- ------------- BLOG POST FORM BEGIN ------------------------- -->

                <form class="article-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-heading">Write a new blog post</div>
                    <br>
                    <input type="hidden" name="articleForm">

                    <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                    <!-- ------------- Category ------------- -->
                    <label for="b1">Choose a category</label>
                    <select name="b1" id="b1" class="form-text">
                        <?php foreach( $categoryArray AS $value ): ?>
                            <option value="<?= $value['catID'] ?>" <?php if($value['catID'] == $category) echo 'selected'?>>
                                <?= $value['catLabel'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <br>
                    <!-- ------------- Title ---------------- -->
                    <label for="b2">Write the title of your post</label>
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
                            The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024/1000 ?> MB.
                        </p>
                        <br>
                        <!-- ------------- Image Upload ---------- -->
                        <div class="error"><?= $errorImage ?></div>
                        <input type="file" name="image">
                        <br>
                        <br>
                        <!-- ------------- Image Alignment ---------- -->
                        <label for="b3">Choose the alignment of the image</label>
                        <br>
                        <select name="b3" id="b3" class="form-select">
                            <option value="left" <?php if( $alignment === 'left') echo 'selected' ?>>Left</option>
                            <option value="right" <?php if( $alignment === 'right') echo 'selected' ?>>Right</option>
                        </select>
                        <br>
                    </fieldset>
                    <br>

                    <!-- ------------- Content ------------------ -->
                    <label for="b4">Write your blog post</label>
                    <div class="error"><?= $errorContent ?></div>
                    <textarea name="b4" id="b4" class="textarea" cols="30" rows="25"><?= $content ?></textarea>
                    <br>
                    <input type="submit" class="form-button" value="Publish">
                </form>
                    
                <!-- ------------- BLOG POST FORM END ---------------------------- -->

            <?php endif ?>

            <div class="mini-forms">
                <!-- ------------- CATEGORY FORM BEGIN ------------------------- -->

                <form class="category-form" action="" method="POST">

                    <div class="form-heading">Create a new category</div>
                    
                    <input type="hidden" name="categoryForm">
                    <br>
                    <label for="b5">Name the new category</label>
                    <div class="error"><?= $errorCategory ?></div>
                    <input type="text" class="form-text" name="b5" id="b5" placeholder="Category name" value="<?= $newCategory ?>">
                    <br>
                    <input type="submit" class="form-button" value="Create category">

                </form>

                <!-- ------------- CATEGORY FORM END --------------------------- -->


                <!-- ------------- EDIT & VIEW FORM BEGIN ---------------------- -->

                <form class="category-form" action="" method="POST">

                    <div class="form-heading">Previous blog posts</div>
                    
                    <input type="hidden" name="previousPostsForm">
                    <br>
                    <!-- Blog post title -->
                    <label for="b6">Select a blog post</label>
                    <select name="b6" id="b6" class="form-text">
                        <?php foreach( $blogArray AS $value ): ?>
                            <option value="<?= $value['blogID'] ?>">
                                <?= $value['blogHeadline'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <br>
                    <!-- Operation selection -->
                    <div class="radio-buttons">
                        <div>
                            <input type="radio" name="b7" id="view" value="view" checked>
                            <label for="view">View</label>
                        </div>
                        <div>
                            <input type="radio" name="b7" id="edit" value="edit">
                            <label for="edit">Edit</label>
                        </div>
                        <div>
                            <input type="radio" name="b7" id="delete" value="delete">
                            <label for="delete">Delete</label>
                        </div>
                    </div>
                    <br>
                    <input type="submit" class="form-button" value="Proceed">

                </form>

                <!-- ------------- EDIT & VIEW FORM END ------------------------ -->
            </div>
            
        </div>     
        
        <!-- ------------- MAIN CONTENT END ---------------------------------- -->

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