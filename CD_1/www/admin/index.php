<?php

header('Content-Type: text/HTML; charset=utf-8');
session_start();
ob_start();

date_default_timezone_set('Europe/Budapest');

require_once('../inc/config_server.php');
require_once('../class/constants_class.php');
require_once('../class/adatbazis_class.php');
require_once('../class/utils_class.php');
require_once('../class/user_class.php');
require_once('../class/lang_class.php');
require_once('../class/lista_class.php');
require_once('class/menu_class.php');

Utils::smarty_prepare();

$nyelv = Utils::nyelv();

$lista = new Lista;
$user = new User;
$user->set_admin_mod();

$muv = (int) Utils::tisztit($_GET['muv']);
$akt = Utils::tisztit($_GET['akt']);
if (!$muv or $muv > 6) $muv = 1;

$menu = new Menu;
$menu->set_akt_menu($muv);
$menu->set_menu_cim_list(); 
$menu->set_almenu_cim_list(); 

$modul_akt = $menu->get_modul_akt();  
$modul_cim = $menu->get_modul_cim();

$lang = new lang; 
$lang->set_nyelv($nyelv); 
$lang->set_admin_mod();
$lang->set_modul_akt($modul_akt); 

$lang_m = $lang->get_text_m();
$lang_i = $lang->get_text_i(); 

require_once('css/index.css'); 
require_once('css/'.$modul_akt.'.css'); 
require_once('php/'.$modul_akt.'.php'); 

for ($i = 1; $i <= 12; $i++) $smarty->assign("szin_".$i, constant("SZIN_".$i));

$smarty->assign('muv', $muv);
$smarty->assign('lec', $modul_cim);
$smarty->assign('modul_akt', $modul_akt); 
$smarty->assign('user_nev', $_SESSION['admin_nev']);
$smarty->setTemplateDir('templates/index'); 
$smarty->display('index.html');

ob_end_flush();
?>
