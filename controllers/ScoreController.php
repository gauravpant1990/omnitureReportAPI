<?php

/**
 * Created by PhpStorm.
 * User: gpant
 * Date: 2/2/2016
 * Time: 10:32 PM
 */
class ScoreController
{
    public static function getItem($item){
        $dbObject = new DbObject();
        $winnerArray = array("keyword"=>$item, "keywordScore"=>array());
        $finalWinnerArray = array("keyword"=>$item, "itemIdScore"=>array());
        $days = $dbObject->query('select days_in_words, days_in_number, multiplication_factor from available_days');
        $query="";
        foreach($days as $key=>$day){
            $days[$day['days_in_number']] = $days[$key];
            unset($days[$key]);
            $query .= 'select "'.$day["days_in_words"].'" as days, '.$day["days_in_number"].' as n_days,
                a.id, a.id_keyword,a.id_item_id,a.score from '.
                $day["days_in_words"].'_days_item_id_ae as a where a.id_item_id="'.$item.'" union ';
        }
        $query=substr($query,0,count($query)-7);
        $results = $dbObject->query($query);
        if(!is_null($results)) {
            foreach ($results as $result) {
                $score=$result['score']*$days[$result['n_days']]['multiplication_factor'];
                array_push($winnerArray["itemIdScore"], array("itemId" => $result["id_item_id"], "score" => $score));
            }
            $winnerArrayLength = count($winnerArray['itemIdScore']);
            $pointer=0;
            $matched=false;
            //while(isset($winnerArray['itemIdScore'][$pointer])){
            $itemcount=count($winnerArray['itemIdScore']);
            while($itemcount>$pointer){
                if(!isset($winnerArray['itemIdScore'][$pointer]['itemId'])){
                    $pointer++;
                    continue;
                }
                $finalScore=$winnerArray['itemIdScore'][$pointer]['score'];
                foreach($winnerArray['itemIdScore'] as $key=>$value){
                    if($key!=$pointer) {
                        //for($i=$pointer; $i<$winnerArrayLength; $i++){
                        if ($value['itemId'] == $winnerArray['itemIdScore'][$pointer]['itemId']) {
                            echo $value['itemId'] . " " . $winnerArray['itemIdScore'][$pointer]['itemId'] . '\n]';
                            $finalScore = $winnerArray['itemIdScore'][$key]['score'] + $finalScore;
                            //array_push($finalWinnerArray["itemIdScore"], array("itemId" => $value['itemId'], "score" => $finalScore));
                            $matched = array("itemId" => $value['itemId'], "score" => $finalScore);
                            unset($winnerArray['itemIdScore'][$key]);
                        }
                    }
                }
                if($matched===false){
                    array_push($finalWinnerArray["itemIdScore"], $matched);
                    $matched=false;
                }
                array_push($finalWinnerArray["itemIdScore"], array("itemId" => $winnerArray['itemIdScore'][$pointer]['itemId'], "score" => $finalScore));
                unset($winnerArray['itemIdScore'][$pointer]);
                $pointer++;
            }
        }
        var_dump($finalWinnerArray);return;
        return json_encode($winnerArray);
    }

    public static function getKeyword($keyword){
        //array_unique(array_merge($array1, $array2));
        $dbObject = new DbObject();
        $winnerArray = array("keyword"=>$keyword, "itemIdScore"=>array());
        $finalWinnerArray = array("keyword"=>$keyword, "itemIdScore"=>array());
        $days = $dbObject->query('select days_in_words, days_in_number, multiplication_factor from available_days');
        $query="";
        foreach($days as $key=>$day){
            $days[$day['days_in_number']] = $days[$key];
            unset($days[$key]);
            $query .= 'select "'.$day["days_in_words"].'" as days, '.$day["days_in_number"].' as n_days,
                a.id, a.id_keyword,a.id_item_id,a.score from '.
                $day["days_in_words"].'_days_item_id_ae as a where a.id_keyword="'.$keyword.'" union ';
        }
        $query=substr($query,0,count($query)-7);
        $results = $dbObject->query($query);
        if(!is_null($results)) {
            foreach ($results as $result) {
                $score=$result['score']*$days[$result['n_days']]['multiplication_factor'];
                array_push($winnerArray["itemIdScore"], array("itemId" => $result["id_item_id"], "score" => $score));
            }
            $winnerArrayLength = count($winnerArray['itemIdScore']);
            $pointer=0;
            $matched=false;
            //while(isset($winnerArray['itemIdScore'][$pointer])){
            $itemcount=count($winnerArray['itemIdScore']);
            while($itemcount>$pointer){
                if(!isset($winnerArray['itemIdScore'][$pointer]['itemId'])){
                    $pointer++;
                    continue;
                }
                $finalScore=$winnerArray['itemIdScore'][$pointer]['score'];
                foreach($winnerArray['itemIdScore'] as $key=>$value){
                    if($key!=$pointer) {
                        //for($i=$pointer; $i<$winnerArrayLength; $i++){
                        if ($value['itemId'] == $winnerArray['itemIdScore'][$pointer]['itemId']) {
                            echo $value['itemId'] . " " . $winnerArray['itemIdScore'][$pointer]['itemId'] . '\n]';
                            $finalScore = $winnerArray['itemIdScore'][$key]['score'] + $finalScore;
                            //array_push($finalWinnerArray["itemIdScore"], array("itemId" => $value['itemId'], "score" => $finalScore));
                            $matched = array("itemId" => $value['itemId'], "score" => $finalScore);
                            unset($winnerArray['itemIdScore'][$key]);
                        }
                    }
                }
                if($matched===false){
                    array_push($finalWinnerArray["itemIdScore"], $matched);
                    $matched=false;
                }
                array_push($finalWinnerArray["itemIdScore"], array("itemId" => $winnerArray['itemIdScore'][$pointer]['itemId'], "score" => $finalScore));
                unset($winnerArray['itemIdScore'][$pointer]);
                $pointer++;
            }
        }
        var_dump($finalWinnerArray);return;
        return json_encode($winnerArray);
    }

    public function getItemKeyword($item, $keyword){

    }
}