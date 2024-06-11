<?php
#******************************************************************************************************#


				#**********************************#
				#********** DATABASE INC **********#
				#**********************************#


#******************************************************************************************************#


				/**
				*
				*	Connects to a database via PDO
				*	The configuration and login details are situated in an external configuration file
				*
				*	@param [String $dbname=DB_NAME]				name of the database to be connected
				*
				*	@return Object								database connection object
				*
				*/

				function dbConnect($DBName=DB_NAME) {
				
if(DEBUG_DB)	echo "<p class='debug db'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): Attempt a connection with the database '<b>$DBName</b>'... <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

					// EXCEPTION-HANDLING
					// trying to establish a database connection
					try {
						
						$PDO = new PDO(DB_SYSTEM . ":host=" . DB_HOST . "; dbname=$DBName; charset=utf8mb4", DB_USER, DB_PWD);
						
						$PDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
						$PDO->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
								
					} catch(PDOException $error) {
					
if(DEBUG_DB)		echo "<p class='debug db err'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): <i>ERROR: " . $error->GetMessage() . " </i> <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						
						exit;
					}
					
if(DEBUG_DB)	echo "<p class='debug db ok'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): Successful connection with the database '<b>$DBName</b>'. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						
					return $PDO;
				}
				
				
#******************************************************************************************************#

				
				/**
				*
				*	Closes an active DB connection and sends a debug message
				*
				*	@param	PDO	&$PDO					Reference of given argument PDO object
				*	@param	PDO	&$PDOStatement			Reference of given argument PDOStatement object
				*
				*	return void
				*/
				
				function dbClose(&$PDO, &$PDOStatement=NULL) {					
if(DEBUG_DB)	echo "<p class='debug db'>🌀 <b>Line  " . __LINE__ .  "</b>: Invoking " . __FUNCTION__ . "() <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					$PDO = $PDOStatement = NULL;
				}

#******************************************************************************************************#