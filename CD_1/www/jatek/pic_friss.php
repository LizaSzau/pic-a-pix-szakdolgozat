<?php

session_start(); 

include("config_server.php");

// *******************************************************************************
// CSATLAKOZAS - adatbázishoz csatlakozás
// *******************************************************************************

function csatlakozas()
{
	$kapcsolat=mysql_connect(SZERVER,FELHASZNALO,JELSZO) or die("Nem sikerült csatlakozni az adatbázis szerverhez!");
	mysql_select_db(ADATBAZIS) or die("Nincs ilyen adatbázis!");
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

// *******************************************************************************
// KEP_KESZ - kész kép
// *******************************************************************************

function kep_kesz()
{
	$pic_id = $_SESSION["old"];
	$user_id = $_SESSION["user_id"];
	
	$megold = 0;
	
	$sql = "SELECT datum FROM pix_play WHERE id_pix = $pic_id AND id_user = 0";
	$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
	$sor = mySQL_fetch_array($vissza);
	$megold = $sor[0];
		
	$sql = "SELECT id FROM pix_play WHERE id_pix = $pic_id AND id_user > 0";
	$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
	$megold += mySQL_num_rows($vissza);	
			
	if ($pic_id > 0 and $user_id > 0) // Van frissíteni való és be vannak jelentkezve
	{
		mysql_query("SET NAMES 'utf8'");
		$sql = "SELECT id FROM pix_play WHERE id_pix = $pic_id AND id_user = $user_id";
		$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
	
		if (mySQL_num_rows($vissza) > 0) 
		{	
			$nyelv = $_SESSION["nyelv"];
			$sql = "SELECT  cim_".$nyelv." FROM pix WHERE id = $pic_id";
			$vissza = mySQL_query($sql) or die(adatbazis_hiba($sql));
			$sor = mySQL_fetch_array($vissza);
			
			$html_kep = '<table class="pix_1" cellpadding="0" cellspacing="0">
					<tr><td>
					<img src="php/rajzol/rajzol_ready.php?id='.$pic_id.'" class="pix" title="'.$sor[0].'">
					</td></tr></table>';
			$html_ok = '<img src="images/btn_ok.gif" alt="start pic-a-pix">';
				
			$tomb[0] = $html_kep;
			$tomb[1] = $pic_id;
			$tomb[2] = $html_ok;
			$tomb[3] = $megold;
		}
		else
		{
			$tomb[0] = "NO";
			$tomb[1] = $pic_id;
			$tomb[2] = $megold;
		}
	}
	else
	{
		$tomb[0] = "NO";
		$tomb[1] = $pic_id;
		$tomb[2] = $megold;
	}

	$_SESSION["old"] = 0;
	
	return $tomb;
}

// *******************************************************************************
// KEP_MENT - mentett kép
// *******************************************************************************

function kep_ment()
{
	$pic_id = $_SESSION["ment"];
	$user_id = $_SESSION["user_id"];

	if ($pic_id > 0 and $user_id > 0)
	{	
			$html_kep = '<table class="pix_1" cellpadding="0" cellspacing="0">
					<tr><td>
					<img src="php/rajzol/rajzol_save.php?id='.$pic_id.'" class="pix">
					</td></tr></table>';
			$html_ok = '<img src="images/btn_felok.gif" alt="start pic-a-pix">';
					
			$tomb[0] = $html_kep;
			$tomb[1] = $pic_id;
			$tomb[2] = $html_ok;
	}
	else
	{
		$tomb[0] = "NO";
	}

	$_SESSION["ment"] = 0;
	return $tomb;
}

if ($_SESSION["old"] > 0)
{
	$tomb = kep_kesz();
}
	else
{
	$tomb = kep_ment();
}

$json = json_encode($tomb);
echo $json;
		
?>
