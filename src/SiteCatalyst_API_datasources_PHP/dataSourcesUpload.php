<?php
require_once('nusoap.php');
/* Include the common functions to send a SOAP request to SiteCatalyst. */
require_once('library/SOAPRequest.php');
/* DataSource.BeginDataBlock - sends data to Analytics using a DataSource */ 
$blockName = 'Adwords Data'; 

$colNameArray = array('Date','Tracking Code', 'Event 84', 'Event 83', 'Event 82'); 
$rowData = array( 
array('1/13/2014','NA:google:cpl:NA:NA:PS4_AR:NA:NA:NA:NA:NA:NA:NA:NA:NA ','455','003456','89')
//array('1/14/2014','100303','455','003456','98') 
); 

/* Set the endOfBlock to 0 to continue appending data. */ 
$endOfBlock=0; 

$params = array( 
'blockName'=>$blockName, 
'dataSourceID'=>1, 
'reportSuiteID'=>'soqdev',//$rsId, 
'columnNames'=>$colNameArray, 
'rows'=>$rowData, 
'endOfBlock'=>$endOfBlock 
); 

$result = sendRequest('DataSource.BeginDataBlock', $params ); 

$blockID = $result['blockID']; 
var_dump($result); 
sleep(3); 

/* DataSource.AppendDataBlock - sends data (continued) to Analytics using a DataSource  
$rowData = array( 
array('1/11/2011','100300','455','003456'), 
array('1/12/2011','100301','455','003456') 
); */

/* Set the endOfBlock to '' to stop appending data.  
$endOfBlock=''; 
$params = array( 
'blockID'=>$blockID, 
'dataSourceID'=>$dsId, 
'reportSuiteID'=>$rsId, 
'rows'=>$rowData, 
'endOfBlock'=>$endOfBlock 
); 

$result = sendRequest('DataSource.AppendDataBlock', $params ); 
var_dump($result); 
sleep(3); */
?>