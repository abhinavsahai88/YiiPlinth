<?php

	abstract class Utilities
	{
		/**
		* Returns true if this request was in development mode
		**/
		public static function isDevelopment()
		{
			$lcKey = $_SERVER['SERVER_NAME'].'_developmentRequest';
			if (!isset($GLOBALS[$lcKey]))
			{
				// Assume that a server with localhost or ending with .dev is a dev machine
				$GLOBALS[$lcKey] = preg_match('/^localhost(.+)?|\.dev$/i', $_SERVER['SERVER_NAME']) > 0;

				if ($GLOBALS[$lcKey])
				{
					defined('YII_DEBUG') or define('YII_DEBUG',true);
					// specify how many levels of call stack should be shown in each log message
					defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
				}
			}
			return $GLOBALS[$lcKey];
		}

		/**
		* Includes the files specified, and if the return value from the
		* file specified is an array, merges the results in the order given
		* in the array.  
		* If the file does not exist it is skipped over
		* If the file does not return an array, the result of that specific 
		* file is not merged, but the process continues to attempt the remaining
		* files.
		**/
		public static function mergeIncludedArray($taFileList)
		{
			$loConfig = array();
			foreach ($taFileList as $laFile)
			{
				if (file_exists($laFile))
				{
					$loReturn = include($laFile);
					if (gettype($loReturn) === 'array')
					{
						$loConfig = CMap::mergeArray($loConfig, $loReturn);
					}
				}
			}
			return $loConfig;
		}



		private static $g_oTwitter = NULL;

		/**
		* Authenticates in as the application and attempts to get the Authentication URL
		**/
		public static function getTwitterAuthenticationURL($tcCallbackURL = NULL)
		{
			$loTwitterObject = self::getTwitterObject($tcCallbackURL, TRUE);
			return $loTwitterObject->getAuthorizeUrl($_SESSION['twitter_token']);
		}

		// TODO: Refactor Twitter to separate class
		// TODO: Add an expiry time to OAuthUser which will force a recheck of the connection
		/**
		* Gets the Twitter oAuth object.  if tlConnectAsConsumer is true this will attempt to 
		* connect as the consumer, otherwise it will connect as the client
		**/
		private static function getTwitterObject($tcCallbackURL = NULL, $tlConnectAsConsumer = TRUE)
		{
			if (is_null($tcCallbackURL))
			{
				$tcCallbackURL = Yii::app()->params['twitter']['callbackURL'];
			}


			if (!$tlConnectAsConsumer)
			{
				// Update the user information
				$loAuthUser = OAuthUser::model()->findByAttributes(array('UserGUID'=>Yii::app()->user->getState('GUID'), 'Provider'=>'Twitter'));
				if (!is_null($loAuthUser))
				{
					$_SESSION['twitter_token'] = $loAuthUser->Token;
					$_SESSION['twitter_token_secret'] = $loAuthUser->Secret;
				}
			}

			// TODO: Consume TwitterOAuth can probably be cached globally, client may be able to be cached in the session
			$loTwitterObject = new TwitterOAuth(
				Yii::app()->params['twitter']['consumerKey'], 
				Yii::app()->params['twitter']['consumerSecret'],
				$tlConnectAsConsumer ? NULL : $_SESSION['twitter_token'],
				$tlConnectAsConsumer ? NULL : $_SESSION['twitter_token_secret']);

			if ($tlConnectAsConsumer)
			{
				// TODO: Add in error handling
				Yii::app()->params['oauth_twitter'] = $loTwitterObject->getRequestToken($tcCallbackURL);
				$_SESSION['twitter_token']=Yii::app()->params['oauth_twitter']['oauth_token'];
				$_SESSION['twitter_token_secret']=Yii::app()->params['oauth_twitter']['oauth_token_secret'];
			}
			return $loTwitterObject;
		}

		/**
		* Handles the callback from Twitter and creates any records required
		**/
		public static function handleTwitterCallback()
		{
			if( !empty($_GET['oauth_verifier']) && 
				!empty($_SESSION['twitter_token']) && 
				!empty($_SESSION['twitter_token_secret']))
			{
				$loTwitter = self::getTwitterObject(NULL, FALSE);
				if (!is_null($loTwitter))
				{
					$_SESSION['twitter_access_token'] = $loTwitter->getAccessToken($_GET['oauth_verifier']);

					$loAuthUser = self::getTwitterUser($loTwitter);
					if (!is_null($loAuthUser))
					{
						$loAuthUser->Token=$_SESSION['twitter_access_token']['oauth_token'];
						$loAuthUser->Secret=$_SESSION['twitter_access_token']['oauth_token_secret'];
						$loAuthUser->save();
					}
				}
				return $loAuthUser;
			}
			unset($_SESSION['twitter_token']);
			return NULL;
		}

		public static function sendTweet($tcMessage)
		{
			// Only if the user is linked to a twitter account
			$loAuthUser = OAuthUser::model()->findByAttributes(array('UserGUID'=>Yii::app()->user->getState('GUID'), 'Provider'=>'Twitter'));
			if (!is_null($loAuthUser))
			{
				$loTwitter = self::getTwitterObject(NULL, FALSE);
				if (!is_null($loTwitter))
				{
					$loTwitter->post('statuses/update', array('status' => $tcMessage)); 
				}
			}
		}

		public static function getTwitterUser($toTwitterObject = NULL)
		{
			// Update the user information
			$loAuthUser = OAuthUser::model()->findByAttributes(array('UserGUID'=>Yii::app()->user->getState('GUID'), 'Provider'=>'Twitter'));

			if (is_null($loAuthUser) && !is_null($toTwitterObject))
			{
				$loUserInfo = $toTwitterObject->get('account/verify_credentials');

				if (isset($loUserInfo->error))
				{
					echo $loUserInfo->error;
					$loUserInfo = null;
				}

				if ($loUserInfo != NULL)
				{
					$loUser = User::model()->findByAttributes(array('GUID'=>Yii::app()->user->getState('GUID')));

					$loAuthUser = new OAuthUser();
					$loAuthUser->Provider='Twitter';
					$loAuthUser->UserID=$loUser->UserID;
					$loAuthUser->UserGUID=$loUser->GUID;
					$loAuthUser->UID=$loUserInfo->id;
					$loAuthUser->DisplayName=$loUserInfo->screen_name;
					$loAuthUser->UserName=$loUserInfo->name;
				}
			}
			return $loAuthUser;
		}
		
		public static function tweet($tcMessage, $tcAuthToken)
		{
			$loTwitter = self::getTwitterObject();
		}

		/**
		* Gets the current URL
		**/
		public static function getURL()
		{
			return Yii::app()->createAbsoluteUrl(str_replace(
						Yii::app()->request->baseUrl, '',
						Yii::app()->request->requestURI));
		}

		// TODO: Refactor Bit.ly to separate class
		/**
		* Shortens the specified URL using bit.ly
		**/
		public static function shortenURL($tcURL, $tcLogin = NULL, $tcAppKey = NULL)
		{
			// TODO: Cache and return cached if it exists
			if ($tcLogin === NULL)
			{
				$tcLogin = Yii::app()->params['bit.ly']['login'];
			}
			if ($tcAppKey === NULL)
			{
				$tcAppKey = Yii::app()->params['bit.ly']['key'];
			}
			if (!Utilities::endsWith($tcURL, '/'))
			{
				$tcURL.='/';
			}
			if (Utilities::startsWith($tcURL, '/'))
			{
				$tcURL = substr($tcURL, 1);
			}
			if (!Utilities::startsWith($tcURL, 'http'))
			{
				$tcURL = Yii::app()->getBaseUrl(true).$tcURL;
			}
			$tcURL = 'http://api.bit.ly/v3/shorten?login='.$tcLogin.'&apiKey='.$tcAppKey.'&format=txt&longUrl='.urlencode($tcURL);
			return @trim(file_get_contents($tcURL));
		}



		/**
		* Returns the encrypted value with the email seed
		**/
		public static function getPasswordValue($tcEmail, $tcPassword)
		{
			return md5(strtolower($tcEmail).'|'.$tcPassword);
		}
	
		/**
		* returns the first non null parameter
		**/
		public static function ISNULL()
		{
			foreach (func_get_args() as $loArg)
			{
				if (!is_null($loArg))
				{
					return $loArg;
				}
			}
			return NULL;
		}

		/**
		* Checks if a string ends with the specified string
		* returns true if the string ends with the specified string, false otherwise
		**/
		public static function endsWith($tcSearchIn, $tcSearchFor, $tlCaseInsensitive = false)
		{
			if ($tlCaseInsensitive)
			{
				$tcSearchFor = strtolower($tcSearchFor);
				$tcSearchIn = strtolower($tcSearchIn);
			}
			$lnLength = strlen($tcSearchFor);
			$lnStart = $lnLength * -1;
			return (substr($tcSearchIn, $lnStart) === $tcSearchFor);
		}

		/**
		* Gets the localised version of the date for the user
		**/
		public static function dateftime($tnMillis)
		{
			// TODO: Allow this to output javascript code for converting timestamp for local time,
			// or convert the time to the users region

			return $tnMillis <= 0 ? '' : strftime('%d %b %Y %I:%M:%m %Z', $tnMillis / 1000);
		}

		/**
		* Checks if a string starts with the specified string
		* returns true if the string starts with the specified string, false otherwise
		**/
		public static function startsWith($tcSearchIn, $tcSearchFor, $tlCaseInsensitive = false)
		{
			if ($tlCaseInsensitive)
			{
				$tcSearchFor = strtolower($tcSearchFor);
				$tcSearchIn = strtolower($tcSearchIn);
			}
			$lnLength = strlen($tcSearchFor);
			return (substr($tcSearchIn, 0, $lnLength) === $tcSearchFor);
		}

		/**
		* Outputs the contents of the variable
		**/
		public static function printVar($toVariable)
		{
			echo '<pre>'.((is_null($toVariable) || !isset($toVariable)) ? 'NULL' : print_r($toVariable, 1)).'</pre>';
		}

		/**
		* Implodes an array, using the key and value to generate a string.
		* The resulting string would look like the following:
		* {key}{glue}{value}{value separator}
		**/
		public static function array_implode($tcGlue = '=', $tcValueSeparator = ', ' , array $taArray)
		{
			$laReturn = array();
			foreach ($taArray as $lcKey => $lcValue) 
			{
				$laReturn[] = "{$lcKey}{$tcGlue}{$lcValue}";
			}
			return implode($tcValueSeparator, $laReturn);
		}

		/**
		* Gets the date in a format suitable for HTTP, 
		* tnTimestamp is the numeric long representation of a date
		**/
		public static function getRFC1123Date($tnTimestamp)
		{
			return gmdate('r', $tnTimestamp);
		}

		/**
		* Returns a unique identifier
		**/
		public static function getStringGUID()
		{
			return str_replace('.', '', uniqid('', TRUE));
		}

		/**
		* Retrieves a timestamp based on the current time.
		**/
		public static function getTimestamp()
		{
			// Update the tracking fields if they exist
			list($laUsec, $laSec) = explode(" ", microtime());
   			return ((float)$laUsec + ((float)$laSec * 1000.00));
		}

		public static function scientificToLong($tnScientific)
		{
			return !is_null($tnScientific) ?
			 number_format($tnScientific, 0, '.', '') :
			 0;
		}

		function array_change_key_case_recursive(array $taInput, $tnCase = CASE_LOWER) 
		{ 
			$loReturn = array();
			foreach($taInput as $lcKey=>$loValue)
			{
				if (!is_array($taInput[$lcKey]))
				{
					$loReturn[$tnCase === CASE_UPPER ? mb_strtoupper($lcKey) : mb_strtolower($lcKey)] = 
						$loValue;
				}
				else
				{
					$loReturn[$tnCase === CASE_UPPER ? mb_strtoupper($lcKey) : mb_strtolower($lcKey)]=self::array_change_key_case_recursive($loValue, $tnCase);
				}
			}
			return $loReturn;
		} 

		/**
		* Recursively merges two arrays, this will alter the value of $taArray
		**/
		public static function inline_array_merge_recursive(&$taArray, $taMerge)
		{
			foreach ($taMerge as $lcKey => $loValue) 
			{
				if (isset($taArray[$lcKey]))
				{
					// Merge
					if (is_array($taArray[$lcKey]))
					{
						if (is_array($taMerge))
						{
							self::inline_array_merge_recursive($taArray[$lcKey], $loValue);
						}
						else
						{
							$taArray[$lcKey][] = $taMerge;
						}
					}
					else
					{
						// Convert to array then merge
						$loTemp = $taArray[$lcKey];
						$taArray[$lcKey] = array();
						$taArray[$lcKey][] = $loTemp;
						$taArray[$lcKey][] = $loValue;
					}
				}
				else
				{
					// New
					$taArray[$lcKey] = $loValue;
				}
			}
			return $taArray;
		}
	}

?>