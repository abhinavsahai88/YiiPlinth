<?php

class Facebook extends OAuth
{
	public $host='https://www.facebook.com/dialog';

	public $permissions='email';

	public $endpointURLs = array(
		'authenticate'=>array(
			'url'=>'/oauth/authenticate', 
			'method'=>'get'),
		'authorize'=>array(
			'url'=>'/oauth/authorize', 
			'method'=>'get'),

		'access'=>array(
			'url'=>'https://graph.facebook.com/oauth/access_token',
			'method'=>'get'),
		'request'=>array(
			'url'=>'/oauth', 
			'method'=>'get',
			'type'=>'redirect',),
		'userinfo'=>array(
			'url'=>'https://graph.facebook.com/me',
			'method'=>'get'),
		);

	public function getProviderName()
	{
		return 'Facebook';
	}

	protected function getVerificationParameter()
	{
		return 'code';
	}

	protected function getTokenParameter()
	{
		return 'state';
	}

	protected function isOAuthVerified($toRequest)
	{
		return isset($toRequest['code']);
	}

	protected function addAccessTokenParameters($toParameters, $toOAuthUser)
	{
		$toParameters['client_secret']=$this->getConsumerSecret();
		$toParameters['oauth_callback']=$toOAuthUser->Secret;
		return $toParameters;
	}

	protected function getUserForAccessRequest($taParameters)
	{
		return $this->getUserForToken($taParameters['access_token']);
	}

	protected function updateOAuthUserInfo($taParameters, $toOAuthUser)
	{
		$loParameters = array('access_token'=>$taParameters['access_token']);
		$loRequest = $this->makeRequest($this->getEndpoint('userinfo'), $loParameters, NULL);

		$laParameters = json_decode($loRequest['response'], true);

		$toOAuthUser->setAttributes(Array(
			'Token'=>$taParameters['access_token'],
			'Expires'=>Utilities::getTimestamp() + $taParameters['expires'] * 1000,
			'UID'=>$laParameters['id'],
			'DisplayName'=>$laParameters['username'],
			'UserName'=>$laParameters['username'],
			));

		return $laParameters;
	}

	protected function populateUser($toUser, $toOAuthUser, $toExtraInfo)
	{
		// TODO: Create and parse a UserInfo here
		// TODO: Parse this additional information

		/*
		    [id] => 517296295
		    [name] => Ken McHugh
		    [first_name] => Ken
		    [last_name] => McHugh
		    [link] => http://www.facebook.com/kmchugh12
		    [username] => kmchugh12
		    [gender] => male
		    [email] => ken_mchugh@hotmail.com
		    [timezone] => 8
		    [locale] => en_GB
		    [verified] => 1
		    [updated_time] => 2012-07-28T07:04:18+0000
		 */
	}

	protected function parseEmail($taParameters)
	{
		return isset($taParameters['email']) ? urldecode($taParameters['email']) : '';
	}

	protected function processParameters($toParameters)
	{
		$laReturn = array();
		foreach ($toParameters as $lcKey => $lcValue) 
		{
			if ($lcKey === 'oauth_callback')
			{
				$lcKey = 'redirect_uri';
			}
			else if ($lcKey === 'oauth_consumer_key')
			{
				$lcKey = 'client_id';
			}
			else if ($lcKey === 'oauth_nonce')
			{
				$lcKey = 'state';
			}
			else if ($lcKey === 'oauth_permissions')
			{
				$lcKey = 'scope';
			}
			else if ($lcKey === 'oauth_verifier')
			{
				$lcKey = 'code';
			}
			if (!is_null($lcKey))
			{
				$laReturn[$lcKey] = $lcValue;
			}
		}
		return $laReturn;
	}

	public function handleOAuthResponse1($toRequest)
	{
		if (isset($toRequest['state']))
		{
			Utilities::printVar($toRequest);
			$loUser = $this->getUserForToken($toRequest['state']);

			Utilities::printVar($loUser);
		}
		exit();

		
	}


}

?>