<?php

require_once('nusoap.php');
/* Include the common functions to send a SOAP request to SiteCatalyst. */
require_once('library/SOAPRequest.php');

/* Set the variables for the environment */
ini_set('memory_limit', -1);
$debug = true;

/* Set a variable for the local copy of the wsdl file */
$wsdl = 'wsdl/adobe_analytics_service-1.4.wsdl';//OmnitureAdminServices.wsdl

/* Error if wsdl file not found */
if ( !is_file($wsdl) ) throw new Exception('WSDL not located.', 0);

/* Setup Data Center Soap Client object */
$soap_client = new soap_client($wsdl, TRUE);

try{
	/* Set up key variables */
	$rsId = 'souqaeprod';
	$dsId = ''; // DataSource id - Numeric (this is not yet initialized)
	$email = 'gpant@souq.com'; 
	$dsName = 'Adwords Data';  // DataSource name  
	$dsType = '32'; // Identifier for Generic Summary type of DataSources with Transaction Id
	
	
	/* DataSource.GetIDs - retrieve a list of all DataSources that exist within the report suite */
	$result = sendRequest('DataSource.GetIDs', array('reportSuiteID'=>$rsId) );
	
	echo "Get IDs: \n";
	var_dump($result);return;
	sleep(2);
	
	foreach($result as $ds){
		if($ds['dataSourceName'] == $dsName){
			$dsId = $ds['dataSourceId'];
		}
	}
	
	/* DataSource.SetupGeneric - creates a new DataSource (type Generic) */
	if(! $dsId){  // if this DataSource doesn't exist then set it up

		$dsSettings = array(
		    'dataSourceName'=>$dsName,  
		    'dataSourceEmail'=> $email, 
		    'metricNames'=>array('Offline Revenue'),
		    'metricEvents'=>array('event 2'),
		    'dimensionNames'=>array('Products'),
		    'dimensionVariables'=>array('Product')
		);
		
		$result = sendRequest('DataSource.SetupGeneric', array('reportSuiteID'=>$rsId, 'dataSourceID'=>$dsId,  'dataSourceType'=>$dsType, 'dataSourceSettings'=>$dsSettings ) );
		$dsId = $result['dataSourceID'];
		
		var_dump($result);
		sleep(4);
	}
	
	/* DataSource.GetFileIDs - Get File IDs for a DataSource in a Report Suite */

    $params = array(
       'dataSourceID'=>$dsId,
       'filter'=>'',
       'reportSuiteID'=>$rsId
    );

    $result = sendRequest('DataSource.GetFileIDs', $params );
    var_dump($result);
    sleep(2);   
    
    /* DataSource.GetFileStatus - Get the File Status for a DataSource in a Report Suite */

    $params = array(
       'dataSourceFileID'=>$dsId,
       'reportSuiteID'=>$rsId
    );

    $result = sendRequest('DataSource.GetFileStatus', $params );
    var_dump($result);

    
	/* DataSource.BeginDataBlock - sends data to Omniture using a DataSource */
	
	$blockName = 'OfflineRevenueBlock1';
	$colNameArray = array('Date','Product','Event 2','transactionID');
	
	$rowData = array(
		array('01/13/2011','100302','455','003456'),
		array('01/14/2011','100303','455','003456')
	);
	
	/* Set the endOfBlock to 0 to continue appending data. */
	$endOfBlock='0';
	$params = array(
	       'blockName'=>$blockName,
		   'dataSourceID'=>$dsId,
	       'reportSuiteID'=>$rsId,
	       'columnNames'=>$colNameArray,
	       'rows'=>$rowData,
	       'endOfBlock'=>$endOfBlock
	);
	
	$result = sendRequest('DataSource.BeginDataBlock', $params );
	$blockID = $result['blockID'];
	var_dump($result);
	sleep(3);   
	
	/* DataSource.AppendDataBlock - sends data (continued) to Omniture using a DataSource */
	
	$rowData = array(
		array('01/11/2011','100300','455','003456'),
		array('01/12/2011','100301','455','003456')
	);
	
	/* Set the endOfBlock to ту to stop appending data. */
	
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
	sleep(3);   
	
}catch(Exception $e){
	echo $e . "\n";
}

/* Additional Data Sources Methods */
try{
   /* DataSource.Restart */
   /*
   $params = array(
       'dataSourceID'=>$dsId,
       'reportSuiteID'=>$rsId
   );
   $status = sendRequest('DataSource.Restart', $params );
   var_dump($status);
   */
   /* DataSource.Deactivate */
   /*
   $params = array(
       'dataSourceID'=>$dsId,
       'reportSuiteID'=>$rsId
   );
   $status = sendRequest('DataSource.Deactivate', $params );
   var_dump($status);
   */
}catch(Exception $e){
	echo $e . "\n";
}

?>