<?php

if (isset($_SESSION['admin_nev'])) 
{
	switch ($akt)
	{
		case 'lista':
			$fajl = "lista";
			lista_user();
			$smarty->assign('lec_al', $menu->get_almenu_cim(1));
			break;
		case 'flag_tilt':
			$fajl = "lista";
			flag_tilt();
			lista_user();
			$smarty->assign('lec_al', $menu->get_almenu_cim(1));
			break;
		case 'flag_enged':
			$fajl = "lista";
			flag_enged();
			lista_user();
			$smarty->assign('lec_al', $menu->get_almenu_cim(1));
			break;
		case 'lista_foto':
			$fajl = "lista_foto";
			lista_foto();
			$smarty->assign('lec_al', $menu->get_almenu_cim(2));
			break;
		case 'foto_torol':
			$fajl = "lista_foto";
			foto_torol();
			lista_foto();
			$smarty->assign('lec_al', $menu->get_almenu_cim(2));
			break;
		case 'login_kuld':     
			$fajl = 'null';
			login_kuld();
			$smarty->assign('reg', 1);
			break;
		case 'logout': 
			$fajl = 'null';
			logout();
			break;
		default: 
			$fajl = "lista";
			lista_user();
			$smarty->assign('lec_al', $menu->get_almenu_cim(1));
			break;
	}
}
else
{
	switch ($akt)
	{
		case 'login_kuld':     
			$fajl = 'index';
			login_kuld();
			$smarty->assign('reg', 1);
		default:
			$fajl = 'index';
			$smarty->assign('reg', 1);
			break;
	}
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');

// *******************************************************************************
// LISTA_USER - felhsaználók listázása
// *******************************************************************************

function lista_user()
{
	global $smarty;
	global $lista;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	if ($get['nyelv'])
	{
		$_SESSION['where'] = '';
		$_SESSION['hol'] = '';
		$_SESSION['mit'] = '';
	}

	if ($post['mod'] == 'keres')
	{ 
		$order = $_SESSION['order'];
		
		
		if ($post['hol'] == 'id')
		{
			$post['mit'] = (int) $post['mit'];
			if ($post['mit']== 0) 
			{
				$where_sql = ''; 
				$post['mit'] = '';
			}
			else 
			{
				$where_sql = $post['hol'].' = '.$post['mit'];
			}
		}
		else
		{
			$where_sql = $post['hol'].' like "%'.$post['mit'].'%"';
		}
			
		$_SESSION['where'] = $where_sql;
		$_SESSION['hol'] = $post['hol'];
		$_SESSION['mit'] = $post['mit'];
	}
	else
	{
		$order = $get['order'];
		$_SESSION['order'] = $order;	
		$where_sql = $_SESSION['where'];
	}
	
	switch ($order)
	{
		case 11:     
			$order_sql = 'id';
			break;
		case 12:     
			$order_sql = 'id DESC';
			break;
		case 21:     
			$order_sql = 'flag';
			break;
		case 22:     
			$order_sql = 'flag DESC';
			break;
		case 31:     
			$order_sql = 'email';
			break;
		case 32:     
			$order_sql = 'email DESC';
			break;
		case 41:     
			$order_sql = 'nev';
			break;
		case 42:     
			$order_sql = 'nev DESC';
			break;
		case 51:     
			$order_sql = 'ip';
			break;
		case 52:     
			$order_sql = 'ip DESC';
			break;
		case 61:     
			$order_sql = 'dat_belep';
			break;
		case 62:     
			$order_sql = 'dat_belep DESC';
			break;
		case 71:     
			$order_sql = 'dat_reg';
			break;
		case 72:     
			$order_sql = 'dat_reg DESC';
			break;
		default:
			$order_sql = 'id';
			$order = 11;
			break;
	}	
	
	$smarty->assign('tomb', $lista->lista_user($where_sql, $order_sql));
	$smarty->assign('order', $order);
	$smarty->assign('hol', $_SESSION['hol']);
	$smarty->assign('mit', $_SESSION['mit']);
}

// *******************************************************************************
// LISTA_FOTO - fényképek listázása
// *******************************************************************************

function lista_foto()
{
	global $smarty;
	global $lista;

	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	if ($get['nyelv'])
	{
		$_SESSION['where_foto'] = '';
		$_SESSION['hol_foto'] = '';
		$_SESSION['mit_foto'] = '';
	}
	
	if ($post['mod'] == 'keres')
	{ 
		if ($post['mit'] != '')
		{
			if ($post['hol'] == 'id')
				$where_sql = $post['hol'].' like "'.$post['mit'].'"';
			else
				$where_sql = $post['hol'].' like "%'.$post['mit'].'%"';
		}
			
		$_SESSION['where_foto'] = $where_sql;
		$_SESSION['hol_foto'] = $post['hol'];
		$_SESSION['mit_foto'] = $post['mit'];
	}
	else
	{
		$order = $get['order'];
		$where_sql = $_SESSION['where_foto'];
	}
	
	$smarty->assign('tomb', $lista->lista_user_foto($where_sql, 'dat_reg DESC'));
	$smarty->assign('mit', $_SESSION['mit_foto']);
	$smarty->assign('hol', $_SESSION['hol_foto']);
}

// *******************************************************************************
// FOTO_TOROL - fénykép törlése
// *******************************************************************************

function foto_torol()
{
	global $user;
	
	$id = Utils::tisztit($_POST["id"]);
	$user->set_id_user($id);
	
	$post['no_foto'] = 1;

	$user->avatar_kuld($post);
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
	$user->login();
	
	$h = $user->get_hiba();
	$hiba = array_merge ($hiba, $h);
	
	$admin = $user->get_admin_ok();
	
	if ($admin == 1)
	{ 
		$smarty->assign("uzen", $hiba);
		$smarty->assign("uzen_tip", 2);
		return true;	
	}
	else
	{
		$smarty->assign("uzen", $hiba);
	}
}

// *******************************************************************************
// FLAG_TILT - felhasználó letiltása
// *******************************************************************************

function flag_tilt()
{
	global $user;
	
	$id = Utils::tisztit($_GET["id"]);
	$user->set_id_user($id);
	$user->flag_tilt();
}

// *******************************************************************************
// FLAG_ENGED - felhasználó letiltásának feloldása
// *******************************************************************************

function flag_enged()
{
	global $user;
	
	$id = Utils::tisztit($_GET["id"]);
	$user->set_id_user($id);
	$user->flag_enged();
}

// *******************************************************************************
// LOGOUT - logout
// *******************************************************************************

function logout()
{
	session_unregister('admin_nev');
	session_unregister('hol');
	session_unregister('mit');
	session_unregister('hol_foto');
	session_unregister('mit_foto');
	session_unregister('where');
	session_unregister('hol');
	session_unregister('mit');
	session_unregister('where_forum');
	session_unregister('hol_forum');
	session_unregister('mit_forum');
	echo "<meta http-equiv='refresh' content ='0; url=?/admin'>";
}

?>
