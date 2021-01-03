<?php

session_set_cookie_params(0);
session_start();
header("Content-type: image/png");

class Captcha
{   
	private $font = array("../inc/font/acid.ttf", "../inc/font/sprocket.ttf", "../inc/font/binner.ttf");
	private $tomb = "0123456789";
	private $kep;
    private $kep_1;
	private $kep_2;
	private $kep_3;
	private $szin_1_r = 250;
	private $szin_1_g = 238;
	private $szin_1_b = 193;
	private $szin_2_r = 255;
	private $szin_2_g = 160;
	private $szin_2_b = 0;

	private $szum = 0;
	
    public function Captcha()
    {   
	    $this->kep_1 = imagecreate(30, 30);
		$this->kep_2 = imagecreate(30, 30);
		$this->kep_3 = imagecreate(30, 30);
		
		
        $this->get_kep($this->kep_1, 8);
		$this->get_kep($this->kep_2, 8);
		$this->get_kep_szum($this->kep_3, 0);
		
		$this->kep = imagecreate(90, 30);
		imageCopy($this->kep, $this->kep_1, 0, 0, 0, 0, 30, 30);
		imageCopy($this->kep, $this->kep_2, 60, 0, 0, 0, 30, 30);
		imageCopy($this->kep, $this->kep_3, 30, 0, 0, 0, 30, 30);

		$szin = imagecolorallocate($this->kep, 255, 255, 255); 
		
		imagepng($this->kep);
        imagedestroy($this->kep);
		
		$_SESSION["kapcsa"] = $this->szum;
    }

    private function get_kep($kep, $x)
    {
        $szam = substr($this->tomb, rand(0, 9), 1);

        $this->szum += $szam;

		$kep_betu = imagecreate (60, 30);
  
		$fok = mt_rand(-10, 10);
  
		$hatter = ImageColorAllocate($kep_betu, $this->szin_2_r, $this->szin_2_g, $this->szin_2_b);
		$szinem = ImageColorAllocate($kep_betu, $this->szin_1_r, $this->szin_1_g, $this->szin_1_b);
  
		$poz = mt_rand(18, 24);
  
		ImageTTFText($kep_betu, 16, $fok, 2, $poz, $szinem, $this->font[mt_rand(0,2)], $szam);
  
		imageCopy($kep, $kep_betu, $x, 0, 0, 0, 60, 30);
		imageDestroy ($kep_betu);
    }

	private function get_kep_szum($kep, $x)
    {
		$kep_szum = imagecreate(30, 30);
  
		$hatter = ImageColorAllocate($kep_szum, $this->szin_1_r, $this->szin_1_g, $this->szin_1_b);
		$szinem = ImageColorAllocate($kep_szum, $this->szin_2_r, $this->szin_2_g, $this->szin_2_b);
  
		imagefilledrectangle ( $kep_szum, 6, 14, 23, 16, $szinem);
		imagefilledrectangle ( $kep_szum, 14, 6, 16, 23, $szinem);
  
		imageCopy($kep, $kep_szum, $x, 0, 0, 0, 30, 30);
		imageDestroy ($kep_szum);
    }
}

$captcha = new Captcha;

?>
