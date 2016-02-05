<?php
/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/16/2015
 * Time: 4:35 PM
 */
require_once dirname(__FILE__).'/../bootstrap.php';

if(isset($_GET['keyword'])){
    $keyword=$_GET['keyword'];
    echo "<pre>".ScoreController::getKeyword($keyword);
}else{
    $keyword = $argv[1];
    echo ScoreController::getKeyword($keyword);
}

//echo CategoryScoreController::getScore($keyword);
