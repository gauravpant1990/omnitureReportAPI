<?php
/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/16/2015
 * Time: 4:46 PM
 */
require_once dirname(__FILE__).'/../bootstrap.php';
$date = date('Y-m-d');

$is_successfull = false;
$isDone = false;
$whilecount = 0;
$queue = false;

$reportController = new ReportController($argv[2]);
if($reportController->isValidReport==false){
    echo "The table for correspoinding days number is not available\n";
    return;
}

$params = array('reportDescription' => array(
    'reportSuiteID' => 'souqaeprod',
    'date' => $date, // the date of the report, format YYYY-MM-DD or YYYY-MM or YYYY
    'dateFrom' => date('Y-m-d', strtotime($date .' -'.$argv[2].' day')), // the start date of the report range, format YYYY-MM-DD
    'dateTo' => date('Y-m-d', strtotime($date .' -1 day')), //  the end date of the report range, format YYYY-MM-DD
    'metrics' => array(
        array('id' => 'event1'),
        array('id' => 'orders'),
        array('id' => 'visits'),
        array('id' => 'event3'),
        array('id' => 'carts')
    ), // metrics
    'elements' => array(
        array(
            'id' => 'evar33',
            'classification' => '',
            'top' => '20',
            'startingWith' => 20*$argv[1]+1,
        ),
        array(
            'id' => 'product',
            'classification' => 'product brand',
            'top' => '6'
        )
    ), // elements, elements don't apply to Overtime reports
    'sortBy' => 'event1', // sortBy
    'validate' => true,
    /*'locale' => 'en_US'*/
    //'dateGranularity' => 'day'// (day, hour, etc.)
)
);
while ($queue === false) {
    $queue = $reportController->runOmnitureReport($params, 'Report.QueueRanked');
    if ($queue !== false) {
        while (!($isDone || $whilecount == 10)) {
            $isDone = $reportController->checkStatus($queue);
            if ($isDone) {
                $report = $reportController->runReport($queue);
                if ($report !== false) {
                    while (!$is_successfull) {
                        $is_successfull = $reportController->saveToDb($report, "brand", $argv[2]);
                    }
                }
            } else $isDone = false;
            $whilecount++;
        }
    }
}