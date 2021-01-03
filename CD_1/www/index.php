<?php

header('Content-Type: text/HTML; charset=utf-8');
session_start();
ob_start();

date_default_timezone_set('Europe/Budapest');

require_once('inc/config_server.php');
require_once('class/constants_class.php');
require_once('class/adatbazis_class.php');
require_once('class/utils_class.php');
require_once('class/user_class.php');
require_once('class/lang_class.php');

Utils::smarty_prepare();

$user = new User;
if (!isset($_SESSION['user_id'])) $user->auto_login();

//echo time();
//referer();

require_once('class/menu_class.php');

$nyelv = Utils::nyelv();
$muv = (int) Utils::tisztit($_GET['muv']);
$akt = Utils::tisztit($_GET['akt']);
if (!$muv or $muv > 6) $muv = 1;

$menu = new Menu;
$menu->set_akt_menu($muv);
$menu->set_menu_cim_list($nyelv); 

$modul_akt = $menu->get_modul_akt();
$modul_cim = $menu->get_modul_cim();

$lang = new lang;
$lang->set_nyelv($nyelv); 
$lang->set_modul_akt($modul_akt); 
$lang_m = $lang->get_text_m();
$lang_i = $lang->get_text_i();

require_once('css/index.css'); 
require_once('css/'.$modul_akt.'.css'); 
require_once('php/'.$modul_akt.'.php'); 

$smarty->assign('lang_m', $lang_m);
$smarty->assign('lang_i', $lang_i);
$smarty->assign('muv', $muv);
$smarty->assign('lec', $modul_cim);
$smarty->assign('modul_akt', $modul_akt);
$smarty->assign('nyelv', $_SESSION['nyelv']);
$smarty->assign('user_id', $_SESSION['user_id']);
$smarty->assign('user_nev', $_SESSION['user_nev']);
$smarty->setTemplateDir('templates/index');
$smarty->display('index.html');

ob_end_flush();

?>
