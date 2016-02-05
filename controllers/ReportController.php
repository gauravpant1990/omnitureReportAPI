<?php

/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/10/2015
 * Time: 4:26 PM
 */
class ReportController
{
    public $omnitureReport;
    public $availableDays=array();
    public $isValidReport=true;

    function __construct($numdays){
        $this->omnitureReport = new OmnitureReport();
        $dbObject = new DbObject();
        $days=$dbObject->select('available_days');
        foreach($days as $day){
            $this->availableDays[(int)$day['id']]=$day['days_in_words'];
        }
        if(!isset($this->availableDays[$numdays])) $this->isValidReport=false;
    }
    function runOmnitureReport($params, $type){
        return $this->omnitureReport->generateReport($params, $type);
    }

    function checkStatus($queue){
        return $this->omnitureReport->checkStatus($queue);
    }

    function runReport($queue){
        return $this->omnitureReport->runReport($queue);
    }

    function saveToDb($report, $table, $days){
        try {
            $dbObject = new DbObject();
            $dataArray = $report->data;
            foreach($report->metrics as $key=>$value){
                if($value->id == "orders"){$orderIndex = $key;}
                if($value->id == "visits"){$visitIndex = $key;}
                if($value->id == "event3"){$productViewIndex = $key;}
                if($value->id == "carts"){$cartIndex = $key;}
            }

            foreach ($dataArray as $data) {
                if (strpos($data->name, "unspecified") == false) {
                    $id_keyword = $data->name;//md5()
                    $metricesKeyword = $data->counts;
                    $keyword=array(
                        "id" => $id_keyword,
                        "value" => $data->name,
                        "orders" =>$metricesKeyword[$orderIndex],
                        "visits" => $metricesKeyword[$visitIndex],
                        "product_views" => $metricesKeyword[$productViewIndex],
                        "carts" => $metricesKeyword[$cartIndex]
                    );

                    if(count($dbObject->select('keyword',array('*'),'id="'.$id_keyword.'"'))==0){
                        $dbObject->insert(
                            "keyword",
                            $keyword
                        );
                    }else{
                        $dbObject->update(
                            "keyword",
                            $keyword,
                            "id='".$id_keyword."'"
                        );
                    }

                    if($table == "sub_category"){
                        $this->saveToSubCategory($data, $id_keyword, $orderIndex, $visitIndex, $productViewIndex, $cartIndex);
                    }elseif($table == "brand"){
                        $this->saveToBrand($data, $id_keyword, $orderIndex, $visitIndex, $productViewIndex, $cartIndex, $this->availableDays[$days]);
                    }elseif($table == "item_id"){
                        $this->saveToItemId($data,
                            $orderIndex, $visitIndex, $productViewIndex,
                            $cartIndex, $this->availableDays[$days],$keyword);
                    }
                    /*foreach ($data->breakdown as $subcategory) {
                        if (strpos($subcategory->name, "other") === false && strpos($subcategory->name, "Unspecified") === false) {
                            $id_subcategory = md5($subcategory->name);
                            $dbObject->insert("sub_category", array("id" => $id_subcategory, "value" => $subcategory->name));
                            //foreach($subcategory->counts as $metric){
                            $metrices = $subcategory->counts;
                            $dbObject->insert(
                                "three_days_sub_category_ae",
                                array("id" => md5($id_keyword . $id_subcategory),
                                    "id_sub_category" => $id_subcategory,
                                    "id_keyword" => $id_keyword,
                                    "orders" => $metrices[$orderIndex],
                                    "visits" => $metrices[$visitIndex],
                                    "product_views" => $metrices[$productViewIndex],
                                    "carts" => $metrices[$cartIndex]
                                )
                            );
                        }
                    }*/
                }
            }
            return true;
        }catch (Exception $e) {
            var_dump($e);
            return false;
        }
    }

    function saveToBrand($data, $id_keyword, $orderIndex, $visitIndex, $productViewIndex, $cartIndex, $days){
        $dbObject = new DbObject();
        foreach ($data->breakdown as $brand) {
            if (stripos($brand->name, "other") === false && stripos($brand->name, "Unspecified") === false && stripos($brand->name, "Others") === false) {
                $id_brand = $brand->name;//md5()
                $dbObject->insert("brand", array("id" => $id_brand, "value" => $brand->name));
                //foreach($subcategory->counts as $metric){
                $metrices = $brand->counts;
                $dbObject->insert(
                    $days."_days_brand_ae",
                    array("id" => md5($id_keyword . $id_brand),
                        "id_brand" => $id_brand,
                        "id_keyword" => $id_keyword,
                        "orders" => $metrices[$orderIndex],
                        "visits" => $metrices[$visitIndex],
                        "product_views" => $metrices[$productViewIndex],
                        "carts" => $metrices[$cartIndex]
                    )
                );
            }
        }
    }

    function saveToSubCategory($data, $id_keyword, $orderIndex, $visitIndex, $productViewIndex, $cartIndex){
        $dbObject = new DbObject();
        foreach ($data->breakdown as $subcategory) {
            if (stripos($subcategory->name, "other") === false && stripos($subcategory->name, "Unspecified") === false && stripos($subcategory->name, "Others") === false) {
                $id_sub_category = md5($subcategory->name);
                $dbObject->insert("sub_category", array("id" => $id_sub_category, "value" => $subcategory->name));
                //foreach($subcategory->counts as $metric){
                $metrices = $subcategory->counts;
                $dbObject->insert(
                    "three_days_sub_category_ae",
                    array("id" => md5($id_keyword . $id_sub_category),
                        "id_sub_category" => $id_sub_category,
                        "id_keyword" => $id_keyword,
                        "orders" => $metrices[$orderIndex],
                        "visits" => $metrices[$visitIndex],
                        "product_views" => $metrices[$productViewIndex],
                        "carts" => $metrices[$cartIndex]
                    )
                );
            }
        }
    }

    function saveToItemId($data, $orderIndex, $visitIndex, $productViewIndex, $cartIndex, $days, $keyword){
        $dbObject = new DbObject();
        foreach ($data->breakdown as $itemId) {
            if (stripos($itemId->name, "other") === false && stripos($itemId->name, "Unspecified") === false && stripos($itemId->name, "Others") === false) {
                $id_item_id = md5($itemId->name);
                $dbObject->insert("item_id", array("id" => $itemId->name));
                //foreach($itemId->counts as $metric){
                $metrices = $itemId->counts;
                $itemIdKeyword = array("id" => md5($keyword['id'] . $id_item_id),
                    "id_item_id" => $itemId->name,
                    "id_keyword" => $keyword['id'],
                    "orders" => $metrices[$orderIndex],
                    "visits" => $metrices[$visitIndex],
                    "product_views" => $metrices[$productViewIndex],
                    "carts" => $metrices[$cartIndex],

                );
                $score = Formula::getScore($keyword,$itemIdKeyword);
                if($score===false)continue;
                $dataarray = array_merge($itemIdKeyword,array('score'=>$score));
                $dbObject->insert(
                    $days."_days_item_id_ae",
                    $dataarray
                );
            }
        }
    }
}