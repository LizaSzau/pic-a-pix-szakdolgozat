<?php

include('class/pix_class.php');

$pix = new Pix;

if (isset($_SESSION['user_id'])) 
{

	switch ($akt)
	{
		case 'del_ready': 
			$fajl = 'del_ready';
			break;
		case 'del_ready_kuld': 
			$fajl = 'null';
			$pix->set_id_user($_SESSION['user_id']);
			$pix->del_ready_pix();
			echo "<meta http-equiv='refresh' content ='0; url=?muv=1'>";
			break;
		case 'del_save': 
			$fajl = 'del_save';
			break;
		case 'del_save_kuld': 
			$fajl = 'null';
			$pix->set_id_user($_SESSION['user_id']);
			$pix->del_save_pix();
			echo "<meta http-equiv='refresh' content ='0; url=?muv=1'>";
			break;
		default:
			$fajl = 'index';
			oldalszam();
			$pix->set_id_user($_SESSION['user_id']);
			$pix->set_nyelv($nyelv);
			$smarty->assign('tomb', $pix->pix_lista());
			break;
	}
}
else
{
	switch ($akt)
	{
		default:
			$fajl = 'index';
			oldalszam(); 
			$pix->set_id_user($_SESSION['user_id']);
			$pix->set_nyelv($nyelv);
			$smarty->assign('tomb', $pix->pix_lista());
			break;
	}
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');

// *******************************************************************************
// OLDAL_SZAM - oldalszamozas
// *******************************************************************************

function oldalszam()
{
	global $pix;
	
	$oldal_akt = (int) Utils::tisztit($_GET['oldal_akt']); 
	$pix->set_oldal_akt($oldal_akt);
	
}

// *******************************************************************************
// DEL_READY - kész játékok törlése
// *******************************************************************************

function del_ready()
{
	$id_user = $_SESSION['user_id'];
	
	$sql = 'SELECT id, id_pix FROM pix_play WHERE id_user = $id_user';
	$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
	$db = mySQL_num_rows($vissza);
	
	if ($db > 0) // Felhasználó megoldott játékainak a száma
	{
		while ($sor = mySQL_fetch_array($vissza))
		{
			$db_old = 0;
			$sql = 'SELECT id FROM pix_play WHERE id_pix = '.$sor[1];
			$vissza_2 = mySQL_query($sql) or die(adatbazis_hiba($sql));
			$db_old = mySQL_num_rows($vissza_2);  // A regisztrált user hányszor oldotta meg az adott játékot.
			
			$sql = 'SELECT id, datum FROM pix_play WHERE id_pix = '.$sor[1].' AND id_user = 0';
			$vissza_1 = mySQL_query($sql) or die(adatbazis_hiba($sql));
			$sor_1 = mySQL_fetch_array($vissza_1);
			
			if (mySQL_num_rows($vissza_1) == 0)
			{
				$sql = 'INSERT INTO pix_play SET id_user = 0, id_pix = '.$sor[1].', datum = '.$db_old;
				mySQL_query($sql) or die(adatbazis_hiba($sql));	
			}
			else
			{	
				$sor_2 = mySQL_fetch_array($vissza_1);
				$db_old += $sor_2[1];
				$sql = 'UPDATE pix_play set datum = '.$db_old.' WHERE id = '.$sor_1[0];
				mySQL_query($sql) or die(adatbazis_hiba($sql));	
			}
			
			$sql = 'DELETE from pix_play where id_user = $id_user';
			mySQL_query($sql) or die(adatbazis_hiba($sql));	
		}
	}
}


?>
