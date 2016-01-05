<?php 
class CsvTranslator extends DbObject
{
	private $filePath;
	private $table;
	public function __construct($filePath, $model)
	{
		parent::__construct();
		$this->filePath = $filePath;
		$this->model = $model;
	}
	public function csv_to_db()
	{
		$row = 1;
		if (($handle = fopen($this->filePath, "r")) !== FALSE) 
		{	
			$headers = array();
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) 
			{
				if($row==1 || $row==2 || $data[0]=="Total")
				{
					if($row==2)
					{
						for ($i=0; $i < count($data); $i++)
						{
							$data[$i] = str_replace(" ","-",$data[$i]);
							$headers[$i] = strtolower($data[$i]);
						}
					}
					$row++;
					continue;
				}
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				$attributes = array_combine($headers, $data);
				if($this->model->save($attributes, false)!==true)
				{
					//echo "<br>".$this->insert($this->model->table,$attributes, true);
					break;
				}
			}
			fclose($handle);
		}
	}
}