<?php

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

// *******************************************************************************
// NYELV - nyelv
// *******************************************************************************

function nyelv()
{ 
	if ($_GET['nyelv'])
	{
		if ($_GET['nyelv'] == 'hu') $nyelv = 'hu'; else $nyelv = 'en';
		setcookie('nyelv', $nyelv, time() + 90000);
	}
	else
	{
		if (!$_COOKIE['nyelv'])  
		{ 
			$nyelv = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			if ($nyelv != 'hu') $nyelv = 'en';
			setcookie('nyelv', $nyelv, time() + 90000);
		}
		else
		{
			$nyelv = $_COOKIE['nyelv'];
		}
	}

	return $nyelv;
}

$nyelv = nyelv();
if ($nyelv != "hu")  $nyelv = "en";

csatlakozas();

mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
mysql_query("SET character_set_results = 'utf8'");
mysql_query("SET character_set_server = 'utf8'");
mysql_query("SET character_set_client = 'utf8'");

$nyelvs = "_".$nyelv;

function csatlakozas()
{
  $kapcsolat = mysql_connect(SZERVER, FELHASZNALO, JELSZO) or die("Nem sikerült csatlakozni az adatbázis szerverhez!");
  mysql_select_db(ADATBAZIS);
}

$id = tisztit($_POST["id"]);
$id_user = tisztit($_POST["id_user"]);

$sql = "SELECT a.kocka_x, a.kocka_y, a.pix, a.cim".$nyelvs.", b.nev FROM pix AS a, user AS b WHERE a.id = $id AND a.id_user = b.id";
$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
$sor = mySQL_fetch_array($vissza);

$kocka_x = $sor[0];
$kocka_y = $sor[1];
$pix = $sor[2];
$cim = $sor[4];

$szinek = explode(",", $sor[2]);  
$szinek = array_unique($szinek); 
$szinek = array_values($szinek); 
sort($szinek);
unset($szinek[0]);
$szinek = array_values($szinek); 
$color = implode(",",$szinek);
					
$sql = "SELECT pix FROM pix_save WHERE id_user = $id_user AND id_pix = $id";
$vissza = mySQL_query($sql)  or die(adatbazis_hiba($sql));

$pakt = 0;

if (mySQL_num_rows($vissza) == 1)
{
	$sor = mySQL_fetch_array($vissza);
	$pakt = $sor[0];
}

echo "kocka_x=$kocka_x&kocka_y=$kocka_y&pix=$pix&szinek=$color&cim=$cim&nyelv=$nyelv&pakt=$pakt";

?>