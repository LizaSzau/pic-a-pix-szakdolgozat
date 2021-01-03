<?php

require_once('../class/pix_class.php');
require_once('../class/lista_class.php');

$pix = new Pix;
$lista = new Lista;

if (isset($_SESSION['admin_nev'])) 
{
	switch ($akt)
	{
		case 'modosit':
			$fajl = "urlap_mod";
			$smarty->assign('lec_al', $menu->get_almenu_cim(5));
			valogat_mod();
			break;
		case 'modosit_kuld':
			if (isset($_POST['btn_vissza'])) 
			{
				$fajl = "lista";
				lista_pix();		
			}
			else
			{
				$fajl = "urlap_mod";
				modosit_kuld();
			}
			$smarty->assign('lec_al', $menu->get_almenu_cim(5));
			break;
		case 'uj_kuld':
			if (isset($_POST['btn_vissza'])) 
			{
				$fajl = "lista";
				lista_pix();		
			}
			else
			{
				$fajl = "null";
				if (uj_kuld())
				{
					$fajl = "lista";
					lista_pix();
				}
				else 
				{
					$fajl = "urlap_uj";
				}
			}
			$smarty->assign('lec_al', $menu->get_almenu_cim(6));
			break;
		case 'uj':
			$fajl = "urlap_uj";
			$smarty->assign('lec_al', $menu->get_almenu_cim(6));
			break;
		case 'torol':
			$fajl = "urlap_torol";
			valogat_mod();
			$smarty->assign('lec_al', $menu->get_almenu_cim(7));
			break;
		case 'torol_kuld':
			$fajl = "lista";
			torol_kuld();
			lista_pix();
			$smarty->assign('lec_al', $menu->get_almenu_cim(5));
			break;
		default: 
			$fajl = "lista";
			lista_pix();
			$smarty->assign('lec_al', $menu->get_almenu_cim(5));
			break;
	}
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');


// *******************************************************************************
// LISTA_PIX 
// *******************************************************************************

function lista_pix()
{
	global $smarty;
	global $lista;
	
	foreach ($_GET as $kulcs => $ertek) { $get[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	if ($get['nyelv'])
	{
		$_SESSION['where_pix'] = '';
		$_SESSION['hol_pix'] = '';
		$_SESSION['mit_pix'] = '';
	}
	
	if ($post['mod'] == 'keres')
	{ 
		if ($post['hol'] == 'a.id')
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
		elseif ($post['hol'] == 'datum')
		{
			$d = explode(".", $post['mit']); 
			$datum = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
			$where_sql = $post['hol'].' = "'.$datum.'"';
		}
		elseif ($post['hol'] == 'magyar' or $post['hol'] == 'angol')
		{
			$where_sql = $post['hol'].' like "%'.$post['mit'].'%" ';
		}
		else
		{
			$where_sql = $post['hol'].' like "%'.$post['mit'].'%" COLLATE utf8_bin';
		}
		
		$_SESSION['where_pix'] = $where_sql;
		$_SESSION['hol_pix'] = $post['hol'];
		$_SESSION['mit_pix'] = $post['mit'];
	}
	else
	{
		$order = $get['order'];
		$where_sql = $_SESSION['where_pix'];
	}
	
	$smarty->assign('tomb', $lista->lista_pix($where_sql));
	$smarty->assign('hol', $_SESSION['hol_pix']);
	$smarty->assign('mit', $_SESSION['mit_pix']);
}

// *******************************************************************************
// VALOGAT_MOD - egy játék adatai 
// *******************************************************************************

function valogat_mod()
{
	global $smarty;
	global $pix;
	
	foreach ($_POST as $kulcs => $ertek) { $post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); }
	
	$pix->set_id_pix($post['id']);
	$tomb = $pix->pix_adat($post);
	
	$smarty->assign('tomb', $tomb);
}

// *******************************************************************************
// MODOSIT_KULD - modosítás küldése
// *******************************************************************************

function modosit_kuld()
{
	global $smarty;
	global $pix;
	global $lang_m;
	
	$hiba = array();
	
	foreach ($_POST as $kulcs => $ertek) 
	{ 
		$post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); 
		//$pix->{'set_'.$kulcs.}($ertek);
	}
	
	$pix->set_id_pix($post['id']);
	
	$pix->set_cim_en($post['cim_en']);
	$pix->set_cim_hu($post['cim_hu']);
	$pix->set_dif($post['dif']);
	$pix->set_szel($post['szel']);
	$pix->set_hossz($post['hossz']);
	$pix->set_kocka_x($post['kocka_x']);
	$pix->set_kocka_y($post['kocka_y']);
	$pix->set_pix($post['pix']);
	
	$pix->ellenoriz();
	
	$h = $pix->get_hiba();
	$hiba = array_merge($hiba, $h);

	if (count($hiba) == 0)
	{ 
		$pix->pix_modosit();
		
		$hiba[] = $lang_m[22];
		$smarty->assign("uzen_tip", 2);
		$smarty->assign("uzen", $hiba);
	}
	else
	{
		$smarty->assign("uzen", $hiba);
	}
	
	foreach ($post as $kulcs => $ertek) 
	{ 
		$tomb[0][$kulcs] = $ertek;
	}
	
	$tomb[0]['pix'] = $pix->get_pix();
	
	$smarty->assign('tomb', $tomb);
}

// *******************************************************************************
// UJ_KULD - új küldése
// *******************************************************************************

function uj_kuld()
{
	global $smarty;
	global $pix;
	global $lang_m;
	
	$hiba = array();
	
	foreach ($_POST as $kulcs => $ertek) 
	{ 
		$post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); 
		//$pix->{'set_'.$kulcs.}($ertek);
	}
	
	$pix->set_cim_en($post['cim_en']);
	$pix->set_cim_hu($post['cim_hu']);
	$pix->set_dif($post['dif']);
	$pix->set_szel($post['szel']);
	$pix->set_hossz($post['hossz']);
	$pix->set_kocka_x($post['kocka_x']);
	$pix->set_kocka_y($post['kocka_y']);
	$pix->set_pix($post['pix']);
	
	$pix->ellenoriz();
	
	$h = $pix->get_hiba();
	$hiba = array_merge($hiba, $h);

	if (count($hiba) == 0)
	{ 
		$pix->pix_uj();
		return true;
	}
	else
	{
		$smarty->assign("uzen", $hiba);
		foreach ($post as $kulcs => $ertek) 
		{ 
			$tomb[0][$kulcs] = $ertek;
		}
		
		$tomb[0]['pix'] = $pix->get_pix();
		
		$smarty->assign('tomb', $tomb);
		
		return false;
	}
}

// *******************************************************************************
// TOROL_KULD - törlés küldése
// *******************************************************************************

function torol_kuld()
{
	global $pix;
	global $lang_m;
	
	$hiba = array();
	
	foreach ($_POST as $kulcs => $ertek) 
	{ 
		$post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); 
	}
	
	$pix->set_id_pix($post['id']);
	$pix->pix_torol();
}


?>
