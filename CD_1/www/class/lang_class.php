<?php 

class Lang
{   
	private $nyelv;
	private $modul_akt;
	private $text_m = array();
	private $text_i = array();
	private $admin_mod;
	
    public function __construct() 
	{

    } 
		
// *******************************************************************************
// GET_*
// *******************************************************************************

	public function get_text_m() 
	{ 
		$this->text_m();
		return $this->text_m; 
	}
	
	public function get_text_i() 
	{ 
		$this->text_i();
		return $this->text_i; 
	}
	
// *******************************************************************************
// SET_* public
// *******************************************************************************

	public function set_nyelv($nyelv) { $this->nyelv = $nyelv; }
	public function set_modul_akt($modul_akt) { $this->modul_akt = $modul_akt; }
	public function set_admin_mod() { $this->admin_mod = 1; }
	
// *******************************************************************************
// TEXT - a modulok fordításai
// *******************************************************************************

	private function text_m()
	{
		if ($this->admin_mod == 1)
			$json = file_get_contents('../lang/'.$this->modul_akt.'_'.$this->nyelv.'.json');
		else
			$json = file_get_contents('lang/'.$this->modul_akt.'_'.$this->nyelv.'.json');
		
		$json = json_decode($json, true); 

		$i = 1;

		foreach ($json as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			foreach ($t as $kulcs => $ertek) { $this->text_m[$i] = $ertek; }
			$i++;
		}
	}

// *******************************************************************************
// TEXT_I - index oldal fordítása
// *******************************************************************************

	private function text_i()
	{
		if ($this->admin_mod == 1)
			$json = file_get_contents('../lang/index_'.$this->nyelv.'.json');
		else
			$json = file_get_contents('lang/index_'.$this->nyelv.'.json');
			
		$json = json_decode($json, true); 
		
		$i = 1;
		
		foreach ($json as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			foreach ($t as $kulcs => $ertek) { $this->text_i[$i] = $ertek; }
			$i++;
		}
	}
}

?>
