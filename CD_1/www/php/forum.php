<?php

require_once('class/forum_class.php');
require_once('class/lista_class.php');

$forum = new Forum;
$lista = new Lista;

switch ($akt)
{
	case 'uzen_kuld':    
		uzen_kuld();
	default:
		$fajl = "index";
		$lista->set_nyelv($nyelv); 
		$lista->set_oldal_sor(10); 
		oldalszam();
		$smarty->assign('tomb', $lista->lista_forum());
		break;
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
// UZEN_KULD - üzenet küldése
// *******************************************************************************

function uzen_kuld()
{
	global $smarty;
	global $forum;
	global $nyelv;
	
	$uzenet = Utils::szoveg_vag(Utils::szo_hosszu(Utils::tisztit($_POST['uzenet']), 40), 2000);
	$forum->set_uzenet($uzenet);
	$forum->set_nyelv($nyelv);
	$forum->set_id_user($_SESSION['user_id']);
	$forum->uzenet_ment();
}

?>