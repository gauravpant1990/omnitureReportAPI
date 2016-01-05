<?php

/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 12/15/2015
 * Time: 6:54 PM
 */
class CategoryScoreController
{
    static function getScore($keyword, $suite = "ae")
    {
        $orderScore=$visitScore=$productViewsScore=$cartsScore =0;
        $dbObject = new DbObject();
        $winnerArray = array("keyword"=>$keyword, "categoryScore"=>array());//array("subCategory"=>"Dress", "score"=>4.3), array("subCategory"=>"Top", "score"=>3.2)
        $query = 'select k.value, sc.value, tdscae.orders, tdscae.visits, tdscae.product_views, tdscae.carts from three_days_sub_category_ae as tdscae
        left join sub_category as sc on sc.id = tdscae.id_sub_category
        left join keyword as k on k.id = tdscae.id_keyword
        where k.value="'.$keyword.'"';
        $results = $dbObject->query($query);
        $keywordResult = $dbObject->query('select * from keyword where value="'.$keyword.'"');
        //var_dump($keywordResult);
        foreach($results as $result){
            if($keywordResult[0]['orders']!=0)
                $orderScore = $result['orders']/$keywordResult[0]['orders'];
            if($keywordResult[0]['visits']!=0)
                $visitScore = $result['visits']/$keywordResult[0]['visits'];
            if($keywordResult[0]['product_views']!=0)
                $productViewsScore = $result['product_views']/$keywordResult[0]['product_views'];
            if($keywordResult[0]['carts']!=0)
                $cartsScore = $result['carts']/$keywordResult[0]['carts'];
            //var_dump($orderScore, $visitScore, $productViewsScore, $cartsScore);echo "now powers";
            if($visitScore>0.01){
                $score = $orderScore+$visitScore+$productViewsScore+$cartsScore;
                array_push($winnerArray["categoryScore"],array("subCategory"=>$result["value"], "score"=>$score));
            }
        }
        return json_encode($winnerArray);
    }

    static function getBrandScore($keyword){
        $orderScore=$visitScore=$productViewsScore=$cartsScore =0;
        $dbObject = new DbObject();
        $winnerArray = array("keyword"=>$keyword, "brandScore"=>array());//array("subCategory"=>"Dress", "score"=>4.3), array("subCategory"=>"Top", "score"=>3.2)
        $query = 'select k.value, b.value, tdbae.orders, tdbae.visits, tdbae.product_views, tdbae.carts from three_days_brand_ae as tdbae
        left join brand as b on b.id = tdbae.id_brand
        left join keyword as k on k.id = tdbae.id_keyword
        where k.value="'.$keyword.'"';
        $results = $dbObject->query($query);
        $keywordResult = $dbObject->query('select * from keyword where value="'.$keyword.'"');
        if(!is_null($results)) {
            foreach ($results as $result) {
                if ($keywordResult[0]['orders'] != 0)
                    $orderScore = $result['orders'] / $keywordResult[0]['orders'];
                if ($keywordResult[0]['visits'] != 0)
                    $visitScore = $result['visits'] / $keywordResult[0]['visits'];
                if ($keywordResult[0]['product_views'] != 0)
                    $productViewsScore = $result['product_views'] / $keywordResult[0]['product_views'];
                if ($keywordResult[0]['carts'] != 0)
                    $cartsScore = $result['carts'] / $keywordResult[0]['carts'];
                //var_dump($orderScore, $visitScore, $productViewsScore, $cartsScore);echo "now powers";
                if ($visitScore > 0.01) {
                    $score = $orderScore + $visitScore + $productViewsScore + $cartsScore;
                    array_push($winnerArray["brandScore"], array("brand" => $result["value"], "score" => $score));
                }
            }
        }
        return json_encode($winnerArray);
    }
}