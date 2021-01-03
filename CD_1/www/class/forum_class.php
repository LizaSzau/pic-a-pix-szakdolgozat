<?php 

class Forum
{   
	private $uzenet;
	private $id_forum;
	private $id_user;
	private $nyelv;
	
    public function __construct() 
	{
        $this->db_kapcsolat = Adatbazis::a_kapcsolat();
        $this->db_kapcsolat->init();
    } 
	
// *******************************************************************************
// SET_*
// *******************************************************************************

	public function set_uzenet($uzenet) { $this->uzenet = $uzenet; }
	public function set_nyelv($nyelv) { $this->nyelv = $nyelv; }
	
	public function set_id_forum($id) { $this->id_forum = $id; }
	public function set_id_user($id) { $this->id_user = $id; }
	
// *******************************************************************************
// uzenet_ment - üzenet mentése
// *******************************************************************************

	public function uzenet_ment()
	{
		if ($this->uzenet)
		{
			$datum = time();
			$mezo = 'flag = 1, id_user = '.$this->id_user.', uzenet = "'.$this->uzenet.'", datum = '.$datum;
			$this->db_kapcsolat->tabla_insert('forum_'.$this->nyelv, $mezo);
		}
	}
	
// *******************************************************************************
// FLAG_TILT - hozzászólás letiltása
// *******************************************************************************

	public function flag_tilt()
	{
		$tabla = 'forum_'.$this->nyelv;
		$mezo = 'flag = 0';
		$where = 'id = "'.$this->id_forum.'"';
		$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
	}
	
// *******************************************************************************
// FLAG_ENGED - hozzászólás letiltásának feloldása
// *******************************************************************************

	public function flag_enged()
	{
		$tabla = 'forum_'.$this->nyelv;
		$mezo = 'flag = 1';
		$where = 'id = "'.$this->id_forum.'"';
		$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
	}
	
}

?>
