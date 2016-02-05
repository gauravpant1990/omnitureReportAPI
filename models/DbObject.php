<?php
class DbObject extends Db
{
	public function select($table, $arrayAttributes = array(), $whereString = 1, $groupby=null)
	{
		if(count($arrayAttributes)==0)
		{
			$attributes = "*";
		}
		else{
			$attributes = implode($arrayAttributes,',');
		}
		$query = "select ".$attributes." from ".$table." where ".$whereString;
		if(!is_null($groupby)) $query = $query." group by ".$groupby;
		$query=$query.";";
		return $this->query($query);
	}
	public function update($table, $arrayAttributes, $where = 1)
	{
		$keyvalue = "";
		foreach($arrayAttributes as $key=>$attribute)
		{
			$keyvalue .="`".$key."`='".$attribute."',"; 
		}
		$keyvalue = rtrim($keyvalue, ",");
		$query = "update ".$table." set ".$keyvalue." where ".$where;
		$query=$query.";";
		return $this->query($query);
	}
	public function delete($table, $where =1)
	{
		$query = 'delete from '.$table.' where '. $where;
		return $this->query($query);
	}
	public function insert($table, $arrayAttributes, $echo = false)
	{
		$columns = array();
		$values = array();
		foreach($arrayAttributes as $key=>$attribute)
		{
			array_push($columns, $key);
			array_push($values, $attribute);
		}
		$columns = (count($arrayAttributes)!=1)? implode($columns, "`,`"):$columns[0];
		$values = (count($arrayAttributes)!=1)? implode($values, "','"):$values[0];
		$query = "insert into ".$table." (`".$columns."`) values ('".$values."')";
		if($echo)	echo "<br>".$query;
		return $this->query($query);
	}

	public function query($sql)
	{
		$result = $this->conn->query($sql);
		if ($result === TRUE) {
			return true;
		} 
		elseif(!is_null($result) ){
			if(!empty($result->num_rows)){
				$arr = array();
				while ($row = $result->fetch_assoc()) {
					array_push($arr, $row);
				}
				$result->free();
				return $arr;//$result->fetch_array();
			}
		}
		else {
			return $this->conn->error;
		}
	}

	public function truncate($table){
		$query = 'truncate '.$table;
		return $this->query($query);
	}
}