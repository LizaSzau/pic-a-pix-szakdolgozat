<?php
header("Content-type: image/png");

include("../../inc/config_server.php");

function csatlakozas()
{
  $kapcsolat = mysql_connect(SZERVER, FELHASZNALO, JELSZO) or die("Nem sikerült csatlakozni az adatbázis szerverhez!");
  mysql_select_db(ADATBAZIS) or die("Nincs ilyen adatbázis!");
}

csatlakozas();

$id = (int) $_GET["id"];

$sql = "SELECT kocka_x, kocka_y, pix FROM pix WHERE id = $id";
$vissza = mySQL_query($sql);
$sor = mySQL_fetch_array($vissza);

$x = $sor[0];
$y = $sor[1];
$pix = explode(",", $sor[2]);
//$szin  = explode(",", $sor[3]);

$szin = explode(",", $sor[2]);  
$szin = array_unique($szin); 
$szin = array_values($szin); 
sort($szin);
unset($szin[0]);
$szin = array_values($szin); 

$max_x = 120;
$max_y = 150;

$mx = $max_x / $x;
$my = $max_y / $y;

if ($mx > $my) $meret = $my; else $meret = $mx;
if ($meret > 15) $meret = 15;

$im = imagecreate($x * $meret, $y * $meret); 
$sz_1 = imagecolorallocate($im, 239, 228, 163);

for ($i = 0; $i < count($szin); $i++)
{ 
	$sql = "SELECT color FROM color WHERE id = $szin[$i]";
	$vissza = mySQL_query($sql);
	$sor = mySQL_fetch_array($vissza);
	$szinek[$szin[$i]] = imagecolorallocate($im, hexdec(substr($sor[0], 0, 2)), hexdec(substr($sor[0], 2, 2)), hexdec(substr($sor[0], 4, 2)));
}

$x1 = 0;
$x2 = $meret;
$y1 = 0;
$y2 = $meret;

for ($j = 0; $j < $y; $j++)
{
	for ($i = 0; $i < $x; $i++)
	{
		$h = $j * $x + $i;
		if ($pix[$h] != 0) imagefilledrectangle($im, $x1, $y1, $x2, $y2, $szinek[$pix[$h]]);
		$x1 = $x2;
		$x2 += $meret;
	}
	
	$x1 = 0;
	$x2 = $meret;
	$y1 = $y2;
	$y2 += $meret;
}

imagegif ($im);
imagedestroy($im);

?>
