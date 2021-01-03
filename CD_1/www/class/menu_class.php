<?php 

class Menu
{   
	private $menu = array();
	private $menu_cim = array();
	private $modul_akt;
	
    public function __construct() 
	{
		$this->set_menu();
		//$this->set_menu_cim();
    } 
	
// *******************************************************************************
// SET_* public
// *******************************************************************************
	
	public function set_akt_menu($muv) 
	{
		if ($muv == 0 || $akt > count($this->menu)) $muv = 1;
		$this->modul_akt = $muv; 
	}
	
	public function set_menu_cim_list($nyelv) 
	{ 
		
		$json_i = file_get_contents('lang/menu_'.$nyelv.'.json');
		$json_i = json_decode($json_i, true); 

		$this->menu_cim = $json_i; 
		
		for ($i = 1; $i <= 6; $i++)  $this->menu[$i][2]  = $this->menu_cim['m_'.$i]['menu_cim'];
	}
	
// *******************************************************************************
// GET_*
// *******************************************************************************

	public function get_modul_akt() { return $this->menu[$this->modul_akt][1]; }
	public function get_modul_cim() { return $this->menu[$this->modul_akt][2]; }
	
// *******************************************************************************
// SET_MENU - főmenühöz tartozó könyvtárak
// *******************************************************************************

	private function set_menu()
	{
		$this->menu[1][1]  = 'pix';
		$this->menu[2][1]  = 'user';
		$this->menu[3][1]  = 'draw';
		$this->menu[4][1]  = 'forum';
		$this->menu[5][1]  = 'help';
		$this->menu[6][1]  = 'contact';
	}
}

?>
