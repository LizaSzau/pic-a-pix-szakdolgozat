<?php

session_start(); 

include("config_server.php");

// *******************************************************************************
// TISZTIT - adatbázisba előkészítés
// *******************************************************************************

function tisztit($mi)
{
	// $mi = addslashes(trim(strip_tags($mi)));
	$mi = trim(strip_tags($mi));
	$mi = eregi_replace("from:", "", $mi);
	$mi = eregi_replace("cc:", "", $mi);
	$mi = eregi_replace("bcc:", "", $mi);
	$mi = eregi_replace("to:", "", $mi);
	$mi = eregi_replace("Reply-to:", "", $mi);
	$mi = eregi_replace("'", '"', $mi);
  
  return $mi;
}

// *******************************************************************************
// ADATBAZIS_HIBA - hibák naplózása
// *******************************************************************************

function adatbazis_hiba($sql)
{ 
	$ip = $_SERVER['REMOTE_ADDR'];
	$referer = $_SERVER['HTTP_REFERER'];
	$datum = time();
	$sql = eregi_replace("'", '"', $sql);
	$sql = "INSERT INTO hiba  SET 
            hiba = '$sql', 
			ip = '$ip', 
			referer = '$referer', 
			datum = '$datum'";
			
	mySQL_query($sql) or die($sql);

}

csatlakozas();

function csatlakozas()
{
	$kapcsolat = mysql_connect(SZERVER, FELHASZNALO, JELSZO) or die("Nem sikerült csatlakozni az adatbázis szerverhez!");
	mysql_select_db(ADATBAZIS);
}

$id_pix = tisztit($_POST["id_pix"]);
$id_user = tisztit($_POST["id_user"]);

$datum = time();

if ($id_user == 0)
{
	$sql = "SELECT id, datum FROM pix_play WHERE id_pix = $id_pix && id_user = 0";
	$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
	
	if (mySQL_num_rows($vissza) == 0)
	{
		$sql = "INSERT INTO pix_play SET id_user = 0, id_pix = $id_pix, datum = '1'";
		mySQL_query($sql) or die(adatbazis_hiba($sql));	
	}
	else
	{
		$sor = mySQL_fetch_array($vissza);
		$db = ++$sor[1];
		
		$sql = "UPDATE pix_play set datum = '$db' WHERE id = ".$sor[0];
		mySQL_query($sql) or die(adatbazis_hiba($sql));	
	}
}
else
{
	$sql = "INSERT INTO pix_play SET id_user = $id_user, id_pix = $id_pix, datum = '$datum'";
	mySQL_query($sql) or die(adatbazis_hiba($sql));	

	$sql = "DELETE FROM pix_save WHERE id_user = $id_user AND id_pix = $id_pix";
	mySQL_query($sql) or die(adatbazis_hiba($sql));
}

$_SESSION["old"] = $id_pix;

?>
