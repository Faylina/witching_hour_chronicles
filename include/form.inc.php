<?php
#**********************************************************************************#

				
				#*************************************#
				#********** SANITIZE STRING **********#
				#*************************************#
				
				/**
				*
				*	Replaces potentially harmful control characters with HTML-entities 
				*	Removes whitespaces before and after a string
				*	Replaces empty string and strings containing only whitespaces with NULL
				*
				*	@params		String	$value			string to be sanitized
				*
				*	@return		String|NULL				sanitized string | NULL for $value = NULL or '' or whitespaces exclusively
				*
				*/

				function sanitizeString($value) {
					debugFunction('sanitizeString', __FUNCTION__);
					
					/*
						Since in PHP no call to PHP's own functions with NULL values will be allowed in the future, we only call the PHP functions if $value is NOT NULL. For DB operations, NULL should not be overwritten with an empty string. Therefore, at this point, an empty string is replaced by NULL.
					*/
					if( $value !== NULL ) {
						
						/*

							PROTECTION AGAINST INJECTION OF UNAUTHORIZED CODES: To prevent such incidents: <script>alert("HACK!")</script> the received string MUST be escaped! The htmlspecialchars() function converts potentially dangerous characters like < > " & into HTML code (&lt; &gt; &quot; &amp;).

							The parameter ENT_QUOTES additionally converts simple ' into &apos;. The parameter ENT_HTML5 ensures that the generated HTML code is HTML5-compliant. 
							
							The 1st optional parameter controls the underlying character encoding (NULL = character encoding is taken over by the web server). 
							
							The 2nd optional parameter determines the character encoding. 
							
							The 3rd optional parameter controls whether existing HTML entities are escaped again (false = no double escaping).

						*/
						$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, double_encode:false);
						
						$value = trim($value);
					}
					
					/*
						A blank string passed from the start should be converted to NULL at this point to avoid issues with empty database fields (NULL) that would otherwise be overwritten by blank strings. 
						
						If $value contains only whitespaces, trim() will return a blank string at this point. This blank string must be converted back to NULL.
					*/
					if( $value === '' ) {
						$value = NULL;
					}
					
					return $value;
			
				}


#**********************************************************************************#

				
				#*******************************************#
				#********** VALIDATE INPUT STRING **********#
				#*******************************************#
				
				/**
				*
				*	Checks a string for a minimum and maximum length  and optionally whether it is mandatory.
				*	Returns an error message for an empty string, NULL or invalid length. 
				*
				*	@param	NULL|String	$value								string to be validated
				*	@param	Boolean		$mandatory=INPUT_MANDATORY			signals whether actual input is supposed to be mandatory
				*	@param	Integer		$maxLength=INPUT_MAX_LENGTH			maximum length to check against
				*	@param	Integer		$minLength=INPUT_MIN_LENGTH			minimum length to check against															
				*
				*	@return	String|NULL										error message | otherwise NULL
				*
				*/

				function validateInputString($value, $mandatory=INPUT_MANDATORY, $maxLength=INPUT_MAX_LENGTH, $minLength=INPUT_MIN_LENGTH) {
					
if(DEBUG_F)		echo "<p class='debug validateInputString'>🌀 <b>Line " . __LINE__ . "</b>: Invoking " . __FUNCTION__ . "('$value' [$minLength | $maxLength] mandatory:$mandatory) <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** MANDATORY CHECK **********#
					/*
						Since a string to be checked may not necessarily come from a form but also from a JSON object, NULL values need to be checked here as well.
					*/
					if( $mandatory === true AND ($value === '' OR $value === NULL) ) {
						// error
						return 'This field is required!'; 
					
					
					#********** MAXIMUM LENGTH CHECK **********#
					/*
						Since database fields often have a length limit, but the database does not give an error message when this limit is exceeded, it silently cuts off anything that goes beyond this limit. Therefore, a check for this maximum length must be performed beforehand. This is the only way to provide the user with an appropriate error message.
					*/
					/*
						mb_strlen() expects a string as data type. However, if a different data type such as integer or float is passed (later during OOP), mb_strlen() throws an error. It makes no sense to check the length of a number value. Therefore, this check is only performed for the 'String' data type.
					*/
					/*
						Since passing NULL to PHP-native functions will no longer be allowed in future PHP versions, it must be ensured before every call to a PHP function that the value to be passed is not NULL.
					*/
					} elseif( $value !== NULL AND mb_strlen($value) > $maxLength ) {
						// error
						return "May not be longer than $maxLength characters!";
						
						
					#********** MINIMUM LENGTH CHECK **********#
					/*
						In special cases, there are minimum length requirements for user input, such as when creating passwords. To allow non-required fields to remain empty, the minimum length must be pre-set as a default value of 0. For an optional field value that also must meet a minimum length requirement, the validation should not validate blank strings, as they never meet the minimum length and therefore the value would no longer be optional.
					*/
					/*
						mb_strlen() expects a string as data type. However, if a different data type such as integer or float is passed (later during OOP), mb_strlen() throws an error. It makes no sense to check the length of a number value. Therefore, this check is only performed for the 'String' data type.
					*/
					/*
						Since passing NULL to PHP-native functions will no longer be allowed in future PHP versions, it must be ensured before every call to a PHP function that the value to be passed is not NULL.
					*/
					} elseif( $value !== NULL AND mb_strlen($value) < $minLength ) {
						// error
						return "Must be at least $minLength characters long!";
					}
					
					return NULL;
					
				}	
		

#**********************************************************************************#

				
				#*******************************************#
				#********** VALIDATE EMAIL FORMAT **********#
				#*******************************************#
				
				/**
				*	
				*	Checks whether a string is a valid email address and not an empty string (if the field is mandatory)
				*	and returns an error message if not.
				*
				*	@param	String	$value						string to be validated
				*	@param	Bool	$mandatory=true				signals whether actual input is supposed to be mandatory
				*
				*	@return	String|NULL							error message | otherwise NULL
				*
				*/

				function validateEmail($value, $mandatory=INPUT_MANDATORY) {
					
if(DEBUG_F)		echo "<p class='debug validateEmail'>🌀 <b>Line " . __LINE__ . "</b>: Invoking " . __FUNCTION__ . "('$value' | mandatory:$mandatory) <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** MANDATORY CHECK **********#
					/*
						Since a string to be checked may not necessarily come from a form but also from a JSON object, NULL values need to be checked here as well.
					*/
					if( $mandatory === true AND ($value === '' OR $value === NULL) ) {
						// error
						return 'This field is required!'; 					
					
					
					#********** VALIDATE EMAIL ADDRESS FORMAT **********#
					/*
						Since passing NULL to PHP-native functions will no longer be allowed in future PHP versions, it must be ensured before every call to a PHP function that the value to be passed is not NULL.
					*/
					} elseif( $value !== NULL AND $value !== '' AND filter_var($value, FILTER_VALIDATE_EMAIL) === false ) {
						// error
						return 'This is not a valid email address!'; 
					}
					
					return NULL;
					
				}	


#**********************************************************************************#

				
				#*******************************************#
				#********** VALIDATE IMAGE UPLOAD **********#
				#*******************************************#
				
				/**
				*
				*	Validates an image that was uploaded to the server regarding the correct MIME-type, image-type,
				*	image size in pixels, file size in Bytes and a plausible header. 
				*	Generates a unique file name and a secure file suffix and moves the image to the intended directory.
				*	
				*
				*	@param	String	$fileTemp													the temporary path to the image inside the quarantine directory
				*	@param	Integer	$imageMaxHeight				=IMAGE_MAX_HEIGHT				maximum image height in pixels
				*	@param	Integer	$imageMaxWidth				=IMAGE_MAX_WIDTH				maximum image width in pixels				
				*	@param	Integer	$imageMinSize				=IMAGE_MIN_SIZE					minimum file size in bytes
				*	@param	Integer	$imageMaxSize				=IMAGE_MAX_SIZE					maximum file size in bytes
				*	@param	Array	$imageAllowedMimeTypes		=IMAGE_ALLOWED_MIME_TYPES		whitelist of trusted MIME-types with their respective suffixes
				*	@param	String	$imageUploadPath			=IMAGE_UPLOAD_PATH				upload path to the intended directory
				*
				*	@return	Array	{'imagePath'	=>	String|NULL, 							when successful shows the upload path to the intended directory | otherwise NULL
				*					'imageError'	=>	String|NULL}							when successful NULL | otherwise error message
				*
				*/

				function validateImageUpload( 	$fileTemp,
												$imageMaxHeight 		= IMAGE_MAX_HEIGHT,
												$imageMaxWidth 			= IMAGE_MAX_WIDTH,
												$imageMinSize 			= IMAGE_MIN_SIZE,
												$imageMaxSize 			= IMAGE_MAX_SIZE,
												$imageAllowedMimeTypes 	= IMAGE_ALLOWED_MIME_TYPES,
												$imageUploadPath		= IMAGE_UPLOAD_PATH ) 
					{
						
					
if(DEBUG_F)		echo "<p class='debug validateImageUpload'>🌀 <b>Line " . __LINE__ . "</b>: Invoking " . __FUNCTION__ . "('$fileTemp') <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#**************************************************************************#
					#********** 1. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER **********#
					#**************************************************************************#
					
					/*
						FILE HEADER
						
						The information that is always present in every image header or file header of an image may vary depending on the specific image format. However, there are some basic information that is present in most common image formats and are considered mandatory. Some of the typical mandatory information include:

						- File signature: Every image format has a unique file signature that is located at the beginning of the file and indicates the format. The file signature is crucial for identifying the file format.

						- File size: The size of the image file in bytes or kilobytes is included in most file headers. This is important for storage management and file reading.

						- Image dimensions: Information about the width and height of the image in pixels is critical to ensure the proper display of the image. This data is almost always present in the file header.

						- Color depth: The color depth specifies how many colors per pixel can be displayed in the image. In RGB images, the usual color depth is 24 bits (8 bits per channel), which corresponds to 16.7 million colors. This is a fundamental information in the header.
											  
						  This data is present in most commonly used image formats and are considered mandatory information in the file header.
					*/
					/*
						The function getimagesize() reads the file header of an image file and returns a mixed array with valid MIME type ('image/...'):
						
						[0] 				Image width in pixels (image dimensions) 
						[1] 				Image height in pixels (image dimensions)
						[3] 				A string prepared for the HTML <img> tag (width="480" height="532")
						['bits']			Number of bits per channel (color depth) 
						['channels']		Number of color channels (thus also the color model: RGB=3, CMYK=4) 
						['mime'] 			MIME Type
						
						If the MIME Type is invalid (not 'image/...'), getimagesize() returns false.
					*/

					$imageDataArray = getimagesize($fileTemp);

if(DEBUG_F)		echo "<pre class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageDataArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_F)		print_r($imageDataArray);					
if(DEBUG_F)		echo "</pre>";				
					
					
					#********** CHECK FOR VALID MIME TYPE **********#
					if( $imageDataArray === false ) {
						// error (MIME TYPE IS NO VALID IMAGE TYPE)
						return array('imagePath' => NULL, 'imageError' => 'This is not an image!');
					
					} elseif( is_array($imageDataArray) === true ) {
						// success (MIME TYPE IS A VALID IMAGE TYPE)
						
						/*
							SPECIAL CASE NUMBER (NUMERIC STRINGS): 
							Since we always receive all values from forms and other user inputs as a string data type, checking for a specific numeric data type in PHP rarely makes sense. 
							
							Instead of directly checking the Integer data type using is_int(), it's better to check the received string for its content format: Is the string numeric and does its value correspond to an Integer?

							The function filter_var() can also check a string for the content 'Integer' or 'Float' using a regular expression controlled by a constant.

							If the value checked by filter_var() matches the data format to be checked, filter_var automatically performs a type conversion and returns the converted value.
						*/
						$imageWidth 	= filter_var($imageDataArray[0], FILTER_VALIDATE_INT);
						$imageHeight 	= filter_var($imageDataArray[1], FILTER_VALIDATE_INT);
						$imageMimeType 	= sanitizeString($imageDataArray['mime']);
						$fileSize		= fileSize($fileTemp);
if(DEBUG_F)			echo "<p class='debug validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageWidth: $imageWidth px<i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageHeight: $imageHeight px<i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageMimeType: $imageMimeType <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileSize: " . round($fileSize/1024, 1) . "kB <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					} // 1. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER END
					#*******************************************************************#
					
					
					#*****************************************#
					#********** 2. IMAGE VALIDATION **********#
					#*****************************************#
					
					#********** VALIDATE PLAUSIBILITY OF FILE HEADER **********#
					/*
						This check relies on the assumption that a manipulated file header was not consistently falsified:
						A hacker changes the MimeType of a text file with malicious code to 'image/jpg', but forgets to add additional entries like 'imageWidth' or 'imageHeight'.
						
						Since we do not know the data type of a missing value in the file header (NULL, '', 0), we explicitly do not check for type safety here, but rather check for 'falsy'. 
						A ! ('NOT') before a value or function negates the evaluation: The condition is met when the evaluation results in false.
					*/
					if( !$imageWidth OR !$imageHeight OR !$imageMimeType OR $fileSize < $imageMinSize ) {
						// 1. error (suspicious file header)
						return array('imagePath' => NULL, 'imageError' => 'Suspicious file header!');
					}
					
				
					#********** VALIDATE IMAGE MIME TYPE **********#
					// Whitelist with allowed MIME TYPES
					// $imageAllowedMimeTypes = array('image/jpg' => '.jpg', 'image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif');
					
					if( array_key_exists($imageMimeType, $imageAllowedMimeTypes) === false ) {
						// 2. error (not permitted image type)
						return array('imagePath' => NULL, 'imageError' => 'This is not a permitted image type!');
					}
					
					
					#********** VALIDATE IMAGE WIDTH **********#
					if( $imageWidth > $imageMaxWidth ) {
						// 3. error (not permitted image width)
						return array('imagePath' => NULL, 'imageError' => 'The maximum image width may be ' . $imageMaxWidth . ' pixels!');
					}
					
					
					#********** VALIDATE IMAGE HEIGHT **********#
					if( $imageHeight > $imageMaxHeight ) {
						// 4. error (not permitted image height)
						return array('imagePath' => NULL, 'imageError' => 'The maximum image height may be ' . $imageMaxHeight . 'pixels!');
					}
					
					
					#********** VALIDATE FILE SIZE **********#
					if( $fileSize > $imageMaxSize ) {
						// 5. error (not permitted file size)
						return array('imagePath' => NULL, 'imageError' => 'The maximum file size may be ' . $imageMaxSize/1024/1000 . 'MB!');
					
					} // 2. IMAGE VALIDATION END
					#*************************************************************#
										
					
					#*************************************************************#
					#********** 3. PREPARE IMAGE FOR PERSISTENT STORAGE **********#
					#*************************************************************#
					
					#********** GENERATE UNIQUE FILE NAME **********#
					/*
						Since the file name itself can contain invalid or hidden characters, double file extensions (file.exe.jpg) and so on, plus all special characters and umlauts need to be removed for security reasons, the file name should be regenerated completely. 
						
						Additionally, the generated filenames must be unique to prevent overwriting each other if two files share the same name.
					*/
					/*
						- 	mt_rand() is the improved version of the rand() function and generates random numbers with a more uniform distribution 
							across the value range. Without additional parameters, it generates numerical values between 0 and the highest possible value that mt_rand() can process.
						- 	str_shuffle() shuffles the characters of a given string randomly.
						- 	microtime() returns a timestamp with millisecond precision (e.g. '0.57914300 163433596'), 
							from which the decimal separator and space are removed for URL-compliant representation.
					*/
					$fileName = mt_rand() . str_shuffle('abcdefghijklmnopqrstuvwxyz__--0123456789') . str_replace('.', '', microtime(true));
					
if(DEBUG_F)		echo "<p class='debug hint validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileName: $fileName <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** GENERATE FILE EXTENSION **********#
					/*
						For security reasons, the original file extension is not used in the filename, but a predefined file extension from the array of allowed MIME types is chosen based on the detected MIME type [key].
					*/
					$fileExtension = $imageAllowedMimeTypes[$imageMimeType];					
if(DEBUG_F)		echo "<p class='debug value hint validateImageUpload'><b>Line " . __LINE__ . "</b>:\$fileExtension: $fileExtension <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** GENERATE FILE TARGET **********#
					/*
						Generate the final storage path on the server:
						destinationPath/fileName.fileExtension
					*/
					$fileTarget = $imageUploadPath . $fileName . $fileExtension;
if(DEBUG_F)		echo "<p class='debug value hint validateImageUpload'><b>Line " . __LINE__ . "</b>:\$fileTarget: $fileTarget <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: Path length: " . strlen($fileTarget) . " characters <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					// 3. PREPARE IMAGE FOR PERSISTENT STORAGE END
					#********************************************************#
					
					
					#********************************************************#
					#********** 4. MOVE IMAGE TO FINAL DESTINATION **********#
					#********************************************************#
					
					if( @move_uploaded_file($fileTemp, $fileTarget) === false ) {
						// 6. error (image cannot be moved)
if(DEBUG_F)			echo "<p class='debug err validateImageUpload'><b>Line " . __LINE__ . "</b>: ERROR attempting to move the image to <i>'$fileTarget'</i>! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						// TODO: entry into the error log - email to the admin
						return array('imagePath' => NULL, 'imageError' => 'An error has occurred! Please contact our support.');
						
					} else {
						// success
if(DEBUG_F)			echo "<p class='debug ok validateImageUpload'><b>Line " . __LINE__ . "</b>: Image successfully moved to <i>'$fileTarget'</i>. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						return array('imagePath' => $fileTarget, 'imageError' => NULL);
					
					} // 4. MOVE IMAGE TO FINAL DESTINATION END
					#*********************************************************#
				
				}	


#**********************************************************************************#