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

                $loggedIn   = false;
                $errorLogin = NULL; 
                $filterID   = NULL;

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

                    session_destroy();

                } else {
                    // success
if(DEBUG)	        echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Valid login. <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $loggedIn = true;
                }

#*************************************************************************#

				
				#****************************************#
				#********** PROCESS LOGIN FORM **********#
				#****************************************#

                #********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($_POST);					
if(DEBUG_A)	    echo "</pre>";
*/

                #************ FORM PROCESSING ***********#

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['loginForm']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The form 'loginForm' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";	


                    // Step 2 FORM: Read, sanitize and output form data

if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Reading and sanitizing form data... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $email      = sanitizeString($_POST['b1']);
                    $password   = sanitizeString($_POST['b2']);

if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$email: $email <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)	        echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$password: $password <i>(" . basename(__FILE__) . ")</i></p>\n";


                    // Step 3 FORM: Field validation

if(DEBUG)	        echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating fields... <i>(" . basename(__FILE__) . ")</i></p>\n";

                    $errorEmail     = validateEmail($email);
                    $errorPassword  = validateInputString($password, minLength:4);


                    // Final form validation

                    if( $errorEmail !== NULL OR $errorPassword !== NULL ) {
                        // error
if(DEBUG)	            echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The form contains errors! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                        $errorLogin = 'Invalid email or password.';

                    } else {
                        //success
if(DEBUG)	            echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The form is formally free of errors. <i>(" . basename(__FILE__) . ")</i></p>\n";


                        // Step 4 FORM: data processing
if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The form data is being further processed... <i>(" . basename(__FILE__) . ")</i></p>\n";


                        #****************************************#
				        #************ DB OPERATIONS *************#
				        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect('blogprojekt');

                        #************ FETCH DATA FROM DB *************#
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                        // Step 2 DB: Create the SQL-Statement and a placeholder-array

                        $sql = 'SELECT userID, userPassword FROM users WHERE userEmail = :userEmail';

                        $placeholders = array('userEmail' => $email);

                        // Step 3 DB: Prepared Statements

                        try {
                            // Prepare: prepare the SQL-Statement
                            $PDOStatement = $PDO -> prepare($sql);
                            
                            // Execute: execute the SQL-Statement and include the placeholder
                            $PDOStatement -> execute($placeholders);
                            // showQuery($PDOStatement);
                            
                        } catch(PDOException $error) {
if(DEBUG) 		            echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
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

                        #************ 1. VALIDATE EMAIL ADDRESS *************#
if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating the email address... <i>(" . basename(__FILE__) . ")</i></p>\n";

                        if( $dbUserArray === false ) {
                            // error
if(DEBUG)	                echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The email address was not found in the database! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                            $errorLogin = 'Invalid email or password.';

                        } else {
                            // success
if(DEBUG)	                echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The email address has been found in the database. <i>(" . basename(__FILE__) . ")</i></p>\n";

                            #************ 2. VALIDATE PASSWORD *************#
if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Validating password... <i>(" . basename(__FILE__) . ")</i></p>\n";

                            if( password_verify($password, $dbUserArray['userPassword']) === false ) {
                                // error
if(DEBUG)	                    echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: The password in the form does not match the password in the database! <i>(" . basename(__FILE__) . ")</i></p>\n";

                                $errorLogin = 'Invalid email or password.';

                            } else {
                                // success
if(DEBUG)	                    echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The password in the form matches the password in the database. <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                #************ 3. PROCESS LOGIN *************#

if(DEBUG)	                    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The user is being logged in... <i>(" . basename(__FILE__) . ")</i></p>\n";

                                #************ PREPARE SESSION *************# 

                                session_name('wwwwitchinghourchroniclescom');

                                #************ START SESSION ***************#

                                if( session_start() === false ) {
                                    // error
if(DEBUG)	                        echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Error starting session! <i>(" . basename(__FILE__) . ")</i></p>\n";	

                                    $errorLogin = 'Login is not possible. Please allow cookies in your browser.';

                                } else {
                                    // success
if(DEBUG)	                        echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: The session has been started successfully. <i>(" . basename(__FILE__) . ")</i></p>\n";

                                    #******** SAVE USER DATA INTO SESSION FILE ******#

                                    $_SESSION['ID']         = $dbUserArray['userID'];
                                    $_SESSION['IPAddress']  = $_SERVER['REMOTE_ADDR'];

                                    #******** REDIRECT TO DASHBOARD ******#

                                    header('LOCATION: dashboard.php');

                                } // 3. PROCESS LOGIN END

                            } // 2. VALIDATE PASSWORD END

                        } // 1. VALIDATE EMAIL ADDRESS END

                    } // FIELD VALIDATION END

                } // FORM PROCESSING END

#*************************************************************************#

				#*******************************************************#
				#********** FETCH DATA FOR CATEGORIES FROM DB **********#
				#*******************************************************#

                #****************************************#
				#************* DB OPERATION *************#
				#****************************************#

                //// Step 1 DB: Connect to database

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

                #***********************************************#
				#******** PROCESS URL PARAMETERS ***************#
				#***********************************************#

                #******** PREVIEW URL PARAMETERS ***************#
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

                    #*************** FILTER **************#
                    } elseif( $action === 'filterByCategory') {

if(DEBUG)	            echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The blog articles are being filtered by category... <i>(" . basename(__FILE__) . ")</i></p>\n";

                        #********* PROCESS CATID PARAMETER ********#

                        // Check whether the parameter has been sent

                        if( isset($_GET['catID']) === true ) {
if(DEBUG)		            echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: The URL-parameter 'catID' has been sent. <i>(" . basename(__FILE__) . ")</i></p>\n";				
                            // Read, sanitize and output URL data
                                        
if(DEBUG)	                echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: The URL parameters are being read and sanitized... <i>(" . basename(__FILE__) . ")</i></p>\n";
                                        
                            $filterID = sanitizeString($_GET['catID']);
                                        
if(DEBUG_V)	                echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$filterID: $filterID <i>(" . basename(__FILE__) . ")</i></p>\n";
                    
                        } // PROCESS CATID PARAMETER END

                    } // BRANCHING END 

                } // PROCESS URL PARAMETERS END 


#*************************************************************************#

                #*************************************************#
				#********** FETCH DATA FOR BLOG FROM DB **********#
				#*************************************************#

                #*************************************************#
				#***************** DB OPERATION ******************#
				#*************************************************#

                // Step 1 DB: Connect to database

                $PDO = dbConnect('blogprojekt');

                #************ FETCH DATA FROM DB *************#

if(DEBUG)	    echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching blog data from database... <i>(" . basename(__FILE__) . ")</i></p>\n";

                // Step 2 DB: Create the SQL-Statement and a placeholder-array

                if( $filterID === NULL ) {
                    //not filtered - show all blog articles
                    $sql = 'SELECT userFirstName, userLastName, userCity, blogHeadline, blogImagePath, blogImageAlignment, blogContent, blogDate, catLabel
                            FROM blogs 
                            INNER JOIN users USING(userID)
                            INNER JOIN categories USING(catID)
                            ORDER BY blogDate DESC';

                    $placeholders = array();

                } else {
                    // filtered - show only selected blog articles

                    $sql = 'SELECT userFirstName, userLastName, userCity, blogHeadline, blogImagePath, blogImageAlignment, blogContent, blogDate, catLabel
                                    FROM blogs 
                                    INNER JOIN users USING(userID)
                                    INNER JOIN categories USING(catID)
                                    WHERE catID = :catID
                                    ORDER BY blogDate DESC';

                    $placeholders = array('catID' => $filterID );
                }

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
if(DEBUG_A)	    echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_A)	    print_r($blogArray);				
if(DEBUG_A)	    echo "</pre>";
*/

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
                <a class="link" href="./dashboard.php"><< Dashboard</a>
                <a class="link" href="?action=logout">Logout >></a>

            <!-- ------------- NAV LINKS END ------------------------------ -->
            <?php endif ?>

        </nav>
    <!-- ------------- NAVIGATION END ----------------------------- -->


    <!-- ------------- HEADER BEGIN ------------------------------- -->
        <header>

            <img class="logo" src="./css/images/logo.png" alt="Parchment paper with a teal quill, a full moon in the background">
            <div class="title">
                <h1>Witching Hour Chronicles</h1>
                <a href="index.php">Show all blog articles</a>   
            </div>

        </header>
    <!-- ------------- HEADER END ---------------------------------- -->

        <div class="content">

        <!-- ------------- BLOG BEGIN ---------------------------------- -->
            <div class="blog">
                <?php foreach( $blogArray AS $value): ?>
                    <?php $dateArray = isoToEuDateTime( $value['blogDate'] ) ?>
                    <div class="blog-category">Category: <?= $value['catLabel'] ?></div>
                    <div class="blog-title"><?= $value['blogHeadline'] ?></div>
                    <div class="blog-meta">
                        <?= $value['userFirstName'] ?> <?= $value['userLastName'] ?> (<?= $value['userCity'] ?>) 
                        wrote on <?= $dateArray['date'] ?> at <?= $dateArray['time'] ?> o'clock:
                    </div>
                    <div class="container clearfix">
                        <?php if( $value['blogImagePath'] !== NULL ): ?>
                            <img class="<?= $value['blogImageAlignment']?>" src="<?= $value['blogImagePath']?>" alt="">
                        <?php endif ?>
                        <div class="blog-content"><?php echo nl2br( $value['blogContent'] ) ?></div>
                    </div>
                    <br>
                    <hr>
                    <br>
                <?php endforeach ?>
            </div>
        <!-- ------------- BLOG END ------------------------------------ -->

        <!-- ------------- CATEGORIES BEGIN ---------------------------- -->
            <div class="categories">
                <?php foreach( $categoryArray AS $value ): ?>
                    <a href="?action=filterByCategory&catID=<?= $value['catID'] ?>"><?= $value['catLabel'] ?></a>
                <?php endforeach ?>
            </div>
        <!-- ------------- CATEGORIES END ------------------------------ -->

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