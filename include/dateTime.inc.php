<?php
#**********************************************************************************#


				#***********************************#
				#********** DATE TIME INC **********#
				#***********************************#


#**********************************************************************************#


				/**
				*
				*	Transforms the ISO date- / time-format into a European date- / time-format and 
				*	separates the date from the time (without seconds).
				*
				* 	@param String $value								ISO date/time
				*
				* 	@return Array (String "date", String "time")		EU-date plus time
				*
				*/

				function isoToEuDateTime($value) {

					debugFunction('isoToEuDateTime', __FUNCTION__);		
					
					if($value) {
						
						// possible input-formats
						// 2018-05-17 14:17:48
						// 2018-05-17
						
						// possible output-formats
						// 17.05.2018	// 14:17
						// 17.05.2018
						
						// check whether a time is included in $value
						if( strpos($value, " ") ) {

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$value contains a time. <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

							// separate date and time
							$dateTimeArray = explode(" ", $value);

							/*
if(DEBUG_F)				echo "<pre class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$dateTimeArray: <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)				print_r($dateTimeArray);					
if(DEBUG_F)				echo "</pre>";
							*/
							
							$date = $dateTimeArray[0];

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$date: $date <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

							// disassemble the date into its parts (day, month, year)
							$dateArray 	= explode("-", $date);

							$time = $dateTimeArray[1];
							// remove the seconds
							$time 		= substr($time, 0, 5);

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$time: $time <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

						} else {

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$value does not contain a time. <i>(" . basename(__FILE__) . ")</i></p>\r\n";			

							// disassemble the date into its parts (day, month, year)
							$dateArray 	= explode("-", $value);
							$time 		= NULL;
						}

						/*				
if(DEBUG_F)			echo "<pre class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$dateTimeArray: <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)			print_r($dateArray);					
if(DEBUG_F)			echo "</pre>";
						*/

						// reformat the date					
						$euDate = "$dateArray[2].$dateArray[1].$dateArray[0]";

// if(DEBUG_F)		echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$euDate: $euDate <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
																	
					} else {
						
						// write NULL-values to the array indices 
						$euDate 	= NULL;
						$time 		= NULL;						
					}
					
					// return date and time separately 
					return array("date"=>$euDate, "time"=>$time);					
				}


#**********************************************************************************#


				/**
				*
				*	Transforms the ISO date- / time-format into a US date- / time-format and 
				*	separates the date from the time (without seconds).
				*
				* 	@param String $value								ISO date/time
				*
				* 	@return Array (String "date", String "time")		US-date plus time
				*
				*/

				function isoToUSDateTime($value) {

					debugFunction('isoToUSDateTime', __FUNCTION__);		
					
					if($value) {
						
						// possible input-formats
						// 2018-05-17 14:17:48
						// 2018-05-17
						
						// possible output-formats
						// 17.05.2018	// 14:17
						// 17.05.2018
						
						// check whether a time is included in $value
						if( strpos($value, " ") ) {

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$value contains a time. <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

							// separate date and time
							$dateTimeArray = explode(" ", $value);

							/*
if(DEBUG_F)				echo "<pre class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$dateTimeArray: <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)				print_r($dateTimeArray);					
if(DEBUG_F)				echo "</pre>";
							*/
							
							$date = $dateTimeArray[0];

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$date: $date <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

							// disassemble the date into its parts (day, month, year)
							$dateArray 	= explode("-", $date);

							$time = $dateTimeArray[1];
							// remove the seconds
							$time 		= substr($time, 0, 5);

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$time: $time <i>(" . basename(__FILE__) . ")</i></p>\r\n";					

						} else {

// if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$value does not contain a time. <i>(" . basename(__FILE__) . ")</i></p>\r\n";			

							// disassemble the date into its parts (day, month, year)
							$dateArray 	= explode("-", $value);
							$time 		= NULL;
						}

						/*				
if(DEBUG_F)			echo "<pre class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$dateTimeArray: <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)			print_r($dateArray);					
if(DEBUG_F)			echo "</pre>";
						*/

						// reformat the date
						$usDate = "$dateArray[1]/$dateArray[2]/$dateArray[0]";					

// if(DEBUG_F)		echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$euDate: $euDate <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
																	
					} else {
						
						// write NULL-values to the array indices 
						$usDate 	= NULL;
						$time 		= NULL;						
					}
					
					// return date and time separately 
					return array("date"=>$usDate, "time"=>$time);					
				}


#**********************************************************************************#


				/**
				*
				*	Transforms a EU/US/ISO-date-format into an ISO-date-format
				*
				* 	@param String 						EU/US/ISO-date
				*
				* 	@return String 						ISO-date
				*
				*/

				function toIsoDate($value) {
					debugFunction('toIsoDate', __FUNCTION__);
					
					if( $value ) {
						// possible input-formats
						// 17.05.2018 | 05/17/2018 | 2018-05-17
						
						// possible output-formats
						// 2018-05-17
						
						// check received date-format
						if( stripos($value, ".") ) {
//if(DEBUG_F)				echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): The received date is in a EU-format. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							
							$dateArray 	= explode(".", $value);					
							$day 		= $dateArray[0];
							$month 		= $dateArray[1];
							$year 		= $dateArray[2];
							
						} elseif( stripos($value, "/") ) {
//if(DEBUG_F)				echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): The received date is in a US-format. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							
							$dateArray 	= explode("/", $value);					
							$day 		= $dateArray[1];
							$month 		= $dateArray[0];
							$year 		= $dateArray[2];
							
						} elseif( stripos($value, "-") ) {
//if(DEBUG_F)				echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): The received date is in a ISO-format. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							
							$dateArray 	= explode("-", $value);
							$day 		= $dateArray[2];
							$month 		= $dateArray[1];
							$year 		= $dateArray[0];						
						}
						
						$isoDate = "$year-$month-$day";
//if(DEBUG_F)			echo "<p class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): \$isoDate: $isoDate <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						
						return $isoDate;	
						
					} else {
						return NULL;
					}

				}


#**********************************************************************************#


				/**
				*
				*	Validates a received ISO/US/EU-date
				*
				*	@param String 	$value		- ISO/US/EU-date to be checked
				*
				*	@return Boolean 			- false for wrong format or invalid date; otherwise true
				*
				*/

				function validateDate($value) {
					debugFunction('validateDate', __FUNCTION__);					
					
					$day 	= NULL;
					$month 	= NULL;
					$year 	= NULL;
										
					if( $value ) {
						
						// separate date for checkdate()
					
						// ISO-format
						if( stripos($value, "-") ) {
							$dateArray = explode("-", $value);
							
							$day 	= $dateArray[2];
							$month 	= $dateArray[1];
							$year 	= $dateArray[0];
						
						// EU-format
						} elseif( stripos($value, ".") ) {
							$dateArray = explode(".", $value);
							
							$day 	= $dateArray[0];
							$month 	= $dateArray[1];
							$year 	= $dateArray[2];
						
						// US-format
						} elseif( stripos($value, "/") ) {
							$dateArray = explode("/", $value);
							
							$day 	= $dateArray[1];
							$month 	= $dateArray[0];
							$year 	= $dateArray[2];
						}
/*
if(DEBUG_F)			echo "<pre class='debug dateTime'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)			print_r($dateArray);					
if(DEBUG_F)			echo "</pre>";		
*/				
					}
									
					/*
						Check date components for completeness and 
						validate for a valid gregorian date
					*/
					if( ($day === NULL OR $month === NULL OR $year === NULL) OR checkdate($month, $day, $year) === false ) {
						// error
						return false;
						
					} else {
						// success
						return true;
					}
					
				}


#**********************************************************************************#