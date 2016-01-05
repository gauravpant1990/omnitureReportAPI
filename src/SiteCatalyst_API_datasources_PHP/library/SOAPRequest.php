<?php

/* Generates the security header required to access the SiteCatalyst API */  

function getSecurityHeader()
{    
  $auth_login = 'gaurav.pant:Souq'; 
  $auth_password = 'c72bbe25522373e187ca3c886b0a6a23';
  $company = 'Souq';
  $nonce_part = 0;
  /* seed random */
  list($usec, $sec) = explode(' ', microtime());
  /*srand((float) $sec + ((float) $usec * 100000));*/
  /*$nonce = md5(rand());*/
  $nonce =  $nonce_part . '-' . $sec . '-' . $usec ;
  /*echo "NONCE: $nonce\n";*/
  $created = date("Y-m-d H:i:s");
  $combo_string = $nonce . $created . $auth_password;

/* Note: the sha1 command is not available in all versions of PHP. If your version of PHP does not support this command, you
can use openssl directly with the command:
echo -n <string> | openssl dgst -sha1 */

  $sha1_string = sha1($combo_string);
  $pwDigest = base64_encode($sha1_string);
  $headers = '<wsse:Security SOAP-ENV:mustUnderstand="1">
  <wsse:UsernameToken wsu:Id="User">
  <wsse:Username>'.$auth_login.'</wsse:Username>
  <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-
  wss-username-token-profile-1.0#PasswordDigest">'.$pwDigest.'</wsse:Password>
  <wsse:Nonce>'.$nonce.'</wsse:Nonce>
  <wsu:Created>'.$created.'</wsu:Created>
  </wsse:UsernameToken>
  </wsse:Security>';

  if($company){
      $headers .= '<AdminInfo>
          <company>'.$company.'</company>
          </AdminInfo>';
  }
  # echo "headers: $headers\n";

  return $headers;

}/* getSecurityHeader()*/

/**
* send a SOAP request to SiteCatalyst
*
* @param    string        $report_type   
* @param    array        $params       Contains the Report Description for the request
*
* @return    array        $response       Data Structure containing report description and data requsted
*/

function sendRequest($report_type, $params )
{    
  global $soap_client, $debug;//
  //var_dump(getSecurityHeader());return;
  //$soap_client = new nusoap_client( '../wsdl/adobe_analytics_service-1.4.wsdl', true );
  $response = $soap_client->call(
         $report_type,
         $params,
          'http://www.omniture.com',        //namespace
          '',                    //Soap Action
         getSecurityHeader()
 );

  if($debug) echo "\n=== REQUEST ===\n" . $soap_client->request . "\n";

  if($debug) echo "\n=== RESPONSE ===\n" . $soap_client->response . "\n";

  /* Check the request response for issues */
  if($err = $soap_client->getError())
      throw new Exception("sendRequest(): SOAP ERROR: $err", 0);

  return($response);

}  
?>