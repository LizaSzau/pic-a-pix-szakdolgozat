<?php

class Adatbazis
{ 	
	private $select_eredmeny = array(); 
	private $sor_darab;
	private $last_id;
	
// *******************************************************************************
// A_KAPCSOLAT - kapcsolat létrehozása
// *******************************************************************************

    public static function a_kapcsolat()
    {  
        static $adatbazis;
        if (!is_object($adatbazis))
        {
            $adatbazis = new Adatbazis();
        } 
        return $adatbazis;
    }
    
    public function init()
    {
        mysql_connect(Constants::$DB_HOST, Constants::$DB_USER, Constants::$DB_PASSWORD) OR die("Adatbázishiba");
        mysql_select_db(Constants::$DB_NAME) OR die("Adatbázishiba");
		
		mysql_query("SET NAMES 'utf8'");
		mysql_query("SET CHARACTER SET 'utf8'");
		mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
		mysql_query("SET character_set_results = 'utf8'");
		mysql_query("SET character_set_server = 'utf8'");
		mysql_query("SET character_set_client = 'utf8'");
    }

// *******************************************************************************
// GET_
// *******************************************************************************
	
	public function get_select_eredmeny() { return $this->select_eredmeny; }
	public function get_sor_darab() { return $this->sor_darab; }
	public function get_last_id() { return $this->last_id; }
	
// *******************************************************************************
// TABLA_SELECT - leválogatások
// *******************************************************************************
	
	public function tabla_select($tabla, $mezo, $where, $order, $limit)
    {	
        $sql = 'SELECT '.$mezo.' FROM '.$tabla;
	
        if($where != null) $sql .= ' WHERE '.$where;
        if($order != null) $sql .= ' ORDER BY '.$order;
        if($limit != null) $sql .= ' LIMIT '.$limit;
		
		// echo "<p>$sql<p>";

        $vissza = mysql_query($sql) or die($this->adatbazis_hiba($sql));
	
		$this->select_eredmeny = array();
		
        if($vissza)
        {
            $db = mysql_num_rows($vissza);
			$this->sor_darab = $db;
			
            for($i = 0; $i < $db; $i++)
            { 
                $sor = mysql_fetch_array($vissza);
                $mezo = array_keys($sor); 
				
                for($j = 0; $j < count($mezo); $j++)
                {
                    if(!is_int($mezo[$j]))
                    {
                        if(mysql_num_rows($vissza) > 0)
						{
                            $this->select_eredmeny[$i][$mezo[$j]] = $sor[$mezo[$j]];
                        }
						else
                        {
							$this->select_eredmeny = null; 
                        }
                    }
                }
            }            
        }
    }

// *******************************************************************************
// TABLA_INSERT - insert
// *******************************************************************************

	public function tabla_insert($tabla, $mezo)
    {
		$sql = 'INSERT INTO '.$tabla.' SET '.$mezo; 
		
		// echo "<p>$sql<p>";
		
		mySQL_query($sql) or die($this->adatbazis_hiba($sql));
		$this->last_id = mySQL_insert_id();
    }

// *******************************************************************************
// TABLA_UPDATE - update
// *******************************************************************************

	public function tabla_update($tabla, $mezo, $where)
    {
		$sql = 'UPDATE '.$tabla.' SET '.$mezo; 
		if ($where) $sql .= ' WHERE '.$where;

		 echo "<p>$sql<p>";
		
		mySQL_query($sql) or die($this->adatbazis_hiba($sql));
    }
	
// *******************************************************************************
// TABLA_DELETE - delete
// *******************************************************************************

	public function tabla_delete($tabla, $where)
    {
		$sql = 'DELETE FROM '.$tabla.' WHERE '.$where;

		// echo "<p>$sql<p>";
		
		mySQL_query($sql) or die($this->adatbazis_hiba($sql));
    }
	
// *******************************************************************************
// ADATBAZIS_HIBA - hibák naplózása
// *******************************************************************************

	public function adatbazis_hiba($sql)
	{ 
		$ip = $_SERVER['REMOTE_ADDR'];
		$referer = $_SERVER['HTTP_REFERER'];
		$datum = time();
		$sql = mysql_escape_string($sql);
		
		$sql = 'INSERT INTO hiba  SET 
				hiba = "'.$sql.'", 
				ip = "'.$ip.'", 
				referer = "'.$referer.'", 
				datum = "'.$datum.'"';
		
		mySQL_query($sql) or die();
		
		// echo "<meta http-equiv='refresh' content ='0; url=?'>";
	}
}

?>
