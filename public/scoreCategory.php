<?php
/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/16/2015
 * Time: 4:35 PM
 */
require_once dirname(__FILE__).'/../bootstrap.php';
echo "<pre>";
$keyword = $_GET['q'];
//echo CategoryScoreController::getScore($keyword);
echo CategoryScoreController::getBrandScore($keyword);