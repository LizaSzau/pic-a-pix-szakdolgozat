<?php 

class Menu
{   
	private $menu = array();
	private $menu_cim = array();
	private $almenu_cim = array();
	private $modul_akt;
	
    public function __construct() 
	{
		$this->set_menu();
		//$this->set_menu_cim();
    } 
		
// *******************************************************************************
// GET_*
// *******************************************************************************

	public function get_modul_akt() { return $this->menu[$this->modul_akt][1]; }
	public function get_modul_cim() { return $this->menu[$this->modul_akt][2]; }
	public function get_almenu_cim($m) { return $this->almenu_cim[$m]; }
	
// *******************************************************************************
// SET_* public
// *******************************************************************************
	
	public function set_akt_menu($muv) 
	{
		if ($muv == 0 || $akt > count($this->menu)) $muv = 1;
		$this->modul_akt = $muv; 
	}
	
	public function set_menu_cim_list() 
	{ 
		$json_i = file_get_contents('lang/menu.json');
		$json_i = json_decode($json_i, true); 

		$this->menu_cim = $json_i; 
		$this->set_menu_cim();
	}

	public function set_almenu_cim_list()
	{
		$json = file_get_contents('lang/almenu.json');
		$json = json_decode($json, true); 
		
		$this->almenu_cim = $json; 
		$this->set_almenu_cim();
	}
	
// *******************************************************************************
// SET_MENU - főmenühöz tartozó könyvtárak
// *******************************************************************************

	private function set_menu()
	{
		$this->menu[1][1]  = 'user';
		$this->menu[3][1]  = 'forum';
		$this->menu[4][1]  = 'pix';
		//$this->menu[5][1]  = 'help';
		//$this->menu[6][1]  = 'contact';
	}
	
// *******************************************************************************
// SET_MENU_CIM - a főmenük címei
// *******************************************************************************

	private function set_menu_cim()
	{
		for ($i = 1; $i <= 6; $i++) $this->menu[$i][2]  = $this->menu_cim['m_'.$i]['menu_cim'];
	}
	
// *******************************************************************************
// SET_ALMENU_CIM - az almenük címei
// *******************************************************************************

	private function set_almenu_cim()
	{
		for ($i = 1; $i <= 7; $i++) $this->almenu_cim[$i]  = $this->almenu_cim['m_'.$i]['almenu_cim'];
	}
}

?>
