<?php

class Utils
{	
	
// *******************************************************************************
// SMARTY-PREPARE - smarty előkészítés
// *******************************************************************************

	public static function smarty_prepare()
	{
		global $smarty;

		require BASE_DIR.'/inc/smarty/Smarty.class.php';
		$smarty = new Smarty;

		$smarty->compile_check = TRUE;
		//$smarty->debugging = true;
		$smarty->force_compile = true; $smarty->setTemplateDir('templates/index'); 
	}

// *******************************************************************************
// NYELV - nyelv lekérdezése
// *******************************************************************************

	public function nyelv()
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
		
		if ($nyelv != 'hu') $nyelv = 'en';
		
		$_SESSION['nyelv'] = $nyelv;
		return $nyelv;
	}

// *******************************************************************************
// JELSZO_KODOL - jelszó kódolás
// *******************************************************************************

	public static function jelszo_kodol($jelszo)
	{
		$salt = "'~`!@#$%^&*()[]{}-_\/|\';:,.+=<>?'";
		$hash = $jelszo.$salt;
			
		for ( $i = 0; $i < 982; $i++ ) 
		{
			$hash = md5($hash);
		}	
			
		return $hash;
	}

// *******************************************************************************
// TISZTIT - bejövő adatok feldolgozása elötti előkészítés
// *******************************************************************************

    public static function tisztit($mit)
    {
        $mit = trim(strip_tags($mit));
		$mit = mySQL_escape_string($mit);
		
        return $mit;
    }

// *******************************************************************************
// TISZTIT_1 - bejövő adatok feldolgozása elötti előkészítés
// *******************************************************************************

    public static function tisztit_1($mit)
    {
        $mit = trim(strip_tags($mit));
		
        return $mit;
    }
	
// *******************************************************************************
// EMAIL_ELL - e-mail cím ellenőrzése
// *******************************************************************************

	public static function email_ell($mi)
	{
		if (eregi("^[a-zöüóőúéáűí0-9\._-]+@[a-zöüóőúéáűí0-9\._-]+\.+[a-z]{2,4}$", $mi))
			return true;
		else
			return false;
	}

// *******************************************************************************
// DÁTUM FORMÁZÁSA - számmal kiírt hónappal + idő
// *******************************************************************************

	public static function datum_forma_ido($datum)
	{
	  return date("Y.m.d. H:i", $datum);
	}
	
// *******************************************************************************
// DÁTUM FORMÁZÁSA RÖVID - számmal kiírt hónappal
// *******************************************************************************

	public static function datum_forma($datum)
	{
	  	if ($_SESSION['nyelv'] == 'hu')
			return @date("Y.m.d.", $datum);
		else
			return @date("d.m.Y.", $datum);
	}
	
// *******************************************************************************
// DÁTUM FORMÁZÁSA RÖVID - számmal kiírt hónappal
// *******************************************************************************

	public static function datum_forma_hu($datum)
	{
		return @date("Y.m.d.", $datum);
	}
	
// *******************************************************************************
// SZOVEG_VAG - text mező levágása x karakterre
// *******************************************************************************

	public static function szoveg_vag($szoveg, $db)
	{
		$szoveg = substr($szoveg, 0, $db);
		return $szoveg;
	}

// *******************************************************************************
// SZO_HOSSZU - hosszú szavak vágása, tábla szétcsúszás miatt
// *******************************************************************************

	public static function szo_hosszu($szoveg_orig, $db)
	{
		$szov_hossz = strlen($szoveg_orig);
	  
		for ($i = 0; $i <= $szov_hossz; $i++) $szoveg[$i] = $szoveg_orig[$i];

		$uj ="";
		$hol = 1;

		foreach ($szoveg as $szov)
		{
			$uj .= $szov;

			if ($hol == $db)
			{
				$uj .= " ";
				$hol = 1;
			}
			else
			{
			if ($szov == " " or $szov == "\n") $hol = 1; else $hol++;
			}
		}
		
		return $uj;
	}
	
// *******************************************************************************
// KEP_VÁG - kép vágása méretezés után x és y méretre
// *******************************************************************************

	function kep_vag($foto_be, $foto_ki, $uj_x, $uj_y)
	{
		$size = getimagesize($foto_be);
	   
		if($size[2] == 1) $foto = ImageCreateFromGIF($foto_be);
		if($size[2] == 2) $foto = ImageCreateFromJPEG($foto_be);
		if($size[2] == 3) $foto = ImageCreateFromPNG($foto_be);

		$akt_x = imagesx($foto);
		$akt_y = imagesy($foto);
	 
		$arany_uj = $uj_x / $uj_y;
		$arany_akt = $akt_x / $akt_y;
	  
		if ($arany_uj < $arany_akt)   // a kép hosszúkásabb
		{
			// a magassághoz igazítunk 

			$arany = $akt_y / $uj_y;
			$akt_x_uj = $akt_x / $arany;
			$akt_y_uj = $uj_y;
			$x = ($akt_x_uj - $uj_x) / 2;
			$y = 0;
		}
		elseif ($arany_uj > $arany_akt)
		{
			$arany = $akt_x / $uj_x;
			$akt_y_uj = $akt_y / $arany;
			$akt_x_uj = $uj_x;
			$y = ($akt_y_uj - $uj_y) / 2;
			$x = 0;
		}
		else
		{
			$akt_x_uj = $uj_x;
			$akt_y_uj = $uj_y;
			$y = 0;
			$x = 0;
		}
	  
	  
		//echo $akt_x_uj." - ".$akt_y_uj." - ".$akt_x." - ".$akt_y;
	
		$foto_at = imagecreatetruecolor($akt_x_uj, $akt_y_uj);
		imagecopyresampled($foto_at, $foto, 0, 0, 0, 0, $akt_x_uj, $akt_y_uj, $akt_x, $akt_y);
		
		$foto_uj = imagecreatetruecolor($uj_x, $uj_y);
		imagecopyresampled($foto_uj, $foto_at, 0, 0, $x, $y, $uj_x, $uj_y, $uj_x, $uj_y);
		
		ImageJPEG($foto_uj, $foto_ki, 90);  
	}
}

?>
