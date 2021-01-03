<?php 

class Pix
{   
	protected $db_kapcsolat;
	private $cim_en;
	private $cim_hu;
	private $dif;
	private $szel;
	private $hossz;
	private $kocka_x;
	private $kocka_y;
	private $pix;
	private $datum;
	private $oldal_akt = 1;  
	private $oldal_sor = 20;
	private $oldal_darab;
	//private $uzenet;
	private $id_user;
	private $id_pix;
	private $nyelv;
	private $hiba = array();
	
    public function __construct() 
	{
        $this->db_kapcsolat = Adatbazis::a_kapcsolat();
        $this->db_kapcsolat->init();
    } 
	
// *******************************************************************************
// SET_*
// *******************************************************************************

	//public function set_uzenet($uzenet) { $this->uzenet = $uzenet; }
	public function set_id_user($id) { $this->id_user = $id; }
	public function set_id_pix($id) { $this->id_pix = $id; }
	public function set_nyelv($nyelv) { $this->nyelv = $nyelv; }
	public function set_cim_hu($cim_hu) { $this->cim_hu = $cim_hu; }
	public function set_cim_en($cim_en) { $this->cim_en = $cim_en; }
	public function set_dif($dif) { $this->dif = $dif; }
	public function set_szel($szel) { $this->szel = $szel; }
	public function set_hossz($hossz) { $this->hossz = $hossz; }
	public function set_kocka_x($kocka_x) { $this->kocka_x = $kocka_x; }
	public function set_kocka_y($kocka_y) { $this->kocka_y = $kocka_y; }
	public function set_pix($pix) { $this->pix = $pix; }
	public function set_datum($datum) { $this->datum = $datum; }
	
	public function set_oldal_akt($oldal_akt) 
	{ 
		$this->db_kapcsolat->tabla_select('pix', 'id', '', '', '');	 
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
// GET_ *
// *******************************************************************************

	public function get_hiba() { return $this->hiba; }
	public function get_pix() { return $this->pix; }
	
// *******************************************************************************
// ELLENORIZ
// *******************************************************************************

	public function ellenoriz()
	{	
		$this->ell_szoveg($this->cim_en, 13);
		$this->ell_szoveg($this->cim_hu, 14);
		$this->ell_szam($this->dif, 1, 5, 15);
		$this->ell_szam($this->szel, 340, 1500, 16);
		$this->ell_szam($this->hossz, 250, 1200, 17);
		$this->ell_kocka($this->kocka_x, 5, 40, 18);
		$this->ell_kocka($this->kocka_y, 5, 40, 19);
		$this->ell_pix();
	}
	
// *******************************************************************************
// PIX_LISTA - Játék lista
// *******************************************************************************

	public function pix_lista()
	{  
		if (!$this->id_user) $this->id_user = 0;
		
		$tabla = 'pix AS a, user AS b';
		$mezo = 'a.id AS id_pic, a.id_user, a.cim_'.$this->nyelv.' AS cim, a.kocka_x, a.kocka_y, a.datum, b.nev, b.id, a.szel, a.hossz, a.dif, pix';
		$where = 'a.id_user = b.id';
		$order = 'datum DESC'; 
		$limit = '1';
		
		$limit = ($this->oldal_akt - 1) * $this->oldal_sor.', '.$this->oldal_sor;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, $order, $limit);		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();	
		 
		$i = 0;
		$megold = 0;
		
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
					$szinek = array_values($szinek); 
					sort($szinek);
					unset($szinek[0]);
					$szinek = array_values($szinek); 
					
					$tomb[$i]['color_db'] = count($szinek);
					$tomb[$i]['color'] = $szinek;
				}	
				
				if ($kulcs == 'id_pic') 
				{
					$this->id_pix = $ertek;
					$tomb[$i]['megold'] = $this->megoldottak();
					
					if ($this->id_user > 0) 
					{
						$tomb[$i]['kesz'] = $this->kesz();
						$tomb[$i]['mentve'] = $this->mentve();
					}
				}
			}
			
			$tomb[$i]['user_id'] = $this->id_user;
			
			$i++;
		}
		
		return $tomb;
	}

// *******************************************************************************
// PIX_ADAT - egy játék adatai
// *******************************************************************************

	public function pix_adat()
	{  
		$tabla = 'pix';
		$mezo = 'id, cim_hu, cim_en, kocka_x, kocka_y, datum, szel, hossz, dif, pix';
		$where = 'id = '.$this->id_pix;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();	
		
		$tomb[0]['datum'] = Utils::datum_forma($tomb[0]['datum']);
		
		$szinek = explode(",", $tomb[0]['pix']);
		$szinek = array_unique($szinek);
		$tomb[0]['color_db'] = count($szinek) - 1;
				
		return $tomb;
	}
	
// *******************************************************************************
// PIX_MODOSIT - egy játék nódosítása
// *******************************************************************************

	public function pix_modosit()
	{  
		$szinek = explode(",", $this->pix); 
		$szinek = array_unique($szinek); 
		$szinek = array_values($szinek); 
		sort($szinek);
		unset($szinek[0]);
		$szinek = array_values($szinek); 
	
		$tabla = 'pix';
		$mezo = 'cim_hu = "'.$this->cim_hu.'", cim_en = "'.$this->cim_en.'", kocka_x = '.$this->kocka_x.', kocka_y ='.$this->kocka_y.',
				 color = "'.$szinek.'", szel = '.$this->szel.', hossz = '.$this->hossz.', dif = '.$this->dif.', pix = "'.$this->pix.'"';
		$where = 'id = '.$this->id_pix;
			
		$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
		
	}
	
// *******************************************************************************
// PIX_UJ - új játék felvitele
// *******************************************************************************

	public function pix_uj()
	{ 
		$szinek = explode(",", $this->pix); 
		$szinek = array_unique($szinek); 
		$szinek = array_values($szinek); 
		sort($szinek);
		unset($szinek[0]);
		$szinek = array_values($szinek); 
		$szinek = implode(",", $szinek);
	
		$ev = date('Y');
		$ho = date('m');
		$nap = date('d');
		$datum = mktime(0, 0, 0, $ho, $nap, $ev);
		
		$tabla = 'pix';
		$mezo = 'cim_hu = "'.$this->cim_hu.'", cim_en = "'.$this->cim_en.'", kocka_x = '.$this->kocka_x.', kocka_y ='.$this->kocka_y.', id_user = 1, 
				 color = "'.$szinek.'", szel = '.$this->szel.', hossz = '.$this->hossz.', dif = '.$this->dif.', datum = "'. $datum.'", pix = "'.$this->pix.'"';
			
		$this->db_kapcsolat->tabla_insert($tabla, $mezo);	
		
	}
	
// *******************************************************************************
// PIX_TOROL - játék törlése
// *******************************************************************************

	public function pix_torol()
	{ 
		$tabla = 'pix';
		$where = 'id = '.$this->id_pix;
			
		$this->db_kapcsolat->tabla_delete($tabla, $where);	
		
	}
	
// *******************************************************************************
// KESZ - megoldotta mer a játékot a user
// *******************************************************************************

	private function kesz()
	{  
		$tabla = 'pix_play';
		$mezo  = 'id_user, id_pix';
		$where = 'id_user = '.$this->id_user.' AND id_pix = '.$this->id_pix;
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		$db = $this->db_kapcsolat->get_sor_darab();	
		
		if ($db > 0) return 1;
	}
	
// *******************************************************************************
// MENTVE - mentett játékok
// *******************************************************************************

	private function mentve()
	{  
		$tabla = 'pix_save';
		$mezo  = 'id_user, id_pix';
		$where = 'id_user = '.$this->id_user.' AND id_pix = '.$this->id_pix;
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		$db = $this->db_kapcsolat->get_sor_darab();	
		
		if ($db > 0) return 1;		
	}
	
// *******************************************************************************
// MEGOLDOTTÁK - hányan oldották már meg
// *******************************************************************************

	private function megoldottak()
	{  
		$tabla = 'pix_play';
		$mezo = 'datum';
		$where = 'id_pix ='.$this->id_pix.' AND id_user = 0';

		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		$tomb = $this->db_kapcsolat->get_select_eredmeny();	
		
		$megold = $tomb[0]['datum'];
		
		$tabla = 'pix_play';
		$mezo = 'datum';
		$where = 'id_pix ='.$this->id_pix.' AND id_user > 0';

		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		$darab = $this->db_kapcsolat->get_sor_darab();	
		
		$megold += $darab;
		
		return $megold;
	}
	
// *******************************************************************************
// DEL_SAVE_PIX - mentett játékok törlése
// *******************************************************************************

	public function del_save_pix()
	{
		$tabla = 'pix_save';
		$where = 'id_user ='.$this->id_user;

		$this->db_kapcsolat->tabla_delete($tabla, $where);	
	}
	
// *******************************************************************************
// DEL_READY_PIX - megfejtett játékok törlése
// *******************************************************************************

	public function del_ready_pix()
	{
		$tabla = 'pix_play';
		$where = 'id_user ='.$this->id_user;

		$this->db_kapcsolat->tabla_delete($tabla, $where);	
	}
	

// *******************************************************************************
// ELL_SZOVEG - szovegek ellenőrzése
// *******************************************************************************

	private function ell_szoveg($mit, $hiba)
	{
		global $lang_m;
		if ($mit == '') $this->hiba[] = $lang_m[$hiba]; 
	}
	
// *******************************************************************************
// ELL_SZAM - szám adatok ellenőrzése
// *******************************************************************************

	private function ell_szam($szam, $min, $max, $hiba)
	{
		global $lang_m;
		
		if ($szam < $min or $szam > $max) $this->hiba[] = $lang_m[$hiba]; 
	}

// *******************************************************************************
// ELL_KOCKA - kockák számának ellenőrzése
// *******************************************************************************

	private function ell_kocka($kocka, $min, $max, $hiba)
	{
		global $lang_m;
		
		if ($kocka < $min or $kocka > $max) 
		{
			$this->hiba[] = $lang_m[$hiba]; 
		}
		else
		{
			if ($kocka % 5 != 0) $this->hiba[] = $lang_m[$hiba]; 
		}
	}
	
// *******************************************************************************
// ELL_PIX - pixelek ellenőrzése
// *******************************************************************************

	private function ell_pix()
	{
		global $lang_m;
		
		$pix = explode(",", $this->pix);
		
		$db = count($pix);
		$db_sz = $this->kocka_x * $this->kocka_y;
		
		if ($db == $db_sz)
		{
			for ($i = 0; $i < $db; ++$i)
			{   

				$pix[$i] = (int) $pix[$i];
				if ($pix < 1) $pix = 0; // A duplikált 0-ák miatt;
				if ($pix[$i] > 17 or $pix[$i] < 0)  $this->hiba[] = $lang_m[20].($i + 1).'. helyen.'; 
			}		
			
			$this->pix = implode(',', $pix);
		}
		else
		{
			$this->hiba[] = $lang_m[21].$db.'/'.$db_sz;
		}
	}
	
}

?>
