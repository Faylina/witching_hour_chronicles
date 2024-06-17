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

                #********** START/CONTINUE SESSION **********#
				if( session_start() === false ) {
					// error
					debugError('Error starting the session.');				
									
				} else {
					// success
					debugSuccess('The session has been started successfully.');	

                    #****************************************#
                    #******** CHECK FOR VALID LOGIN *********#
                    #****************************************#

                    if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
                        // error
						debugAuth('User is not logged in.');

                        #************ DENY PAGE ACCESS ***********#

                        session_destroy();

                        #************ FLAG AS LOGGED OUT *********#

                        $loggedIn = false;

                    } else {
                        // success
                        debugAuth('User is logged in.');

                        #************ GENERATE NEW SESSION ID ***********#
                        session_regenerate_id(true);

                        #************ FLAG AS LOGGED IN *****************#

                        $loggedIn = true;

                    } // CHECK FOR VALID LOGIN END

                } // VALIDATE LOGIN END

#*************************************************************************#

				
				#****************************************#
				#********** PROCESS LOGIN FORM **********#
				#****************************************#

                #********** PREVIEW POST ARRAY **********#

                debugArray('_POST', $_POST);

                #************ FORM PROCESSING ***********#

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['loginForm']) === true ) {
                    debugProcessStart('The form "loginForm" has been sent.');
										
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');

                    $email      = sanitizeString($_POST['b1']);
                    $password   = sanitizeString($_POST['b2']);

                    debugVariable('email', $email);
                    debugVariable('password', $password);


                    // Step 3 FORM: Field validation
					debugProcessStart('Validating fields...');

                    $errorEmail     = validateEmail($email);
                    $errorPassword  = validateInputString($password, minLength:4);


                    #********** FINAL FORM VALIDATION **********#	

                    if( $errorEmail !== NULL OR $errorPassword !== NULL ) {
                        // error
                        debugError('The login form contains errors!');	

                        // neutral user message
                        $errorLogin = 'Invalid email or password.';

                    } else {
                        //success
                        debugSuccess('The form is formally free of errors.');


                        // Step 4 FORM: data processing

                        debugProcessStart('The form data is being further processed...');

                        #****************************************#
				        #************ DB OPERATIONS *************#
				        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect();

                        #************ FETCH DATA FROM DB *************#
                        debugProcessStart('Fetching data from database...');

                        // Step 2 DB: Create the SQL-Statement and a placeholder-array

                        $sql = 'SELECT userID, userFirstName, userLastName, userPassword 
                                FROM users 
                                WHERE userEmail = :userEmail';

                        $placeholders = array('userEmail' => $email);

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
                        $dbUserArray = $PDOStatement -> fetch(PDO::FETCH_ASSOC);

                        // close DB connection
                        dbClose($PDO, $PDOStatement);

                        debugArray('dbUserArray', $dbUserArray);

                        #************ 1. VALIDATE EMAIL ADDRESS *************#

                        debugProcessStart('Validating the email address...');

                        if( $dbUserArray === false ) {
                            // error
                            debugError('The email could not be found in the database!');

                            // neutral user message
                            $errorLogin = 'Invalid email or password.';

                        } else {
                            // success
                            debugSuccess('The email has been found in the database.');

                            #************ 2. VALIDATE PASSWORD *************#

                            debugProcessStart('Validating password...');

                            if( password_verify($password, $dbUserArray['userPassword']) === false ) {
                                // error
                                debugError('The password in the form does not match the password in the database!');

                                // neutral user message
                                $errorLogin = 'Invalid email or password.';

                            } else {
                                // success
                                debugSuccess('The password in the form matches the password in the database.');

                                #************ 3. PROCESS LOGIN *************#

                                debugProcessStart('The user is being logged in...');

                                #************ START SESSION ***************#

                                if( session_start() === false ) {
                                    // error
                                    debugError('Error starting session!');	

                                    $errorLogin = 'Login is not possible. Please allow cookies in your browser.';

                                    // error message for admin
									$logErrorForAdmin = 'Error during login process.';
					
									#******** WRITE TO ERROR LOG ******#
							
									// create file
							
									if( file_exists('./logdocs') === false ) {
										mkdir('./logdocs');
									}
							
									// create error message
							
									$logEntry    = "\t<p>";
									$logEntry   .= date('Y-m-d | h:i:s |');
									$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
									$logEntry   .= '<i>' . $logErrorForAdmin . '</i>';
									$logEntry   .= "</p>\n";
							
									// put error message into the error log
							
									file_put_contents('./logdocs/error_log.html', $logEntry, FILE_APPEND);

                                } else {
                                    // success
                                    debugSuccess('The session has been started successfully.');

                                    #******** SAVE USER DATA INTO SESSION FILE ******#

                                    debugProcessStart('Writing user data to session...');

                                    $_SESSION['ID']         = $dbUserArray['userID'];
                                    $_SESSION['firstName']  = $dbUserArray['userFirstName'];
                                    $_SESSION['lastName']   = $dbUserArray['userLastName'];
                                    $_SESSION['IPAddress']  = $_SERVER['REMOTE_ADDR'];

                                    #******** REDIRECT TO DASHBOARD ******#

                                    header('LOCATION: dashboard.php');

                                } // 3. PROCESS LOGIN END

                            } // 2. VALIDATE PASSWORD END

                        } // 1. VALIDATE EMAIL ADDRESS END

                    } // FIELD VALIDATION END

                } // FORM PROCESSING END

#*************************************************************************#

				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#

                #****************************************#
				#************* DB OPERATIONS ************#
				#****************************************#

                debugProcessStart('Fetching categories...');

                // Step 1 DB: Connect to database

                $PDO = dbConnect();

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
                    debugErrorDB($error);										
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection
                $categoryArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection
                dbClose($PDO, $PDOStatement);

                debugArray('categoryArray', $categoryArray);

#*************************************************************************#

                #***********************************************#
				#******** PROCESS URL PARAMETERS ***************#
				#***********************************************#

                #******** PREVIEW URL PARAMETERS ***************#

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
                    
                        // 1. Delete session file
                        session_destroy();
                    
                        // 2. Reload homepage
                        header('LOCATION: index.php');

                        // 3. Fallback in case of an error: end processing of the script
                        exit();

                    #*************** FILTER BY CATEGORY **************#
                    } elseif( $action === 'filterByCategory') {

                        debugProcessStart("The blog posts are being filtered by category...");	

                        #********* PROCESS CAT ID PARAMETER ********#

                        if( isset($_GET['catID']) === true ) {
                            debugProcessStart("URL-parameter 'catID' has been committed.");		

                            // Read, sanitize and output URL data
                            debugProcessStart('The URL parameters are being read and sanitized...');        
                                        
                            $filterID = sanitizeString($_GET['catID']);

                            debugVariable('filterID', $filterID);
                    
                        } // PROCESS CAT ID PARAMETER END

                    } // BRANCHING END 

                } // PROCESS URL PARAMETERS END 


#*************************************************************************#

                #*************************************************#
				#********** FETCH DATA FOR BLOG FROM DB **********#
				#*************************************************#

                #*************************************************#
				#***************** DB OPERATIONS *****************#
				#*************************************************#
                debugProcessStart("Fetching blog posts from database...");	

                // Step 1 DB: Connect to database

                $PDO = dbConnect();

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
                    debugErrorDB($error);									
                }

                // Step 4 DB: evaluate the DB-operation and close the DB connection
                $blogArray = $PDOStatement -> fetchAll(PDO::FETCH_ASSOC);

                // close DB connection
                dbClose($PDO, $PDOStatement);

                debugArray('blogArray', $blogArray);

#*************************************************************************#
?>

<!DOCTYPE html>
<html lang="en">

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

        <!-- ------------- LINK TO THE CODING SORCERESS BEGIN ------------------------- -->
         
         <div class="coding-sorceress">
            <a href="../../portfolio.php#projects"><< Go back to The Coding Sorceress</a>
         </div>

        <!-- ------------- LINK TO THE CODING SORCERESS END --------------------------- -->


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


        <!-- ------------- MAIN CONTENT BEGIN -------------------------- -->

        <div class="content">

            <!-- ------------- BLOG BEGIN ---------------------------------- -->

            <div class="blog">

                <?php if( empty($blogArray) === true ): ?>
					<p>No blog posts have been written yet. Get creative!</p>
				
				<?php else: ?>

                    <!-- -------- Generate blog articles ---------- -->
                    <?php foreach( $blogArray AS $value): ?>

                        <!-- Convert ISO time from DB to EU time and split into date and time -->
                        <?php $dateArray = isoToUSDateTime( $value['blogDate'] ) ?>

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

                    <?php endforeach ?>
                <?php endif ?>
            </div>
            <!-- ------------- BLOG END ------------------------------------ -->


            <!-- ------------- CATEGORIES BEGIN ---------------------------- -->

            <div class="categories">
                <div class="blog-title">Categories</div>
                <?php if( empty($categoryArray) === true ): ?>
					<p>There are no categories yet. Go ahead and create some. :&#41;</p>
			
				<?php else: ?>
                    <?php foreach( $categoryArray AS $value ): ?>
                        <a href="?action=filterByCategory&catID=<?= $value['catID'] ?>"
                        <?php if( $value['catID'] == $filterID ) echo 'class="active"' ?>>
                            <?= $value['catLabel'] ?></a>
                    <?php endforeach ?>
                <?php endif ?>
            </div>

            <!-- ------------- CATEGORIES END ------------------------------ -->

        </div>
        <!-- ------------- MAIN CONTENT END -------------------------- -->


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
                <div><strong>Disclaimer:</strong> All images, apart from the logo and background, were generated by AI.</div>
            </div>
        </footer>
        <!-- ------------- FOOTER END ---------------------------------- -->
        
    </body>
</html>