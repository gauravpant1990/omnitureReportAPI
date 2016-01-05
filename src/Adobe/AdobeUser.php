<?php 
$nonce = md5(rand(), TRUE);
$adobeUsername = "gaurav.pant:Souq";
$created = gmdate('Y-m-d\TH:i:sO');
$sharedSecret = "c72bbe25522373e187ca3c886b0a6a23";
$b64nonce = base64_encode($nonce);
$adobePasswordDigest = base64_encode(sha1($nonce . $created . $sharedSecret, TRUE));