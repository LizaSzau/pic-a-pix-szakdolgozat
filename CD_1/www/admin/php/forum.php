<?php

require_once('../class/forum_class.php');

$forum = new Forum;

if (isset($_SESSION['admin_nev'])) 
{
	switch ($akt)
	{
		case 'flag_tilt':
			$fajl = "lista";
			flag_tilt();
			lista_forum();
			break;
		case 'flag_enged':
			$fajl = "lista";
			flag_enged();
			lista_forum();
			break;
		default: 
			$fajl = "lista";
			lista_forum();
			break;
	}
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');

// *******************************************************************************
// OLDAL_SZAM - oldalszamozas
// *******************************************************************************

function oldalszam()
{
	global $lista;
	
	$oldal_akt = (int) Utils::tisztit($_GET['oldal_akt']); 
	$lista->set_oldal_akt('forum', $oldal_akt);
}

// *******************************************************************************
// LISTA_forum
// *******************************************************************************

function lista_forum()
{
	global $lista;
	global $smarty;
	global $menu;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	if ($get['nyelv'])
	{
		$nyelv = nyelv($get['nyelv']);
		$_SESSION['where_forum'] = '';
		$_SESSION['hol_forum'] = '';
		$_SESSION['mit_forum'] = '';
	}
	else
	{
		$nyelv = $_SESSION['nyelv'];
	}
	
	if ($post['mod'] == 'keres')
	{  
		$order = $_SESSION['order_forum'];
		
		if( $post['mit'] != '')
		{			
			if ($post['hol'] == 'b.id' or $post['hol'] == 'a.id')
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
			elseif ( $post['hol'] == 'a.flag')
			{
				$where_sql = $post['hol'].' = 0';
			}
			else
			{
				$where_sql = $post['hol'].' like "%'.$post['mit'].'%"';
			}
				
			$_SESSION['where_forum'] = $where_sql;
			$_SESSION['hol_forum'] = $post['hol'];
			$_SESSION['mit_forum'] = $post['mit'];
		}
		else
		{
			$_SESSION['where_forum'] = '';
			$_SESSION['hol_forum'] = '';
			$_SESSION['mit_forum'] = '';		
		}
	}
	else
	{
		$order = $get['order'];
		$_SESSION['order_forum'] = $order;	
		$where_sql = $_SESSION['where_forum'];
	}

	switch ($order)
	{
		case 11:     
			$order_sql = 'id_user';
			break;
		case 12:     
			$order_sql = 'id_user DESC';
			break;
		case 21:     
			$order_sql = 'nev';
			break;
		case 22:     
			$order_sql = 'nev DESC';
			break;
		case 31:     
			$order_sql = 'id_forum';
			break;
		case 32:     
			$order_sql = 'id_forum DESC';
			break;
		case 41:     
			$order_sql = 'datum';
			break;
		case 42:     
			$order_sql = 'datum DESC';
			break;
		case 51:     
			$order_sql = 'forum_flag';
			break; 
		case 52:     
			$order_sql = 'forum_flag DESC';
			break; 
		default:
			$order_sql = 'datum DESC';
			$order = 11;
			break;
	}
	
	$lista->set_nyelv($nyelv); 
	
	//$lista->set_oldal_sor(10); 
	//oldalszam();
	
	if ($nyelv != 'en') 
		$smarty->assign('lec_al', $menu->get_almenu_cim(3));
	else 
		$smarty->assign('lec_al', $menu->get_almenu_cim(4));
	
	$smarty->assign('tomb', $lista->lista_forum_admin($where_sql, $order_sql));
	$smarty->assign('order', $order);
	$smarty->assign('nyelv', $nyelv);
	$smarty->assign('hol', $_SESSION['hol_forum']);
	$smarty->assign('mit', $_SESSION['mit_forum']);
}

// *******************************************************************************
// FLAG_TILT - felhasználó letiltása
// *******************************************************************************

function flag_tilt()
{
	global $forum;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	$nyelv = nyelv($get['nyelv']);
	
	$forum->set_id_forum($get['id']);
	$forum->set_nyelv($nyelv);
	$forum->flag_tilt();
}

// *******************************************************************************
// FLAG_ENGED - felhasználó letiltásának feloldása
// *******************************************************************************

function flag_enged()
{
	global $forum;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	$nyelv = nyelv($get['nyelv']);
	
	$forum->set_id_forum($get['id']);
	$forum->set_nyelv($nyelv);
	$forum->flag_enged();
}

// *******************************************************************************
// NYELV - fórum nyelve
// *******************************************************************************

function nyelv($nyelv)
{
	global $menu;
	global $smarty;
	
	if ($nyelv != 'en') 
	{
		$nyelv = 'hu'; 
	}
	else 
	{
		$nyelv = 'en';
	}
	
	return $nyelv;
}

?>
