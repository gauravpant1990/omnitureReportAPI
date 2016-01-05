<?php
require_once dirname(__FILE__) . '/checkStatus_junk.php';
/* Check if the report completed or timed-out */
if(!$reportDone)
{
    throw new Exception("runReport(): Report response timeout.", 1);
} else {    
    $report=rp_getReport($queue, $requestclient);
    $record_count = count($report->data);
   	 
    /* Print the report */
    if($record_count > 0)
    {
		echo "\n". "Report Suite: JJEsquire Getting Started Suite" . "\n";
		echo "Report ID: ". $queue ."\n";
		echo "Report record count: ". $record_count ."\n";
		echo "<pre>Report data:\n";
		var_dump($report);exit;
		printReport($report);
    }
}

/**
 *  Should always have used rp_getStatus previous to calling this method
 *  @returns mixed report object
 */                   
function rp_getReport($reportID, RequestManager $requestclient){
    global $REPORT_STATUS_DONE;
    $response = json_decode($requestclient->getReport(array('reportID' => $reportID)));
    if($response->status == 'done'){
   	 return $response->report;
    }else
   	 throw new Exception("rp_getReport(): " . $response['status'] . " - ". $response['statusMsg'], 0);
}

/* Print the report  */   
function printReport($report) {
    foreach($report->data as $records => $cols){
   	 echo $cols['name'];
   	 //if($cols['url'])
   	 $date = '';
   	 if(array_key_exists('year',$cols)) $date .= $cols['year'];
   	 if(array_key_exists('month',$cols)) $date .= '-' . $cols['month'];
   	 if(array_key_exists('day',$cols)) $date .= '-' . $cols['day'];
   	 if(array_key_exists('hour',$cols)) $date .= ' ' . $cols['hour'] . ':00';
   	 if($date) echo ' [' . $date . ']';
   	 echo " =>";
   	 foreach($cols['counts'] as $c)
   		 echo "\t" . $c;
   	 echo "\n";
   	 
   	 if(array_key_exists('breakdown',$cols)){
   		 foreach($cols['breakdown'] as $brRow){
   			 echo "\t" . $brRow['name'];
   			 //if($cols['url'])
   			 $date = '';
   			 if(array_key_exists('year',$brRow)) $date .= $brRow['year'];
   			 if(array_key_exists('month',$brRow)) $date .= '-' . $brRow['month'];
   			 if(array_key_exists('day',$brRow)) $date .= '-' . $brRow['day'];
   			 if(array_key_exists('hour',$brRow)) $date .= ' ' . $brRow['hour'] . ':00';
   			 if($date) echo ' [' . $date . ']';
   			 echo " =>";
   			 foreach($brRow['counts'] as $c)
   				 echo "\t" . $c;
   			 echo "\n";
   		 }
   	 }
    }
}