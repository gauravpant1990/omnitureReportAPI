<?php

/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 2/2/2016
 * Time: 4:11 PM
 */
class Formula
{
    static function getScore($keyword, $itemIdKeyword){
        $orderScore=0;
        $visitScore=0;
        $productViewsScore=0;
        $cartsScore=0;
        if ($keyword['orders'] != 0)
            $orderScore = $itemIdKeyword['orders'] / $keyword['orders'];
        if ($keyword['visits'] != 0)
            $visitScore = $itemIdKeyword['visits'] / $keyword['visits'];
        if ($keyword['product_views'] != 0)
            $productViewsScore = $itemIdKeyword['product_views'] / $keyword['product_views'];
        if ($keyword['carts'] != 0)
            $cartsScore = $itemIdKeyword['carts'] / $keyword['carts'];

        if($visitScore<0.01)return false;
        if($itemIdKeyword['visits']<5){
            if($itemIdKeyword['orders']==0)return false;
        }
        return $orderScore + $visitScore + $productViewsScore + $cartsScore;
    }
    static function getCombinedScore(){

    }
}