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
					
if(DEBUG_F)		echo "<p class='debug sanitizeString'>🌀 <b>Line " . __LINE__ . "</b>: Invoking " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					/*
						Da in PHP künftig kein Aufruf der PHP-eigenen Funktionen
						mit NULL-Werten erlaubt ist, rufen wir die PHP-Funktionen
						nur auf, wenn $value NICHT NULL ist.
						Für DB-Operationen soll NULL nicht mit Leersteings überschrieben
						werden. Daher wird an dieser Stelle ein Leerstring durch NULL ersetzt.
					*/
					if( $value !== NULL ) {
						
						/*
							SCHUTZ GEGEN EINSCHLEUSUNG UNERWÜNSCHTEN CODES:
							Damit so etwas nicht passiert: <script>alert("HACK!")</script>
							muss der empfangene String ZWINGEND entschärft werden!
							htmlspecialchars() wandelt potentiell gefährliche Steuerzeichen wie
							< > " & in HTML-Code um (&lt; &gt; &quot; &amp;).
							
							Der Parameter ENT_QUOTES wandelt zusätzlich einfache ' in &apos; um.
							Der Parameter ENT_HTML5 sorgt dafür, dass der generierte HTML-Code HTML5-konform ist.
							
							Der 1. optionale Parameter regelt die zugrundeliegende Zeichencodierung 
							(NULL=Zeichencodierung wird vom Webserver übernommen)
							
							Der 2. optionale Parameter bestimmt die Zeichenkodierung
							
							Der 3. optionale Parameter regelt, ob bereits vorhandene HTML-Entities erneut entschärft werden
							(false=keine doppelte Entschärfung)
						*/
						$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, double_encode:false);
						
						$value = trim($value);
					}
					
					/*
						Ein von vornherein übergebener Leerstring sollte an dieser Stelle in NULL
						umgewandelt werden, damit es keine Probleme mit leeren Datenbankfeldern (NULL) gibt, die
						ansonsten durch Leerstrings überschrieben würden.
						
						Sollte $value ausschließlich Whitespaces beinhalten, liefert trim() an dieser
						Stelle einen Leerstring zurück. Dieser Leerstring muss wieder in NULL umgewandelt werden.
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
						Da ein zu prüfender String nicht zwangsläufig aus einem Formular,
						sondern beispielsweise auch aus einem JSON-Objekt stammen könnte, sollten
						hier auch NULL-Werte mit geprüft werden.
					*/
					if( $mandatory === true AND ($value === '' OR $value === NULL) ) {
						// error
						return 'This field is required!'; 
					
					
					#********** MAXIMUM LENGTH CHECK **********#
					/*
						Da die Felder in der Datenbank oftmals eine Längenbegrenzung besitzen,
						die Datenbank aber bei Überschreiten dieser Grenze keine Fehlermeldung
						ausgibt, sondern alles, das über diese Grenze hinausgeht, stillschweigend 
						abschneidet, muss vorher eine Prüfung auf diese Maximallänge durchgeführt 
						werden. Nur so kann dem User auch eine entsprechende Fehlermeldung ausgegeben
						werden.
					*/
					/*
						mb_strlen() erwartet als Datentyp einen String. Wenn (später bei der OOP)
						jedoch ein anderer Datentyp wie Integer oder Float übergeben wird, wirft
						mb_strlen() einen Fehler. Da es ohnehin keinen Sinn macht, einen Zahlenwert
						auf seine Länge (Anzahl der Zeichen) zu prüfen, wird diese Prüfung nur für
						den Datentyp 'String' durchgeführt.
					*/
					/*
						Da die Übergabe von NULL an PHP-eigene Funktionen in künftigen PHP-Versionen 
						nicht mehr erlaubt ist, muss vor jedem Aufruf einer PHP-Funktion sichergestellt 
						werden, dass der zu übergebende Wert nicht NULL ist.
					*/
					} elseif( $value !== NULL AND mb_strlen($value) > $maxLength ) {
						// error
						return "May not be longer than $maxLength characters!";
						
						
					#********** MINIMUM LENGTH CHECK **********#
					/*
						Es gibt Sonderfälle, bei denen eine Mindestlänge für einen Userinput
						vorgegeben ist, beispielsweise bei der Erstellung von Passwörtern.
						Damit nicht-Pflichtfelder aber auch weiterhin leer sein dürfen, muss
						die Mindestlänge als Standardwert mit 0 vorbelegt sein.
						
						Bei einem optionalen Feldwert, der gleichzeitig eine Mindestlänge
						einhalten muss, darf die Prüfung keine Leerstrings validieren, da 
						diese nie die Mindestlänge erfüllen und somit der Wert nicht mehr 
						optional wäre.
					*/
					/*
						mb_strlen() erwartet als Datentyp einen String. Wenn (später bei der OOP)
						jedoch ein anderer Datentyp wie Integer oder Float übergeben wird, wirft
						mb_strlen() einen Fehler. Da es ohnehin keinen Sinn macht, einen Zahlenwert
						auf seine Länge (Anzahl der Zeichen) zu prüfen, wird diese Prüfung nur für
						den Datentyp 'String' durchgeführt.
					*/
					/*
						Da die Übergabe von NULL an PHP-eigene Funktionen in künftigen PHP-Versionen 
						nicht mehr erlaubt ist, muss vor jedem Aufruf einer PHP-Funktion sichergestellt 
						werden, dass der zu übergebende Wert nicht NULL ist.
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
						Da ein zu prüfender String nicht zwangsläufig aus einem Formular,
						sondern beispielsweise auch aus einem JSON-Objekt stammen könnte, sollten
						hier auch NULL-Werte mit geprüft werden.
					*/
					if( $mandatory === true AND ($value === '' OR $value === NULL) ) {
						// error
						return 'This field is required!'; 					
					
					
					#********** VALIDATE EMAIL ADDRESS FORMAT **********#
					/*
						Da die Übergabe von NULL an PHP-eigene Funktionen in künftigen PHP-Versionen 
						nicht mehr erlaubt ist, muss vor jedem Aufruf einer PHP-Funktion sichergestellt 
						werden, dass der zu übergebende Wert nicht NULL ist.
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
						
						Die Informationen, die immer in jedem Bildheader oder Dateiheader eines Bildes vorhanden sind, können 
						je nach dem spezifischen Bildformat variieren. Es gibt jedoch einige grundlegende Informationen, die in 
						den meisten gängigen Bildformaten vorkommen und als Pflichtangaben angesehen werden. 
						Zu den typischen Pflichtangaben gehören:

						- Dateisignatur: Jedes Bildformat hat eine eindeutige Dateisignatur, die am Anfang der Datei steht und 
						  auf das Format hinweist. Die Dateisignatur ist entscheidend, um das Dateiformat zu identifizieren.

						- Dateigröße: Die Größe der Bilddatei in Bytes oder Kilobytes ist in den meisten Dateiheadern enthalten. 
						  Dies ist wichtig für die Speicherplatzverwaltung und das Einlesen der Datei.

						- Bildabmessungen: Informationen über die Breite und Höhe des Bildes in Pixeln sind entscheidend, um die 
						  richtige Darstellung des Bildes zu gewährleisten. Diese Informationen sind nahezu immer im Dateiheader vorhanden.

						- Farbtiefe: Die Farbtiefe gibt an, wie viele Farben pro Pixel im Bild dargestellt werden können. 
						  Bei RGB-Bildern beträgt die übliche Farbtiefe 24 Bit (8 Bit pro Kanal), was 16,7 Millionen Farben entspricht. 
						  Dies ist eine grundlegende Information im Header.
											  
						  Diese Angaben sind in den meisten gängigen Bildformaten zu finden und gelten als grundlegende Pflichtangaben im 
						  Dateiheader. 
					*/
					/*
						Die Funktion getimagesize() liest den Dateiheader einer Bilddatei aus und 
						liefert bei gültigem MIME Type ('image/...') ein gemischtes Array zurück:
						
						[0] 				Bildbreite in PX (Bildabmessungen)
						[1] 				Bildhöhe in PX  (Bildabmessungen)
						[3] 				Einen für das HTML <img>-Tag vorbereiteten String (width="480" height="532") 
						['bits']			Anzahl der Bits pro Kanal (Farbtiefe)
						['channels']	Anzahl der Farbkanäle (somit auch das Farbmodell: RGB=3, CMYK=4) 
						['mime'] 		MIME Type
						
						Bei ungültigem MIME Type (also nicht 'image/...') liefert getimagesize() false zurück
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
							SONDERFALL NUMBER (NUMERIC STRINGS):
							Da wir aus Formularen und anderen Usereingaben alle Werte immer
							als Datentyp String erhalten, macht eine Prüfung auf einen konkreten
							numerischen Datentyp in PHP nur selten Sinn.
							
							Anstatt mittels is_int() direkt auf den Datentyp Integer zu prüfen,
							ist es besser, einen empfangenen String auf sein inhaltliches Format 
							zu prüfen: Ist der String numerisch und entspricht sein Wert einem Integer?

							Die Funktion filter_var() kann mittels eines regulären Ausdrucks, der über
							eine Konstante gesteuert wird, auch einen String auf den Inhalt 'Integer' oder
							'Float' überprüfen.

							Entspricht der mittels filter_var() geprüfte Wert dem zu prüfenden Datenformat,
							nimmt filter_var automatisch eine Typumwandlung vor und liefert den umgewandelten 
							Wert zurück.
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
						Diese Prüfung setzt darauf, dass ein maniplulierter Dateiheader nicht konsequent
						gefälscht wurde:
						Ein Hacker ändert den MimeType einer Textdatei mit Schadcode aud 'image/jpg', vergisst
						allerdings, zusätzlich weitere Einträge wie 'imageWidth' oder 'imageHeight' hinzuzufügen.
						
						Da wir den Datentyp eines im Dateiheader fehlenden Wertes nicht kennen (NULL, '', 0), 
						wird an dieser Stelle ausdrücklich nicht typsicher, sondern auf 'falsy' geprüft.
						Ein ! ('NOT') vor einem Wert oder einer Funktion negiert die Auswertung: Die Bedingung 
						ist erfüllt, wenn die Auswertung false ergibt.
					*/
					if( !$imageWidth OR !$imageHeight OR !$imageMimeType OR $fileSize < $imageMinSize ) {
						// 1. Fehlerfall (verdächtiger Datei Header)
						return array('imagePath' => NULL, 'imageError' => 'Suspicious file header!');
					}
					
				
					#********** VALIDATE IMAGE MIME TYPE **********#
					// Whitelist mit erlaubten MIME TYPES
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
						return array('imagePath' => NULL, 'imageError' => 'The maximum file size may be ' . $imageMaxSize/1024 . 'pixels!');
					
					} // 2. IMAGE VALIDATION END
					#*************************************************************#
										
					
					#*************************************************************#
					#********** 3. PREPARE IMAGE FOR PERSISTANT STORAGE **********#
					#*************************************************************#
					
					#********** GENERATE UNIQUE FILE NAME **********#
					/*
						Da der Dateiname selbst Schadcode in Form von ungültigen oder versteckten Zeichen,
						doppelte Dateiendungen (dateiname.exe.jpg) etc. beinhalten kann, darüberhinaus ohnehin 
						sämtliche, nicht in einer URL erlaubten Sonderzeichen und Umlaute entfernt werden müssten 
						sollte der Dateiname aus Sicherheitsgründen komplett neu generiert werden.
						
						Hierbei muss außerdem bedacht werden, dass die jeweils generierten Dateinamen unique
						sein müssen, damit die Dateien sich bei gleichem Dateinamen nicht gegenseitig überschreiben.
					*/
					/*
						- 	mt_rand() stellt die verbesserte Version der Funktion rand() dar und generiert 
							Zufallszahlen mit einer gleichmäßigeren Verteilung über das Wertesprektrum. Ohne zusätzliche
							Parameter werden Zahlenwerte zwischen 0 und dem höchstmöglichem von mt_rand() verarbeitbaren 
							Zahlenwert erzeugt.
						- 	str_shuffle() mischt die Zeichen eines übergebenen Strings zufällig durcheinander.
						- 	microtime() liefert einen Timestamp mit Millionstel Sekunden zurück (z.B. '0.57914300 163433596'),
							aus dem für eine URL-konforme Darstellung der Dezimaltrenner und das Leerzeichen entfernt werden.
					*/
					$fileName = mt_rand() . str_shuffle('abcdefghijklmnopqrstuvwxyz__--0123456789') . str_replace('.', '', microtime(true));
if(DEBUG_F)		echo "<p class='debug hint validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileName: $fileName <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** GENERATE FILE EXTENSION **********#
					/*
						Aus Sicherheitsgründen wird nicht die ursprüngliche Dateinamenerweiterung aus dem
						Dateinamen verwendet, sondern eine vorgenerierte Dateiendung aus dem Array der 
						erlaubten MIME Types.
						Die Dateiendung wird anhand des ausgelesenen MIME Types [key] ausgewählt.
					*/
					$fileExtension = $imageAllowedMimeTypes[$imageMimeType];					
if(DEBUG_F)		echo "<p class='debug value hint validateImageUpload'><b>Line " . __LINE__ . "</b>:\$fileExtension: $fileExtension <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** GENERATE FILE TARGET **********#
					/*
						Endgültigen Speicherpfad auf dem Server generieren:
						destinationPath/fileName.fileExtension
					*/
					$fileTarget = $imageUploadPath . $fileName . $fileExtension;
if(DEBUG_F)		echo "<p class='debug value hint validateImageUpload'><b>Line " . __LINE__ . "</b>:\$fileTarget: $fileTarget <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: Path length: " . strlen($fileTarget) . " characters <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					// 3. PREPARE IMAGE FOR PERSISTANT STORAGE END
					#********************************************************#
					
					
					#********************************************************#
					#********** 4. MOVE IMAGE TO FINAL DESTINATION **********#
					#********************************************************#
					
					if( @move_uploaded_file($fileTemp, $fileTarget) === false ) {
						// 6. error (image cannot be moved)
if(DEBUG_F)			echo "<p class='debug err validateImageUpload'><b>Line " . __LINE__ . "</b>: ERROR attempting to move the image to <i>'$fileTarget'</i>! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						// TODO: entry into the error log - email to the admin
						return array('imagePath' => NULL, 'imageError' => 'An error has occured! Please contact our support.');
						
					} else {
						// success
if(DEBUG_F)			echo "<p class='debug ok validateImageUpload'><b>Line " . __LINE__ . "</b>: Image successfully moved to <i>'$fileTarget'</i>. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						return array('imagePath' => $fileTarget, 'imageError' => NULL);
					
					} // 4. MOVE IMAGE TO FINAL DESTINATION END
					#*********************************************************#
				
				}	


#**********************************************************************************#