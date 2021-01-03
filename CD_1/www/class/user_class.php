<?php 

class User
{   
    protected $db_kapcsolat;
	private $email;
	private $nev;
	private $jelszo;
	private $jelszo_1;
	private $jelszo_2;
	private $kapcsa;
	private $hiba = array();
	private $dat_reg;
	private $id_user;
	private $ip;
	private $login_ok;
	private $flag;
	private $auto_login;
	private $foto;
	private $admin_ok;
	private $admin_mod;
	
    public function __construct()
    {
        $this->db_kapcsolat = Adatbazis::a_kapcsolat();
        $this->db_kapcsolat->init();
    }

/*
USER FLAG

0 - nem aktivált
1 - aktiv
2 - admin
3 - letiltott
*/

// *******************************************************************************
// SET_ *
// *******************************************************************************

	public function set_email($email) { $this->email = $email; }
	public function set_nev($nev) { $this->nev = $nev; }
	public function set_jelszo($jelszo) { $this->jelszo = $jelszo; }
	public function set_jelszo_1($jelszo_1) { $this->jelszo_1 = $jelszo_1; }
	public function set_jelszo_2($jelszo_2) { $this->jelszo_2 = $jelszo_2; }
	public function set_kapcsa($kapcsa) { $this->kapcsa = $kapcsa; }
	public function set_id_user($id_user) { $this->id_user = $id_user; }
	public function set_dat_reg($dat_reg) { $this->dat_reg = $dat_reg; }
	public function set_auto_login($auto_login) { $this->auto_login = $auto_login; }
	public function set_admin_mod() { $this->admin_mod = 1;  }
	
// *******************************************************************************
// GET_ *
// *******************************************************************************

	public function get_hiba() { return $this->hiba; }
	public function get_email() { return $this->email; }
	public function get_nev() { return $this->nev; }
	public function get_flag() { return $this->flag; }
	public function get_dat_reg() { return $this->dat_reg; }
	public function get_dat_belep() { return $this->dat_belep; }
	
	public function get_avatar() 
	{ 
		$this->avatar();
		return $this->foto; 
	}
	
	public function get_admin_ok() { return $this->admin_ok; }
	public function get_admin_mod() { return $this->admin_mod; }
	
// *******************************************************************************
// ELLENORIZ
// *******************************************************************************

	public function ellenoriz()
	{		
		$this->ell_email_forma();
		$this->ell_email();
		$this->ell_nev_forma();
		$this->ell_nev();
		$this->ell_jelszo(1);
		$this->ell_kapcsa();
	}

// *******************************************************************************
// reg_adat_ment - regisztráció során
// *******************************************************************************

	public function reg_adat_ment()
	{		
		$dat_reg = time();
		$ip = $_SERVER['REMOTE_ADDR'];

		$mezo  = 'flag = 0, ';
		$mezo .= 'email = "'.$this->email.'", ';
		$mezo .= 'nev = "'.$this->nev.'", ';
		// $mezo .= 'jelszo = "'.Utils::jelszo_kodol($this->jelszo_1).'", ';
		$mezo .= 'jelszo = "'.$this->jelszo_1.'", ';
		$mezo .= 'ip = "'.$ip.'", ';
		$mezo .= 'dat_reg = '.$dat_reg;
		
		$this->db_kapcsolat->tabla_insert('user', $mezo);	
		$this->id_user = $this->db_kapcsolat->get_last_id();
		$this->dat_reg = $dat_reg;
	}
	
// *******************************************************************************
// AKTIVAL - regisztráció aktiválása
// *******************************************************************************

	public function aktival()
	{
		$tabla = 'user';
		$mezo = 'id, ip, flag, dat_reg';
		$where = 'id = '.$this->id_user;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');		
		$db = $this->db_kapcsolat->get_sor_darab();			
		$tomb = $this->db_kapcsolat->get_select_eredmeny();			
	  
		if ($db == 1 and $tomb[0]['flag'] == 0 and $tomb[0]['dat_reg'] = $this->dat_reg) // Nem aktivált regisztráció
		{
			$datum = time();
			
			$tabla = 'user';
			$mezo = 'flag = 1, dat_reg = "'.$datum.'"';
			$where = 'id = '.$this->id_user;
	
			$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	

			$this->ip = $tomb[0]['ip'];
			$this->ip_valtozas();
			
			$reg = 1;
		}
		elseif ($db == 1 and  $tomb[0]['flag'] != 0) // Már aktivált regisztráció
		{
			$reg = 2;
		}
		else // Gikszer
		{
			$reg = 3;
		}
		
		return $reg;
	}
	
// *******************************************************************************
// IP_VALTOZAS - ip cím változása
// *******************************************************************************

	public function ip_valtozas()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$datum = time();

		if ($this->ip != $ip)
		{
			$tabla = 'user';
			$mezo = 'ip = "'.$ip.'"';
			$where = 'id = '.$this->id_user;
			$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
			
			$tabla = 'ip';
			$mezo = 'regi_ip = "'.$this->ip.'", id_user = '.$this->id_user.', datum = "'.$datum.'"';
			$this->db_kapcsolat->tabla_insert($tabla, $mezo);	
		}
	}
	
// *******************************************************************************
// LOGIN - login
// *******************************************************************************

	public function login()
	{		
		global $lang_m;
	
		// $jelszo = Utils::jelszo_kodol($this->jelszo_1);
		
		$tabla = 'user';
		$mezo = 'id, ip, flag, nev';
		$where = 'email = "'.$this->email.'" AND jelszo = "'.$this->jelszo_1.'"';
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');	
		$db = $this->db_kapcsolat->get_sor_darab();	
		
		if ($db == 1) // e-mail és jelszó egyezik
		{ 
			$tomb = $this->db_kapcsolat->get_select_eredmeny();	
		
			switch ($tomb[0]['flag'])
			{
				case 0: // nem aktivált
					$this->hiba[] = $lang_m[39];
					$this->log_stat = 1;
					break;
				case 3:  // letiltott
					$this->hiba[] = $lang_m[21];
					$this->log_stat = 2;
					break;
				case 2: // admin
					if ($this->admin_mod == 1) $this->admin_ok = 1;
				default: // ok
				
					if ($this->admin_mod == 1)
					{
						if ($this->admin_ok == 1) 
						{ 
							$_SESSION['admin_nev'] = $tomb[0]['nev']; 
							$this->hiba[] = $lang_m[60];
						}
						else 
						{
							$this->hiba[] = $lang_m[59];
						}
					}
					else
					{
						$this->id_user = $tomb[0]['id'];
						$this->ip = $tomb[0]['ip'];
						$_SESSION['user_id'] = $this->id_user;
						$_SESSION['user_nev'] = Utils::szo_hosszu($tomb[0]['nev'], 10);
					
						$this->ip_valtozas();
						
						if ($this->auto_login == 1)
						{
							setcookie('user_id', $this->id_user, time() + 90000);
							setcookie('user_nev', $tomb[0]['nev'], time() + 90000);
						}
						else
						{
							setcookie('user_id', 0, time() + 90000);
							setcookie('user_nev', 0, time() + 90000);		
						}				

							$tabla = 'user';
							$mezo = 'dat_belep = "'.time().'"';
							$where = 'id = '.$this->id_user;
							$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);
					}	
			}	
		}
		else
		{
			$tabla = 'user';
			$mezo = 'id';
			$where = 'email = "'.$this->email.'"';
			$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');	
			$db = $this->db_kapcsolat->get_sor_darab();	
			
			if ($db == 1) //  van -email cím, de nem jó a jelszó
			{
				$this->hiba[] = $lang_m[40];
				$this->log_stat = 3;
			}
			else // nincs ilyen e-mail cím;
			{
				$this->hiba[] = $lang_m[41];
				$this->log_stat = 4;			
			}
		}
	}

// *******************************************************************************
// JELSZO_FELEJT - elfelejtett jelszó
// *******************************************************************************

	public function jelszo_felejt()
	{		
		global $lang_m;
		
		$tabla = 'user';
		$mezo = 'jelszo';
		$where = 'email = "'.$this->email.'"';
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');	
		$db = $this->db_kapcsolat->get_sor_darab();	
		
		if ($db == 1) // e-mail megtalálható
		{
			$tomb = $this->db_kapcsolat->get_select_eredmeny();	
			$this->jelszo = $tomb[0]['jelszo'];	
		}
		else
		{
			$this->hiba[] = $lang_m[41];
		}
	}
	
// *******************************************************************************
// MOD_JELSZO_KULD jelszó módosítása
// *******************************************************************************

	public function jelszo_mod_kuld()
	{
		global $lang_m;
		
		$tabla = 'user';
		$mezo = 'jelszo';
		$where = 'id = "'.$this->id_user.'"';
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');	 
		$tomb = $this->db_kapcsolat->get_select_eredmeny();	

		if ($tomb[0]['jelszo'] != $this->jelszo) 
		{
			$this->hiba[] = $lang_m[56];
			return false;
		}
		
		if (!$this->ell_jelszo(0)) return false;
		
		$mezo = 'jelszo = "'.$this->jelszo_1.'"';
		$where = 'id = '.$this->id_user;
		$this->db_kapcsolat->tabla_update('user', $mezo, $where);    
		
		return true;
	}

// *******************************************************************************
// MAIL_KULD_REG - regisztrációs levél küldése
// *******************************************************************************

	public function mail_kuld_reg()
	{
		$domain = Constants::$DOMAIN_1;
		global $lang_m;
		global $nyelv;
		
		require_once("class/email_class.php");
		$email_k = new Email;	
		$email_k->set_nyelv($nyelv);
		$email_k->set_to_nev($this->nev);
		$email_k->set_from_nev(Constants::$NEV);
		$email_k->set_from_email(Constants::$EMAIL_1);
		$email_k->set_reply_email(Constants::$EMAIL);
		$email_k->set_to_email($this->email);
		$email_k->set_link('http://'.$domain.'/index.php?muv=2&akt=aktiv&id='.$this->id_user.'&dat_reg='.$this->dat_reg);
		$email_k->level_3();
	}
	
// *******************************************************************************
// MAIL_KULD_JELSZO - elfelejtett jelszó küldése
// *******************************************************************************

	public function mail_kuld_jelszo()
	{
		$domain = Constants::$DOMAIN_1;
		global $lang_m;
		global $nyelv;
		
		require_once("class/email_class.php");
		$email_k = new Email;	
		$email_k->set_nyelv($nyelv);
		$email_k->set_from_nev(Constants::$NEV);
		$email_k->set_from_email(Constants::$EMAIL_1);
		$email_k->set_reply_email(Constants::$EMAIL);
		$email_k->set_to_email($this->email);
		$email_k->set_tartalom($this->jelszo);
		$email_k->level_4();
	}
	
// *******************************************************************************
// AUTO_LOGIN - automatikus bejelentkezés
// *******************************************************************************

	public function auto_login()
	{ 
		$id = (int) $_COOKIE['user_id'];
		$nev = $_COOKIE['user_nev'];
		
		if ($id > 0)
		{
			$tabla = 'user';
			$mezo = 'nev';
			$where = 'id = '.$id;
			
			$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');			
			$sor = $this->db_kapcsolat->get_select_eredmeny();		
			
			

			//if ($nev == md5($sor[0]['nev']))
			if ($nev == $sor[0]['nev'])
			{	
				$_SESSION["user_id"] = $id;
				$_SESSION["user_nev"] = Utils::szo_hosszu($sor[0]['nev'], 10);
			}
			
			$tabla = 'user';
			$mezo = 'dat_belep = "'.time().'"';
			$where = 'id = '.$this->id_user;
			$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);
		}
	}
	
// *******************************************************************************
// AVATAR - van-e avatar
// *******************************************************************************

	private function avatar()
	{
		global $lang_m;
		
		$tabla = 'user';
		$mezo = 'foto';
		$where = 'id = '.$this->id_user;
		
		$this->db_kapcsolat->tabla_select($tabla, $mezo, $where, '', '');	
	 	$tomb = $this->db_kapcsolat->get_select_eredmeny();		

		if ($tomb[0]['foto'] == 1)
		{
			$this->foto = $this->id_user;
		}
		else
		{
			$this->foto = 0;
		}
	}
	
// *******************************************************************************
// AVATAR - új kép feltöltése
// *******************************************************************************

	public function avatar_kuld($post)
	{
		global $lang_m;

		if ($post['no_foto'] == 1)
		{  
			$tabla = 'user';
			$mezo = 'foto = 0';
			$where = 'id = "'.$this->id_user.'"';
			$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);		
				
			@unlink('user/foto/nagy/'.$id.'.jpg');
			@unlink('user/foto/kicsi/'.$id.'.jpg');
		}
		else
		{
			$hiba = array();
	 
			if (isset($_FILES['foto']) and is_uploaded_file($_FILES['foto']['tmp_name']))
			{  
				if ($_FILES['foto']['size'] > 100000) $this->hiba[] = $_FILES['foto']['name'].$lang_m[50].$_FILES['foto']['size'].' byte';
				if (!($_FILES['foto']['type'] == 'image/jpeg' or $_FILES['foto']['type'] == 'image/pjpeg')) $this->hiba[] = $_FILES['foto']['name'].$lang_m[51].$_FILES['foto']['type'];   

				if (count($this->hiba) == 0)
				{ 
				
					@unlink('user/foto/nagy/'.$id.'.jpg');
					@unlink('user/foto/kicsi/'.$id.'.jpg');
			
					$meret = @GetImageSize($_FILES['foto']['tmp_name']);
					$foto = time();
					$y = $meret[1];
					$x = $meret[0];
					Utils::kep_vag($_FILES['foto']['tmp_name'], 'user/foto/nagy/'.$this->id_user.'.jpg', 200, 200); 
					Utils::kep_vag($_FILES['foto']['tmp_name'], 'user/foto/kicsi/'.$this->id_user.'.jpg', 50, 50);       
					
					$tabla = 'user';
					$mezo = 'foto = 1';
					$where = 'id = "'.$this->id_user.'"';
					$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
				}
			}	
		}
	}
	
// *******************************************************************************
// ELL_EMAIL_FORMA - formai ellenőrzés
// *******************************************************************************

	private function ell_email_forma()
	{
		global $lang_m;
		
		if (!Utils::email_ell($this->email))
		{	
			$this->hiba[] = $lang_m[20]; // nem jó e-mail cím
		}
	}
	
// *******************************************************************************
// ELL_EMAIL - ellenőrzés
// *******************************************************************************

	private function ell_email()
	{
		global $lang_m;
		
		$this->db_kapcsolat->tabla_select('user', 'flag', 'email = "'.$this->email.'"', '', '1');		
		$email = $this->db_kapcsolat->get_select_eredmeny();
		
		if ($email)
		{
			switch ($email[0]['flag'])
			{
				case 0:
					$this->hiba[] = $lang_m[22]; // nem aktivált 
					break;
				case 1:
					$this->hiba[] = $lang_m[23]; // van már ilyen e-mail
					break;
				case 2:
					$this->hiba[] = $lang_m[23]; // van már ilyen e-mail - admin
					break;
				case 3:
					$this->hiba[] = $lang_m[21]; // letiltott
					break;
			}
		}
	}
	
// *******************************************************************************
// ELL_NEV_FROMA - név ellenőrzés formai
// *******************************************************************************

	private function ell_nev_forma()
	{
		global $lang_m;
		$nev = $this->nev;
		
		if (eregi("[^a-z0-9öüóőúéáűí_-]", $nev) or strlen($nev) > 12 or strlen($nev) < 3) $this->hiba[] = $lang_m[26];
	}
	
// *******************************************************************************
// ELL_NEV - név ellenőrzés
// *******************************************************************************

	private function ell_nev()
	{
		global $lang_m;
		
		$this->db_kapcsolat->tabla_select('user', 'id', 'nev = "'.$this->nev.'"', '', '');		
		$db = $this->db_kapcsolat->get_sor_darab();	
		
		if ($db != 0)
		{
			$this->hiba[] = $lang_m[36]; 
		}
	}
	
// *******************************************************************************
// ELL_JELSZO - jelszó ellenőrzése
// *******************************************************************************

	private function ell_jelszo($hiba)
	{
		global $lang_m;
		
		if ($this->jelszo_1 != $this->jelszo_2) 
		{
			if ($hiba == 1) 
			{
				$this->hiba[] = $lang_m[24]; 
			}
			else
			{
				$this->hiba[] = $lang_m[58]; 
				return false;
			}
		}
		else
		{
			if (strlen($this->jelszo_1) > 12 or strlen($this->jelszo_1) < 6) 
			{
				if ($hiba == 1) 
				{
					$this->hiba[] = $lang_m[25]; 
				}
				else 
				{
					$this->hiba[] = $lang_m[57];
					return false;
				}
			}
		}
		
		return true;
	}
	
// *******************************************************************************
// ELL_KAPCSA - kapcsa ellenőrzése
// *******************************************************************************

	private function ell_kapcsa()
	{
		global $lang_m;
		if ($this->kapcsa != $_SESSION["kapcsa"]) $this->hiba[] = $lang_m[28];
	}
	
// *******************************************************************************
// FLAG_TILT - felhasználó letiltása
// *******************************************************************************

	public function flag_tilt()
	{
		$tabla = 'user';
		$mezo = 'flag = 3';
		$where = 'id = "'.$this->id_user.'"';
		$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
	}
	
// *******************************************************************************
// FLAG_ENGED - felhasználó letiltásának feloldása
// *******************************************************************************

	public function flag_enged()
	{
		$tabla = 'user';
		$mezo = 'flag = 1';
		$where = 'id = "'.$this->id_user.'"';
		$this->db_kapcsolat->tabla_update($tabla, $mezo, $where);	
	}
}
?>