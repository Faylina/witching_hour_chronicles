<?php
#*************************************************************************#

				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#

				require_once('./include/config.inc.php');
                require_once('./include/form.inc.php');
                require_once('./include/db.inc.php');
                require_once('./include/dateTime.inc.php');
                require_once('./include/debugging.inc.php');

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
                $chosenBlog             = NULL;

                #********* ERROR VARIABLES **************#
                $errorTitle             = NULL;
                $errorImage             = NULL;
                $errorContent           = NULL;
                $errorCategory          = NULL;
                $errorNewCategory       = NULL;
                $dbError                = NULL;
                $dbSuccess              = NULL;
                $dbDeleteError          = NULL;
                $dbDeleteSuccess        = NULL;
                $info                   = NULL;
                $alert                  = NULL;

                #********* GENERATE LIST OF ALLOWED MIME TYPES *********#

                $allowedMIMETypes       = implode(', ', array_keys(IMAGE_ALLOWED_MIME_TYPES));
                $mimeTypes              = strtoupper( str_replace( array('image/jpeg, ', 'image/'), '', $allowedMIMETypes));


#*************************************************************************#

				
				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#

                // secure access only for logged-in users of Coding Sorceress
                secureAccess('wwwcodingsorceresscom', 'user', '../../index.php');

                // secure access only for logged-in users of Witching Hour Chronicles

                #****************************************#
                #******** CHECK FOR VALID LOGIN *********#
                #****************************************#

                if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
                    // error
                    debugAuth('User is not logged in.');

                    #************ DENY PAGE ACCESS ***********#

                    // 1. Delete session for Witching Hour Chronicles
                    unset($_SESSION['ID']);

                    // 2. Redirect to homepage
                    header('LOCATION: index.php');

                    // 3. Fallback in case of an error: end processing of the script
                    exit();

                #************ VALID LOGIN ***********#
                } else {
                    // success
                    debugAuth('Valid login.');	

                    #************ GENERATE NEW SESSION ID ***********#
                    session_regenerate_id(true);

                    $userID         = $_SESSION['ID'];
                    $userFirstName  = $_SESSION['firstName'];
                    $userLastName   = $_SESSION['lastName'];

                    debugVariable('userID',         $userID );
                    debugVariable('userFirstName',  $userFirstName );
                    debugVariable('userLastName',   $userLastName );
                    
                }     
            

#*************************************************************************#
				

				#*****************************************#
				#******** PROCESS URL PARAMETERS *********#
				#*****************************************#

                #******** PREVIEW URL PARAMETERS *********#
                debugArray('_GET', $_GET);
				
				// Step 1 URL: Check whether the parameters have been sent

                if( isset($_GET['action']) === true ) {
                    debugProcessStart("URL-parameter 'action' has been committed.");
								
					// Step 2 URL: Read, sanitize and output URL data
					debugProcessStart('The URL parameters are being read and sanitized...');
    
                    $action = sanitizeString($_GET['action']);
                    
                    debugVariable('action', $action);
                    
                    // Step 3 URL: Branching
                    
                    #*************** LOGOUT **************#
                    
                    if( $action === 'logout') {
                    
                        debugProcessStart('Logging out...');
                    
                        // Step 4 URL: processing data
                    
                        // 1. Delete session for Witching Hour Chronicles
                        unset($_SESSION['ID']);
                    
                        // 2. Reload homepage
                        header('LOCATION: index.php');

                        // 3. Fallback in case of an error: end processing of the script
                        exit();


                    #*************** DELETION **************#

                    } elseif( $action === 'delete') {

                        debugProcessStart('Deleting data from database...');

                        // fetch the blogID  and image path (if the post has an image) of the post to be deleted
                        $chosenBlog = $_SESSION['postToBeDeleted'];

                        if($_SESSION['blogImageToBeDeleted']) {
                            $blogImageToBeDeleted = $_SESSION['blogImageToBeDeleted'];

                            debugVariable('blogImageToBeDeleted', $blogImageToBeDeleted);
                        }

                        #****************************************#
                        #************ DB OPERATIONS *************#
                        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect();

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
                            debugErrorDB($error);									
                        }
                        
                        // Step 4 DB: evaluate the DB-operation and close the DB connection
                        $rowCount = $PDOStatement -> rowCount();

                        debugVariable('rowCount', $rowCount);
                        

                        if( $rowCount !== 1 ) {
                            // error
                            debugErrorDB('Deletion failed!');	
                        
                            // error message for user
                            $dbDeleteError = 'The blog post could not be deleted. Please try again later.';

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
                            debugSuccess("$rowCount blog post has been successfully deleted.");
                        
                            $dbDeleteSuccess = 'The blog post has been successfully deleted.';

                            #*********** DELETE OLD IMAGE FROM SERVER ************#

                            if($blogImageToBeDeleted) {
                                
                                if( @unlink( $blogImageToBeDeleted) === false ) {
                                    // error
                                    debugError("Error when attempting to delete the old image at '$blogImageToBeDeleted'");	

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
                                    debugSuccess("Old image at '$blogImageToBeDeleted' has been successfully deleted.");

                                } // DELETE OLD IMAGE FROM SERVER END

                            } // CHECK IF IMAGE EXISTS END

                        } // EVALUATE DB OPERATION END
                        
                        // close DB connection
                        dbClose($PDO, $PDOStatement);


                    #*************** CONFIRMATIONS **************#
                    
                    } elseif( $action === 'cancelDelete' OR $action = 'okay') {
                        debugProcessStart('Reloading page after cancel or confirmation...');

                        // delete blog ID from session
                        $_SESSION['postToBeDeleted'] = '';

                        // 2. Reload homepage
                        header('LOCATION: dashboard.php');

                        // 3. Fallback in case of an error: end processing of the script
                        exit();

                    } // BRANCHING END
                    
                } // PROCESS URL PARAMETERS END

#*************************************************************************#


				#**********************************************#
				#******** PROCESS FORM 'NEW CATEGORY' *********#
				#**********************************************#

                #******** PREVIEW POST ARRAY ************#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['categoryForm']) === true ) {
                    debugProcessStart('The form "formNewCategory" has been sent.');								

                    // Step 2 FORM: Read, sanitize and output form data
                    debugProcessStart('Reading and sanitizing form data...');

                    $newCategory = sanitizeString($_POST['b5']);

                    debugVariable('newCategory', $newCategory);

                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');

                    $errorNewCategory = validateInputString( $newCategory, maxLength:50 );

                    #********** FINAL FORM VALIDATION **********#

                    if( $errorNewCategory !== NULL ) {
                        // error
                        debugError('The form contains errors!');

                        $errorNewCategory = 'Please enter a category.';

                    } else {
                        // success
                        debugSuccess('The form is formally free of errors.');

                        // Step 4 FORM: data processing

                        #****************************************#
				        #************ DB OPERATIONS *************#
				        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect();

                        #************ 1. CHECK WHETHER CATEGORY ALREADY EXISTS IN DB *************#

                        debugProcessStart('Checking whether the category exists in the database...');

                        // Step 2 DB: Create the SQL-Statement and a placeholder-array

                        $sql = 'SELECT COUNT(catLabel) 
                                FROM categories 
                                WHERE catLabel = :catLabel';

                        $placeholders = array('catLabel' => $newCategory );

                        // Step 3 DB: Prepared Statements

                        try {
                            // Prepare: prepare the SQL-Statement
                            $PDOStatement = $PDO->prepare($sql);
                            
                            // Execute: execute the SQL-Statement and include the placeholder
                            $PDOStatement->execute($placeholders);
                            // showQuery($PDOStatement);
                            
                        } catch(PDOException $error) {
                            debugErrorDB($error);										
                        }

                        // Step 4 DB: evaluate the DB-operation 

                        $count = $PDOStatement -> fetchColumn();

                        debugVariable('count', $count);

                        if( $count !== 0 ) {
                            // error
                            debugError("This category already exists.");

                            $errorNewCategory = 'This category already exists.';

                        } else {
                            // success
                            debugSuccess("This category does not exist in the database yet.");

                            #************ 2. SAVE THE CATEGORY TO DB *************#

                            debugProcessStart('Saving category to database...');

                            // Step 2 DB: Create the SQL-Statement and a placeholder-array

                            $sql = 'INSERT INTO categories (catLabel) 
                                    VALUES (:catLabel)';

                            $placeholders = array('catLabel' => $newCategory);

                            // Step 3 DB: Prepared Statements

                            try {
                                // Prepare: prepare the SQL-Statement
                                $PDOStatement = $PDO -> prepare($sql);
                                
                                // Execute: execute the SQL-Statement and include the placeholder
                                $PDOStatement -> execute($placeholders);
                                // showQuery($PDOStatement);
                                
                            } catch(PDOException $error) {
                                debugErrorDB($error);										
                            }

                            // Step 4 DB: evaluate the DB-operation

                            $rowCount = $PDOStatement -> rowCount();

                            debugObject('rowCount', $rowCount);

                            if( $rowCount !== 1 ) {
                                // error
                                debugErrorDB("Error when attempting to save $rowCount category!");
                                
                                // error message for user
                                $dbError    = 'The category could not be saved. Please try again later.'
                                ;

                                // error message for admin
                                $logError   = 'Error trying to SAVE a new CATEGORY to database.';

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
                                debugSuccess("$rowCount category was saved to the database.");

                                $dbSuccess = "The new category $newCategory has been saved.";

                                // clear the form
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

                debugProcessStart('Fetching category data from database...');

                // Step 1 DB: Connect to database

                $PDO = dbConnect();

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                $sql = 'SELECT catID, catLabel 
                        FROM categories';

                $placeholders = array();

                // Step 3 DB: Prepared Statements

                try {
                    // Prepare: prepare the SQL-Statement
                    $PDOStatement = $PDO -> prepare($sql);
                    
                    // Execute: execute the SQL-Statement and include the placeholder
                    $PDOStatement -> execute($placeholders);
                    // showQuery($PDOStatement);
                    
                } catch(PDOException $error) {
                    debugErrorDB($error);									
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection

                $categoryArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection

                dbClose($PDO, $PDOStatement);

                debugArray('categoryArray', $categoryArray);


#*************************************************************************#


				#************************************************#
				#******** PROCESS FORM 'NEW BLOG POST' **********#
				#************************************************#

                #******** PREVIEW POST ARRAY ************#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['articleForm']) === true ) {
                    debugProcessStart('The form "articleForm" has been sent.');     
					
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');

                    $category   = sanitizeString($_POST['b1']);
                    $title      = sanitizeString($_POST['b2']);
                    $alignment  = sanitizeString($_POST['b3']);
                    $content    = sanitizeString($_POST['b4']);

                    debugVariable('category',   $category);
                    debugVariable('title',      $title);
                    debugVariable('alignment',  $alignment);
                    debugVariable('content',    $content);

                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');

                    $errorCategory  = validateInputString( $category, maxLength:11 );
                    $errorTitle     = validateInputString( $title );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment = validateInputString( $alignment, minLength:4, maxLength:5 );
                    $errorContent   = validateInputString( $content, minLength:5, maxLength:20000 );

                    debugArray('categoryArray', $categoryArray);

                    #********** WHITELISTING 1: CHECK IF CATEGORY NAME EXISTS IN DATABASE **********#

                    $key = $category - 1;

                    debugVariable('key', $key);

					if( $category != $categoryArray[$key]['catID']) {
						// error
						debugError('This category does not exist.');
					
						$errorCategory = 'This category does not exist.';
					}
					
					#********** WHITELISTING 2: IMAGE ALIGNMENT ***********#
					
					if( $alignment !== 'left' AND $alignment !== 'right') {
						// error
						debugError('Invalid image alignment.');
					
						$errorAlignment = 'Invalid image alignment.';
					}

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorCategory  !== NULL OR 
                        $errorTitle     !== NULL OR 
                        $errorAlignment !== NULL OR
                        $errorContent   !== NULL ) 
                    {
                        // error
                        debugError('FINAL FORM VALIDATION PART I: The form contains errors!');

                    } else {
                        // success
                        debugSuccess('FINAL FORM VALIDATION PART I: The form is formally free of errors.');	

                        #**************************************************#
				        #************ OPTIONAL: IMAGE UPLOAD **************#
				        #**************************************************#

                        #************ CHECK IF IMAGE UPLOAD IS ACTIVE **************#
                        debugProcessStart('Checking image upload...');

                        if( $_FILES['image']['tmp_name'] === '') {
                            // image upload is not active
                            debugOccurrence('Image upload is inactive');	

                        } else {
                            // image upload is active
                            debugOccurrence('Image upload is active');

                            #************ VALIDATE IMAGE UPLOAD ********************#

                            $validatedImageArray = validateImageUpload( $_FILES['image']['tmp_name'] );

                            debugArray('validatedImageArray', $validatedImageArray);

                            if( $validatedImageArray['imageError'] !== NULL ) {
                                // error
                                debugError("Image upload error: " . $validatedImageArray['imageError']);	

                                $errorImage = $validatedImageArray['imageError'];

                            } elseif( $validatedImageArray['imagePath'] !== NULL ) {
                                // success
                                debugSuccess("The image has successfully saved here:" . $validatedImageArray['imagePath'] . ".");

                                $imagePath = $validatedImageArray['imagePath'];

                                debugVariable('imagePath', $imagePath);

                            } // VALIDATE IMAGE UPLOAD END

                        } // IMAGE UPLOAD END

                        #**************** FINAL FORM VALIDATION 2 (IMAGE UPLOAD VALIDATION) *****************#

                        if( $errorImage !== NULL ) {
                            // error
                            debugError("FINAL FORM VALIDATION PART II: Error for image upload: $validatedImageArray[imageError]");

                        } else {
                            // success
                            debugSuccess('FINAL FORM VALIDATION PART II: The form is completely free of errors.');	

                            // Step 4 FORM: data processing

                            #**************** UPLOAD DATA TO DATABASE *****************#

                            debugProcessStart('Saving new blog post to database...');

                            #****************************************#
				            #************ DB OPERATIONS *************#
				            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect();

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
                                debugErrorDB($error);										
                            }

                            // Step 4 DB: evaluate the DB-operation and close the DB connection

                            $rowCount = $PDOStatement -> rowCount();

                            debugVariable('rowCount', $rowCount);

                            if( $rowCount !== 1 ) {
                                // error
                                debugErrorDB("Error when attempting to save $rowCount category!");

                                // error message for user
                                $dbError    = 'The blog post could not be saved. Please try again later.';

                                // error message for admin
                                $logError   = 'Error trying to SAVE a new BLOG POST to database.';

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
                                debugSuccess("$rowCount blog post has been saved to the database.");

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

                } // PROCESS FORM 'NEW BLOG POST' END

#*************************************************************************#



                #****************************************#
				#********* PROCESS EDIT FORM ************#
				#****************************************#

                #********** PREVIEW POST ARRAY **********#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent
                if( isset($_POST['editForm']) === true ) {

                    debugProcessStart('The form "editForm" has been sent.');
                    
                    // Step 2 FORM: Read, sanitize and output form data
                    debugProcessStart('Reading and sanitizing form data...');
                    
                    $editedCategory     = sanitizeString($_POST['b8']);
                    $editedTitle        = sanitizeString($_POST['b9']);
                    $editedAlignment    = sanitizeString($_POST['b10']);
                    $editedContent      = sanitizeString($_POST['b11']);
                    $editedBlogID       = sanitizeString($_POST['b12']);
                    $editedImagePath    = sanitizeString($_POST['b13']);

                    debugVariable('editedCategory',     $editedCategory);
                    debugVariable('editedTitle',        $editedTitle);
                    debugVariable('editedAlignment',    $editedAlignment);
                    debugVariable('editedContent',      $editedContent);
                    debugVariable('editedBlogID',       $editedBlogID);
                    debugVariable('editedImagePath',    $editedImagePath);

                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');

                    $errorCategory              = validateInputString( $editedCategory, maxLength:11 );
                    $errorTitle                 = validateInputString( $editedTitle );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment             = validateInputString( $editedAlignment, minLength:4, maxLength:5 );
                    $errorContent               = validateInputString( $editedContent, maxLength:10000 );
                    $errorEditedBlogID          = validateInputString( $editedBlogID, maxLength:11 );
                    $errorEditedImagePath       = validateInputString( $editedImagePath, mandatory:false );

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorCategory              !== NULL OR 
                        $errorTitle                 !== NULL OR 
                        $errorAlignment             !== NULL OR
                        $errorContent               !== NULL OR
                        $errorEditedBlogID          !== NULL OR 
                        $errorEditedImagePath       !== NULL ) 
                    {
                        // error
                        debugError('FINAL FORM VALIDATION PART I: The form contains errors!');	

                        $showEdit = true;

                    } else {
                        // success
                        debugSuccess('FINAL FORM VALIDATION PART I: The form is formally free of errors.');	

                        #****************************************#
				        #************ IMAGE UPLOAD **************#
				        #****************************************#

                        debugProcessStart('Checking image upload...');

                        #************ CHECK IF IMAGE UPLOAD IS ACTIVE **************#

                        if( $_FILES['image']['tmp_name'] === '') {
                            // image upload is not active
                            debugOccurrence('Image upload is inactive');

                        } else {
                            // image upload is active
                            debugOccurrence('Image upload is active');

                            #************ VALIDATE IMAGE UPLOAD ********************#

                            $validatedImageArray = validateImageUpload( $_FILES['image']['tmp_name'] );

                            debugArray('validatedImageArray', $validatedImageArray);

                            if( $validatedImageArray['imageError'] !== NULL ) {
                                // error
                                debugError("Image upload error: " . $validatedImageArray['imageError']);	

                                $errorImage = $validatedImageArray['imageError'];

                            } else {
                                // success
                                debugSuccess("The image has successfully saved here:" . $validatedImageArray['imagePath'] . ".");	

                                #*********** DELETE OLD IMAGE FROM SERVER ************#
                                if($editedImagePath !== NULL) {

                                    if( @unlink( $editedImagePath) === false ) {
                                        // error
                                        debugError("Error when attempting to delete the old image at '$editedImagePath'");	

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
                                        debugSuccess("Old image at '$editedImagePath' has been successfully deleted.");

                                    } // DELETE OLD IMAGE FROM SERVER END

                                } // CHECK IF IMAGE EXISTS END

                                $editedImagePath = $validatedImageArray['imagePath'];

                            } // VALIDATE IMAGE UPLOAD END

                        } // IMAGE UPLOAD END

                        #**************** FINAL FORM VALIDATION 2 (IMAGE UPLOAD VALIDATION) *****************#

                        if( $errorImage !== NULL ) {
                            // error
                            debugError("FINAL FORM VALIDATION PART II: Error for image upload: $validatedImageArray[imageError]");

                        } else {
                            // success
                            debugSuccess('FINAL FORM VALIDATION PART II: The form is completely free of errors.');


                            // Step 4 FORM: data processing

                            #**************** UPLOAD DATA TO DATABASE *****************#

                            debugProcessStart('Updating blog post...');

                            #****************************************#
				            #************ DB OPERATIONS *************#
				            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect();

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
                                debugErrorDB($error);									
                            }

                            // Step 4 DB: evaluate the DB-operation and close the DB connection

                            $rowCount = $PDOStatement -> rowCount();

                            debugVariable('rowCount', $rowCount);

                            if( $rowCount !== 1 ) {
                                // error
                                debugErrorDB('The blog post could not be updated.');

                                // error message for user
                                $dbError    = 'The blog post could not be updated. Please try again later.';

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
                                debugSuccess("$rowCount blog post has been successfully updated.");

                                $dbSuccess = 'Your blog post has been updated.'; 

                            } // UPLOAD DATA TO DATABASE END

                            // close the DB connection
                            dbClose($PDO, $PDOStatement);

                        } // FINAL FORM VALIDATION 2 END

                    } // FINAL FORM VALIDATION 1 END

                } // PROCESS EDIT FORM END


#*************************************************************************#


                #****************************************#
				#******* FETCH BLOG DATA FROM DB ********#
				#****************************************#

				#****************************************#
				#************* DB OPERATIONS ************#
				#****************************************#

                debugProcessStart("Fetching blog posts from database...");	

                // Step 1 DB: Connect to database
                $PDO = dbConnect();

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
                    debugErrorDB($error);								
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection

                $blogArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection

                dbClose($PDO, $PDOStatement);

                debugArray('blogArray', $blogArray);


#*************************************************************************#


                #********************************************#
				#******** PROCESS VIEW & EDIT FORM **********#
				#********************************************#

                #******** PREVIEW POST ARRAY ****************#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['previousPostsForm']) === true ) {
                    debugProcessStart('The form "previousPostsForm" has been sent.');
					
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');
                    
                    $chosenBlog     = sanitizeString($_POST['b6']);
                    $operation      = sanitizeString($_POST['b7']);

                    debugVariable('chosenBlog', $chosenBlog);
                    debugVariable('operation', $operation);
                    
                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');
                    
                    $errorChosenBlog    = validateInputString($chosenBlog, maxLength:11);
                    $errorOperation     = validateInputString($operation, minLength:4, maxLength:6);

                    #********** WHITELISTING OPERATION **********#

					if(	$operation !== 'view' AND 
                        $operation !== 'edit' AND 
                        $operation !== 'delete') 
                    {
                        $errorOperation = 'This is not a valid operation.';
                    }
                    

                    #********** FINAL FORM VALIDATION **********#
                    
                    if( $errorChosenBlog !== NULL OR $errorOperation !== NULL ) {
                        // error
                        debugError('The form contains errors!');
                    
                    } else {
                        //success
                        debugSuccess('The form is formally free of errors.');	
                    
                        // Step 4 FORM: data processing

                        #************ VIEW POST ************************#

                        if( $operation === 'view' ) {
                            debugProcessStart('Showing blog post...');

                            $showView = true; 


                        #************ START EDITING PROCESS *************#

                        } elseif( $operation === 'edit' ) {
                            debugProcessStart('Starting editing process...');

                            #********* USER AUTHORIZATION **********#

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
                                debugError('The user is not the author of this post and may not alter the blog post.');	

                                $info = 'You have no permission to edit this post.';

                            } else {
                                // the user is the author of the chosen blog post -> editing is allowed
                                debugSuccess('The user is confirmed to be the author of this post.');

                                $showEdit = true;
                            }

                         #************ START DELETION PROCESS *************#

                        } elseif( $operation === 'delete' ) {
                            debugProcessStart('Starting deletion process...');

                            #********* USER AUTHORIZATION **********#

                            foreach( $blogArray AS $value ) {

                                // find the blog in the blogArray that was chosen for deletion
                                if ( $value['blogID'] == $chosenBlog ) {

                                    // retrieve the user ID of the blog post to be deleted
                                    $blogUserID         = $value['userID'];
                                    $blogTitleToDelete  = $value['blogHeadline'];

                                    if($value['blogImagePath'] !== NULL) {
                                        $blogImageToBeDeleted = $value['blogImagePath'];

                                        debugVariable('blogImageToBeDeleted', $blogImageToBeDeleted);
                                    } 
                                }
                            }

                            // check whether the user is the author of the blog post
                            if( $blogUserID !== $userID ) {
                                // the user is not the author of the chosen blog post -> deletion is prevented
                                debugError('The blog post was not deleted because the user is not the author.');	

                                $info = 'You have no permission to delete this post.';

                            } else {
                                // the user is the author of the chosen blog post -> deletion is allowed
                                debugSuccess('The user is confirmed to be the author of this post.');

                                // store blog ID and image path of the post to be deleted in session
                                $_SESSION['postToBeDeleted'] = $chosenBlog;

                                if($blogImageToBeDeleted) {
                                    $_SESSION['blogImageToBeDeleted'] = $blogImageToBeDeleted;
                                }

                                $alert = "Do you really want to delete the blog post '$blogTitleToDelete'?";

                            } // USER AUTHORIZATION END

                        } // PROCESS OPERATIONS END
                    
                    } // FINAL FORM VALIDATION END

                } // PROCESS VIEW & EDIT FORM END
                    
                    
#*************************************************************************#
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="./css/images/favicon.ico">
        <title>Witching Hour Chronicles - Dashboard</title>
        <link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
    </head>

    <body>
        <!-- ------------- LINK TO THE CODING SORCERESS BEGIN ------------------------- -->
         
        <div class="coding-sorceress">
            <a href="../../portfolio.php#projects"><< Go back to The Coding Sorceress</a>
         </div>

        <!-- ------------- LINK TO THE CODING SORCERESS END --------------------------- -->
         

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

        <?php if(   $dbError            !== NULL OR 
                    $dbSuccess          !== NULL OR 
                    $info               !== NULL OR 
                    $alert              !== NULL OR 
                    $dbDeleteError      !== NULL OR  
                    $dbDeleteSuccess    !== NULL ): ?>
            <div class="overlay">
                <popupBox>
                    <!-- Message -->
                    <?php if( $dbError ):?>
                        <h3 class="popup-error"><?= $dbError ?></h3>
                    <?php elseif( $dbSuccess ): ?>
                        <h3 class="popup-success"><?= $dbSuccess ?></h3>
                    <?php elseif( $dbDeleteError ): ?>
                        <h3 class="popup-error"><?= $dbDeleteError ?></h3>
                    <?php elseif( $dbDeleteSuccess ): ?>
                        <h3 class="popup-success"><?= $dbDeleteSuccess ?></h3>
                    <?php elseif( $info ): ?>
                        <h3 class="popup-error"><?= $info ?></h3>
                    <?php elseif( $alert ): ?>
                        <h3 class="popup-success"><?= $alert ?></h3>
                    <?php endif ?>

                    <!-- Button -->
                    <?php if( $dbError OR $dbSuccess OR $info ): ?>
                        <a class="button" href="./dashboard.php">Okay</a>
                    <?php elseif( $alert ): ?>
                        <a class="button" href="?action=cancelDelete">Cancel</a>
                        <a class="button" href="?action=delete">Delete Post</a>
                    <?php elseif( $dbDeleteError OR $dbDeleteSuccess ): ?>
                        <a class="button" href="?action=okay">Okay</a>
                    <?php endif ?>
                </popupBox> 
            </div>
        <?php endif ?>

        <!-- ------------- USER MESSAGE END ------------------------------------ -->


        <!-- ------------- MAIN CONTENT BEGIN ---------------------------------- -->

        <div class="forms">

            
            <?php if( $showView === true ): ?>

                <!-- ------------- VIEW BLOG POST BEGIN ---------------------------------- -->

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
                <!-- ------------- VIEW BLOG POST END ------------------------------------ -->
                        

            <?php elseif( $showEdit === true ): ?>

                <div class="article-form">  

                    <?php if( $chosenBlog !== NULL ): ?>

                        <!--------------- Edit form loaded for the first time ----------------->

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
                                        <input type="file" name="image" class="image-button">
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
                    
                    <?php else: ?>
                        
                        <!-- Edit form in the case of an input error --> 

                        <!-- ------------- ERROR EDIT FORM BEGIN ------------------------- -->

                        <form action="" class="edit-form" method="POST" enctype="multipart/form-data">

                            <!-- Link to create new post -->
                            <a href="dashboard.php"><< Write a new blog post</a>
                            <br>
                            <div class="form-heading">Edit blog post</div>
                            <br>
                            <input type="hidden" name="editForm">
                            <input type="hidden" name="b12" value="<?= $editedBlogID ?>">
                            <input type="hidden" name="b13" value="<?= $editedImagePath ?>">

                            <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                            <!-- ------------- Category ------------- -->
                            <label for="b8">Choose a category</label>
                            <select name="b8" id="b8" class="form-text">
                                <?php foreach( $categoryArray AS $dbCategory ): ?>
                                    <option value="<?= $dbCategory['catID'] ?>" <?php if($dbCategory['catID'] == $editedCategory) echo 'selected'?>>
                                        <?= $dbCategory['catLabel'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>

                            <br>
                            <!-- ------------- Title ---------------- -->
                            <label for="b9">Write the title of your post</label>
                            <div class="error"><?= $errorTitle ?></div>
                            <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $editedTitle ?>">

                            <br>
                            <!-- ------------- Image Upload ---------- -->
                            <fieldset>
                                <legend>Upload an image</legend>

                                <!-- ------------- Database Image ---------- -->

                                <?php if( $editedImagePath !== NULL ): ?>
                                    <img class="left" src="<?= $editedImagePath ?>" alt="image for the blog article">
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
                                <input type="file" name="image" class="image-button">
                                <br>
                                <br>
                                <!-- ------------- Image Alignment ---------- -->
                                <label for="b10">Choose the alignment of the image</label>
                                <br>
                                <select name="b10" id="b10" class="form-select">
                                    <option value="left" <?php if( $editedAlignment === 'left') echo 'selected' ?>>Left</option>
                                    <option value="right" <?php if( $editedAlignment === 'right') echo 'selected' ?>>Right</option>
                                </select>
                                <br>
                            </fieldset>
                            <br>

                            <!-- ------------- Content ------------------ -->
                            <label for="b11">Write your blog post</label>
                            <div class="error"><?= $errorContent ?></div>
                            <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $editedContent ?></textarea>
                            <br>
                            <input type="submit" class="form-button" value="Publish">
                            </form>
                            <!-- ------------- ERROR EDIT FORM END ---------------------------- -->

                    <?php endif ?>
                </div>

            <?php else: ?>

                <!-- ------------- NEW BLOG POST FORM BEGIN ------------------------- -->

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
                        <input type="file" name="image" class="image-button">
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
                    <textarea name="b4" id="b4" class="textarea" cols="30" rows="25" placeholder="..."><?= $content ?></textarea>
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
                    <div class="error"><?= $errorNewCategory ?></div>
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
                    <?php if(date('Y') > 2024): ?>
                        <li>THE CODING SORCERESS 2024 - <?= date('Y') ?></li>
                    <?php else: ?>
                        <li>THE CODING SORCERESS 2024</li>
                    <?php endif ?>
                </ul>
            </div>
        </footer>
        <!-- ------------- FOOTER END ---------------------------------- -->
    
    </body>
</html>