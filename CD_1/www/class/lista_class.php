<?php 

class Lista
{   
	private $nyelv;
 	private $oldal_akt = 1;  
	private $oldal_sor;
	private $oldal_darab;
	
	protected $db_kapcsolat;
	
    public function __construct()
    {
        $this->db_kapcsolat = Adatbazis::a_kapcsolat();
        $this->db_kapcsolat->init();
    }
	
// *******************************************************************************
// SET_*
// *******************************************************************************

	public function set_nyelv($nyelv) { $this->nyelv = $nyelv; }
	public function set_oldal_sor($db) { $this->oldal_sor = $db; }
	
	public function set_oldal_akt($table, $oldal_akt) 
	{ 
		$this->db_kapcsolat->tabla_select($table.'_'.$this->nyelv, 'id', 'flag = 1', '', '');	
		$oldal_darab = round($this->db_kapcsolat->get_sor_darab() / $this->oldal_sor);
		$this->oldal_darab = $oldal_darab;

		if ($oldal_akt > $this->oldal_darab) $oldal_akt = $this->oldal_darab;
		if ($oldal_akt == 0) $oldal_akt = 1;
		$this->oldal_akt = $oldal_akt;
		
		global $smarty;
		
		for ($i = 1; $i <= $oldal_darab; $i++) { $oldal_db[$i-1] = $i; }
		$smarty->assign("oldal_db", $oldal_db);
		$smarty->assign("oldal_akt", $this->oldal_akt);
	}
	
// *******************************************************************************
// LISTA_USER - felhasználók kiírása
// *******************************************************************************

	public function lista_user($where, $order)
	{ 
		$tabla = 'user';
		$mezo = 'id, flag, email, nev, ip, dat_reg, dat_belep';
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, 0);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();		
		
		$i = 0;

		foreach ($tomb as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			
			foreach ($t as $kulcs => $ertek) 
			{
				if ($kulcs == 'flag') 
				{
					switch ($ertek)
					{
						case '0':
							$tomb[$i]['flag_n'] = 'Nem aktivált';
							break;
						case '1':     
							$tomb[$i]['flag_n'] = 'Aktiv';
							break;
						case '2':
							$tomb[$i]['flag_n'] = 'Admin';
							break;
						case '3':     
							$tomb[$i]['flag_n'] = 'Letiltott';
							break;
					}
				}
				
				if ($kulcs == 'dat_reg') $tomb[$i]['dat_reg'] = Utils::datum_forma_hu($ertek);
				if ($kulcs == 'dat_belep') $tomb[$i]['dat_belep'] = Utils::datum_forma_hu($ertek);
				
				if ($i % 2 == 0) $tomb[$i]['sor'] = 1; else $tomb[$i]['sor'] = 2;
			}
			
			$i++;
		}
		
		return $tomb;
	}

// *******************************************************************************
// LISTA_USER_FOTO - fotók listázása
// *******************************************************************************

	public function lista_user_foto($where, $order)
	{ 
		$tabla = 'user';
		$mezo = 'id, email, nev, foto';
		
		if ($where == '') $where = 'foto = 1'; else $where .= ' AND foto = 1';
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, 0);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();		

		$i = 0;

		foreach ($tomb as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			
			foreach ($t as $kulcs => $ertek) 
			{	
				if ($i % 2 == 0) $tomb[$i]['sor'] = 1; 
			}
			
			$i++;
		}
		
		return $tomb;
	}

// *******************************************************************************
// LISTA_FORUM - fórum kiírása
// *******************************************************************************

	public function lista_forum()
	{ 
		$tabla = 'forum_'.$this->nyelv.' AS a, user AS b';
		$mezo = 'a.id AS id_forum, uzenet, datum, b.id AS id_user, b.flag AS user_flag';
		$where = 'a.flag = 1 AND a.id_user = b.id';
		$order = 'datum DESC';
		$limit = ($this->oldal_akt - 1) * $this->oldal_sor.', '.$this->oldal_sor;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, $limit);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();		
		
		$i = 0;

		foreach ($tomb as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			
			foreach ($t as $kulcs => $ertek) 
			{
				if ($kulcs == 'datum') $tomb[$i]['datum'] = Utils::datum_forma_ido($ertek);
				if ($kulcs == 'uzenet') $tomb[$i]['uzenet'] = nl2br($ertek);
				
				if ($kulcs == 'id_user')
				{
					$where = 'id = '.$ertek;
					$this->db_kapcsolat->tabla_select('user', 'nev, foto', $where, '', '');		
					$db = $this->db_kapcsolat->get_sor_darab();
				
					$sor = $this->db_kapcsolat->get_select_eredmeny();		
					$tomb[$i]['nev'] = $sor[0]['nev'];
					
					if ($sor[0]['foto'] == 0)
					{
						$tomb[$i]['foto'] = 0;
					}
					else
					{
						$tomb[$i]['foto'] = $ertek;
					}
				}
			}
			
			$i++;
		}
		
		return $tomb;
	}

// *******************************************************************************
// LISTA_FORUM_ADMIN - fórum kiírása
// *******************************************************************************

	public function lista_forum_admin($where_sql, $order_sql)
	{ 
		$mezo = 'a.flag AS forum_flag, a.id AS id_forum, uzenet, datum, nev, b.id AS id_user';
		$tabla = 'forum_'.$this->nyelv.' AS a, user AS b';
		
		if ($where_sql != '')
			$where = 'a.id_user = b.id AND '.$where_sql;
		else
			$where = 'a.id_user = b.id';
		
		if ($order_sql == '')
			$order = 'datum DESC';
		else
			$order = $order_sql;
		
		//$limit = ($this->oldal_akt - 1) * $this->oldal_sor.', '.$this->oldal_sor;
		$limit = 0;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, $limit);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();		
		
		$i = 0;

		foreach ($tomb as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			
			foreach ($t as $kulcs => $ertek) 
			{
				if ($kulcs == 'datum') $tomb[$i]['datum'] = Utils::datum_forma_ido($ertek);
				if ($kulcs == 'uzenet') $tomb[$i]['uzenet'] = nl2br($ertek);
			}
			
			$i++;
		}
		
		return $tomb;
	}

// *******************************************************************************
// LISTA_PIX - Játék lista
// *******************************************************************************

	public function lista_pix($where_sql)
	{  
		$tabla = 'pix AS a, user AS b';
		$mezo = 'a.id AS id_pic, a.id_user, a.cim_hu AS cim_hu, a.cim_en AS cim_en, a.kocka_x, a.kocka_y, szel, hossz, a.datum, b.nev, b.id, a.szel, a.hossz, a.dif, pix';
		$where = 'a.id_user = b.id';
		$order = 'datum DESC'; 
		
		if ($where_sql != '')
			$where = 'a.id_user = b.id AND '.$where_sql;
		else
			$where = 'a.id_user = b.id';
			
		//$limit = ($this->oldal_akt - 1) * $this->oldal_sor.', '.$this->oldal_sor;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, $limit);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();	
		 
		$i = 0;
		
		foreach ($tomb as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			
			foreach ($t as $kulcs => $ertek) 
			{
				if ($kulcs == 'datum') $tomb[$i]['datum'] = Utils::datum_forma($ertek);
				
				if ($kulcs == 'pix') 
				{  
					$szinek = explode(",", $ertek);
					$szinek = array_unique($szinek);
					$tomb[$i]['color_db'] = count($szinek) - 1;
				}	
				
				if ($i % 2 == 0) $tomb[$i]['sor'] = 1; else $tomb[$i]['sor'] = 2;
			}
			
			$i++;
		}
		
		return $tomb;
	}

}
?>