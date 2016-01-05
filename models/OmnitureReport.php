<?php

/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/10/2015
 * Time: 1:02 PM
 */
class OmnitureReport
{
    public $requestClient;
    function __construct($params = null)
    {
        //$this->params = $params;
        $this->requestClient = new RequestManager();
    }

    function generateReport($params, $type)
    {
        $REPORT_STATUS_NOT_READY = 'not ready';
        $REPORT_STATUS_QUEUED = 'queued';
        $REPORT_STATUS_DONE = 'done';

        $max_report_checks = 500;
        $rsId = 'souqaeprod';

        /* The API method determining which type of report to queue. */
        /* There are 3 types of reports (Report.QueueOvertime, Report.QueueTrended, Report.QueueRanked)) */

        /* Queue the request */
        $response = $this->requestClient->reportOperation($params, 'Report.QueueRanked');
        $response = json_decode($response);
        /* Check for a valid request reponse */
        if ($response->status === $REPORT_STATUS_QUEUED) {
            /* Save the queue ID */
            $queue = $response->reportID;
            return $queue;
        } else {
            throw new Exception('rp_queueAndGetReport(): ' . $response['status'] . ' - ' . $response['statusMsg'], 0);
        }
    }

    function checkStatus($queue)
    {
        $attempts = 0;
        $nextSleepSeconds = 0;
        $max_checks = 20;
        $check_wait_seconds = 2;
        $reportDone = false;
        while ($nextSleepSeconds = $this->nextSleep($attempts, $max_checks, $check_wait_seconds)) {
            if (true) echo("sleeping for... $nextSleepSeconds seconds.\n");
            sleep($nextSleepSeconds);

            if (true) echo("Checking on queue: $queue (" . date("H:i:s") . ")\n");

            /* Check the status */
            $status = $this->rp_getStatus($queue);
            /* Check if the report is ready */
            if (true) echo("Request Status: " . $status . "\n");
            if ($status == 'done') {
                /* Report is ready */
                $reportDone = true;
                break;

            } else if (strstr($status, 'fail') || strstr($status, 'error')) {
                /* Report failed, exit out */
                //throw new Exception("rp_queueAndGetReport(): " . $status);//. " - ". $response['error_msg'], 0);
                return false;
            } else {
                /* Report not ready yet */
                if (true) echo $status . "\n";
            }
            $attempts++;
        }
        return $reportDone;
    }

    /**
     *  Does a token free status check on a single report
     *  @returns string status
     */
    function rp_getStatus($reportID){
        $response = $this->requestClient->reportOperation(array('reportID' => $reportID), 'Report.GetStatus');
        $response = json_decode($response);
        return $response->status;
    }

    /**
     * Determines next sleep time for report queue checking.
     * Uses a backing off algorithm so that long requests don't have to check as often.

     *
     * @param    $attempts     int    The number of checks so far
     * @param    $max_checks   int User specified maximum number of checks
     *
     * @return    FALSE to stop checking OR the number of seconds for the next sleep
     */
    function nextSleep( $attempts, $max_checks, $check_wait_seconds ){
        global $max_report_checks;
        if( $max_checks <= 0){
            if($attempts >= $max_report_checks)
                return -1;
            if($attempts <= 4 ){
                return 3;
            }else{
                return min( ($attempts - 4) * 3, 30);
            }
        }else{
            if($attempts < $max_checks)
                return $check_wait_seconds;
        }
        return FALSE;
    }

    function runReport($queue)
    {
        $report = $this->rp_getReport($queue);
        if($report!==false) {
            $record_count = count($report->data);
            /* Print the report */
            if ($record_count > 0) {
                return $report;
            }
        }
        return false;
    }
    /**
     *  Should always have used rp_getStatus previous to calling this method
     *  @returns mixed report object
     */
    function rp_getReport($reportID){
        global $REPORT_STATUS_DONE;
        $response = json_decode($this->requestClient->reportOperation(array('reportID' => $reportID),'Report.GetReport'));
        if(isset($response->status)) {
            if ($response->status == 'done') {
                return $response->report;
            } else
                throw new Exception("rp_getReport(): " . $response->status . " - " . $response->statusMsg, 0);
        }else return false;
    }
}