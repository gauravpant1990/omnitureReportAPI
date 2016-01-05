<?php
require_once dirname(__FILE__).'/../bootstrap.php';
$dbobject = new DbObject();
var_dump($dbobject->truncate('three_days_sub_category_ae'));
var_dump($dbobject->truncate('three_days_brand_ae'));

