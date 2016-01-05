<?php
class RequestManager
{
	public $adobeUsername;
	public $adobePasswordDigest;
	public $b64nonce;
	public $created;
	public $sharedSecret;
	public $host = 'https://api.omniture.com/admin/1.3/rest/';
	function __construct()
	{
		global $adobeUsername, $created, $sharedSecret;//$b64nonce, $adobePasswordDigest, 
		$nonce = md5(rand(), TRUE);
		$b64nonce = base64_encode($nonce);
		$adobePasswordDigest = base64_encode(sha1($nonce . $created . $sharedSecret, TRUE));
		$this->adobeUsername = $adobeUsername;
		$this->adobePasswordDigest = $adobePasswordDigest;
		$this->b64nonce = $b64nonce;
		$this->created = $created;
		$this->sharedSecret = $sharedSecret;
		
	}

	/*function queueRankedReport($params){
		$host = $this->host.'?method=Report.QueueRanked';
		$data = json_encode($params);
		return $this->sendRequest($data, $host);
	}

	function getStatus($reportIds){
		$host = $this->host.'?method=Report.GetStatus';
		$data = json_encode($reportIds);
		return $this->sendRequest($data, $host);
	}

	function getReport($reportIds){
		$host = $this->host.'?method=Report.GetReport';
		$data = json_encode($reportIds);
		return $this->sendRequest($data, $host);
	}*/

	function reportOperation($data, $type){
		$host = $this->host.'?method='.$type;
		$data = json_encode($data);
		return $this->sendRequest($data, $host);
	}

	function sendRequest($data, $host)
	{
		//Creating the WSSE Header: https://marketing.adobe.com/developer/documentation/authentication-1/wsse-authentication-2
		//X-WSSE: UsernameToken Username="gaurav.pant:Souq", PasswordDigest="wdgRL3tuvFdPmzuVAE6n0UkCP/M=", Nonce="Mzk1MmE0ZTRlOTlmMjJlM2JjMGQ5MDJl", Created="2015-06-04T06:36:23Z"		
		$nonce = md5(rand(), TRUE);
		$b64nonce = base64_encode($nonce);
		$created = gmdate('Y-m-d\TH:i:sO');
		$adobePasswordDigest = base64_encode(sha1($nonce . $created . $this->sharedSecret, TRUE));
		$token = array(
			sprintf('X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
			  $this->adobeUsername,
			  $adobePasswordDigest,
			  $b64nonce,
			  $created
			)
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $token);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}
?>