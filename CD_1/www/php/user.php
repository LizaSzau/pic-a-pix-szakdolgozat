<?php

if (isset($_SESSION['user_id'])) 
{
	switch ($akt)
	{
		case 'logout': 
			$fajl = 'null';
			logout();
			break;
		case 'jelszo': 
			$fajl = 'jelszo';
			break;
		case 'jelszo_kuld': 
			$fajl = 'null';
			if (jelszo_mod_kuld()) logout(); else $fajl = 'jelszo';
			break;
		case 'avatar': 
			$fajl = 'avatar';
			avatar();
			break;
		case 'avatar_kuld': 
			$fajl = 'avatar';
			avatar_kuld();
			avatar();
			break;
		default:
			$fajl = 'null';
			echo "<meta http-equiv='refresh' content ='0; url=?muv=1'>";
			break;
	}
}
else
{
	switch ($akt)
	{
		case 'reg':              
			$fajl = 'reg';		
			break;
		case 'reg_kuld':                                              
			$fajl = 'reg';
			regisztracio_kuld();
			break;
		case 'aktiv':                                              
			$fajl = 'aktiv';
			$smarty->assign('reg', aktiv());
			$smarty->assign('email_jp', Constants::$EMAIL);
			break;
		case 'login_kuld':        
			$fajl = 'null';
			if (login_kuld()) 
			{
				echo "<meta http-equiv='refresh' content ='0; url=?muv=1'>";
			}
			else 
			{
				$smarty->assign('email_jp', Constants::$EMAIL);
				$fajl = 'login';
			}
			break;
		case 'jelszo_felejt':  
			$fajl = 'login';		
			$smarty->assign('email_jp', Constants::$EMAIL);
			$fajl = 'login';
			jelszo_felejt();
			break;
		default:
			echo "<meta http-equiv='refresh' content ='0; url=?muv=1'>";
			break;
	}
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');

// *******************************************************************************
// REGISZTRACIO_KULD - regisztráció
// *******************************************************************************

function regisztracio_kuld()
{	
	global $smarty;
	global $user;

	$hiba = array();
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }

	$user->set_email($post['email']);
	$user->set_nev($post['nev']);
	$user->set_jelszo_1($post['jelszo_1']);
	$user->set_jelszo_2($post['jelszo_2']);
	$user->set_kapcsa($post['kapcsa']);

	$user->ellenoriz();
	
	$h = $user->get_hiba();
	$hiba = array_merge ($hiba, $h);
	
	if (count($hiba) == 0)
	{ 
		$user->reg_adat_ment();
		$user->mail_kuld_reg();
		
		$smarty->assign("uzen_tip", 2);
		$smarty->assign("uzen", $uzen);
	}
	else
	{
		foreach ($post as $kulcs => $ertek) 
		{ 
			$v = Utils::tisztit_1($kulcs);
			$$v = Utils::tisztit_1($ertek); 
			$smarty->assign($v, $ertek);
		}
		
		$smarty->assign("uzen", $hiba);
	}
}

// *******************************************************************************
// AKTIV - e-mail-es aktiválás 
// *******************************************************************************

function aktiv()
{  
	global $smarty;
	global $user;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	$user->set_id_user($get['id']);
	$user->set_dat_reg($get['dat_reg']);
	
	$reg = $user->aktival();
	
	return $reg;	
}

// *******************************************************************************
// LOGIN_KULD
// *******************************************************************************

function login_kuld()
{
	global $smarty;
	global $user;
	
	$hiba = array();
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	$user->set_email($post['email']);
	$user->set_jelszo_1($post['jelszo']);
	$user->set_auto_login($post['auto']);
	$user->login();
	
	$h = $user->get_hiba();
	$hiba = array_merge ($hiba, $h);

	if (count($hiba) > 0) 
	{
		$smarty->assign("uzen", $hiba);
		$smarty->assign("email", $post['email']);
	}
	else
	{
		return true;
	}
}

// *******************************************************************************
// JELSZO_FELEJT
// *******************************************************************************

function jelszo_felejt()
{
	global $smarty;
	global $lang_m;
	global $user;
	
	$hiba = array();
	
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	$user->set_email($post['email']);
	$user->jelszo_felejt();
	
	$h = $user->get_hiba();
	$hiba = array_merge ($hiba, $h);

	if (count($hiba) == 0) 
	{
		$user->mail_kuld_jelszo();
		$smarty->assign("uzen_tip", 2);
		$hiba[] = $lang_m[43];
	}
	
	$smarty->assign("email", $post['email']);
	$smarty->assign("uzen", $hiba);
}
// *******************************************************************************
// JELSZO_CSERE_KULD - jelszó cseréje
// *******************************************************************************

function jelszo_mod_kuld()
{
	global $smarty;
	global $user;
  
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
  
	$user->set_jelszo($post['jelszo_0']);
	$user->set_jelszo_1($post['jelszo_1']);
	$user->set_jelszo_2($post['jelszo_2']);
	$user->set_id_user($_SESSION['user_id']);
	
	if ($user->jelszo_mod_kuld())
	{ 
		return true;
	}
	else
	{	
		$hiba = array();
		$h = $user->get_hiba();
		$hiba = array_merge ($hiba, $h);
		$smarty->assign("uzen", $hiba);
		
		return false;
	}
}

// *******************************************************************************
// AVATAR - jelenlegi avatar
// *******************************************************************************

function avatar()
{
	global $smarty;
	global $user;
	
	$user->set_id_user($_SESSION['user_id']);
	$foto = $user->get_avatar();

	$smarty->assign('foto', $foto);
}

// *******************************************************************************
// AVATAR_KULD - avatar feltöltése
// *******************************************************************************

function avatar_kuld()
{
	global $smarty;
	global $user;
	
	$hiba = array();
	
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	$user->set_id_user($_SESSION['user_id']);
	$user->avatar_kuld($post);
	
	$h = $user->get_hiba();
	$hiba = array_merge ($hiba, $h);

	$smarty->assign("uzen", $hiba);
}

// *******************************************************************************
// LOGOUT - logout
// *******************************************************************************

function logout()
{
	session_unregister('user_id');
	session_unregister('user_nev');
	setcookie('user_id', 0, time() + 90000);
	setcookie('user_pass', 0, time() + 90000);
	echo "<meta http-equiv='refresh' content ='0; url=?'>";
}

?>
