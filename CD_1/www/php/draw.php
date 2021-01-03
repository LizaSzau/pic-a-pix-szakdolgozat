<?php

switch ($akt)
{
  default:
    $fajl = 'index';
    break;
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');