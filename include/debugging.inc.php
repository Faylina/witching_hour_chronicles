<?php
#********************************************************************************************#


				#********************************************#
				#********** SIMPLE DEBUG OUTPUT *************#
				#********************************************#

				#********** A process has been started: **********#
				function debugProcessStart($process) {
					if(DEBUG) { 
						echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: $process ... <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}
				
				
				#********** One-time occurrence: **********#
				function debugOccurrence($occurrence) {
					if(DEBUG) {
						echo "<p class='debug'><b>Line " . __LINE__ . "</b>: $occurrence . <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}

				#********** Authentification: **********#
				function debugAuth($message) {
					if(DEBUG) {
						echo "<p class='debug auth'><b>Line " . __LINE__ . "</b>:" . $message . "<i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}
			
				
#********************************************************************************************#


				#********************************************#
				#********** SUCCESS-/ERROR-MESSAGES *********#
				#********************************************#

				#********** Debug-Output for error: **********#
				function debugError($errorMessage) {
					if(DEBUG) {	
						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: ERROR: $errorMessage <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}

				#********** Debug-Output for db error: **********#
				function debugErrorDB($error) {
					if(DEBUG) {	
						echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";			
					}
				}	
		
		
				#********** Debug-output for success: **********#
				function debugSuccess($successMessage) {
					if(DEBUG) {	
						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: $successMessage <i>(" . basename(__FILE__) . ")</i></p>\n";	
					}
				}
			
				#********** Debug-output for notification: **********#
				function debugNotification($notification) {
					if(DEBUG) {	
						echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: $notification <i>(" . basename(__FILE__) . ")</i></p>\n";	
					}
				}			
				
		
#********************************************************************************************#
		
				
				#*********************************************#
				#********** OUTPUT VARIABLE VALUES ***********#
				#*********************************************#
								
				#********** SIMPLE DATA TYPES (STRING, INTEGER, FLOAT, BOOLEAN) **********#
				function debugVariable($name, $variable) {
					if(DEBUG_V)	{
						echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$$name: $variable <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}
								
				
				#********** COMPLEX DATA TYPES (ARRAYS, OBJECTS) **********#
				function debugArray($name, $array) {
					if(DEBUG_A) {
						echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$$name <i>(" . basename(__FILE__) . ")</i>:<br>\n";
						print_r($array);
						echo "</pre>";
					}
				}

				function debugObject($name, $object) {
					if(DEBUG_O) {
						echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$$name <i>(" . basename(__FILE__) . ")</i>:<br>\n";
						print_r($object);
						echo "</pre>";
					}
				}


#********************************************************************************************#


				#********************************#
				#********** FUNCTIONS ***********#
				#********************************#
				
				function debugFunction($name, $function) {
					if(DEBUG_F) {		
						echo "<p class='debug $name'>🌀 <b>Line " . __LINE__ . "</b>: Invoking " . $function . "() <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}
				
				
#********************************************************************************************#
				

				#*****************************#
				#********** CLASSES **********#
				#*****************************#
				
				#********** CONSTRUCTOR *********#
				function debugConstructorInvoke($method) {
					if(DEBUG_CC) {	
						echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Invoking $method()  (<i>" . basename(__FILE__) . "</i>)</p>\n";
					}
				}

				function debugConstructorObject($method, $object) {
					if(DEBUG_CC) {
						echo "<pre class='debug class value'><b>Line " . __LINE__ .  "</b> | $method(): <i>(" . basename(__FILE__) . ")</i>:<br>\n";
						print_r($object);
						echo "</pre>";
					}
				}
				
				
				#********** DESTRUCTOR **********#
				function debugDestructor($method) {
					if(DEBUG_CC) {	
						echo "<p class='debug class'>☠️  <b>Line " . __LINE__ .  "</b>: Invoking $method()  (<i>" . basename(__FILE__) . "</i>)</p>\n";			
					}			
				}
										
					
				#********** METHODS **********#
				function debugMethod($method) {
					if(DEBUG_C) {		
						echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Invoking $method() (<i>" . basename(__FILE__) . "</i>)</p>\n";
					}
				}

				function debugMethodMessage($method, $message) {
					if(DEBUG_C) {		
						echo "<p class='debug class'><b>Line " . __LINE__ .  "</b> | $method(): $message... (<i>" . basename(__FILE__) . "</i>)</p>\n";
					}
				}

				function debugMethodError($method, $message) {
					if(DEBUG_C)	{
						echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: $method(): $message <i>(" . basename(__FILE__) . ")</i></p>\n";
					}
				}
				
#********************************************************************************************#
?>