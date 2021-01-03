<?php

switch ($akt)
{
	default:
		if (!$akt) $akt = 'tortenet';
		$fajl = $akt;
		break;
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');
