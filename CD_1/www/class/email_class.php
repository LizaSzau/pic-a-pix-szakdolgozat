<?php 

class Email
{   
	private $from_nev;  
	private $from_email;  
	private $reply_email;
	private $to_email;  
	private $to_nev;  
	private $targy;
	private $tartalom;
	private $domain;
	private $domain_nev;
	private $link;
	private $nyelv;
	private $text_m = array();
	
    public function __construct() 
	{
		$this->domain = Constants::$DOMAIN_1;
		$this->domain_nev = Constants::$NEV;
	   
		require_once("inc/PHP_mailer/class.phpmailer.php");
		$this->mail = new PHPMailer();
    } 
	
// *******************************************************************************
// SET_*
// *******************************************************************************

	public function set_from_nev($from_nev) { $this->from_nev = $from_nev; }
	public function set_from_email($from_email) { $this->from_email = $from_email; }
	public function set_reply_email($reply_email) { $this->reply_email = $reply_email; }
	public function set_to_email($to_email) { $this->to_email = $to_email; }
	public function set_to_nev($to_nev) { $this->to_nev = $to_nev; }
	public function set_targy($targy) { $this->targy = $targy; }
	public function set_tartalom($tartalom) { $this->tartalom = $tartalom; }
	public function set_link($link) { $this->link = $link; }
	public function set_nyelv($nyelv) { $this->nyelv = $nyelv; }
	
// *******************************************************************************
// LEVEL_KULD - levél elküldése
// *******************************************************************************

	private function level_kuld()
	{ 
		/*
		echo $this->from_email."<br>";
		echo $this->from_nev."<br>";
		echo $this->reply_email."<br>";
		echo $this->to_email."<br>";
		echo $this->targy."<br>";
		echo $this->tartalom."<br>";
		echo "-------------------<br>";
		*/
		//echo $this->link;
		
		$this->mail->IsHTML(false); 
		$this->mail->CharSet = 'utf-8';
		$this->mail->From = $this->from_email;
		$this->mail->FromName = $this->from_nev; 
		$this->mail->ReplyTo = $this->reply_email;
		$this->mail->AddAddress($this->to_email);
		$this->mail->Subject = $this->targy;
		$this->mail->Body = $this->tartalom; 
		$this->mail->Send();
		$this->mail->ClearAddresses();	
	}
	
// *******************************************************************************
// LEVEL_1 - üzenet a honlapról
// *******************************************************************************

	public function level_1()
	{ 
		$this->lang_init();
		$text_m = $this->text_m;

		$this->tartalom = <<< EOM
$text_m[1] $this->from_nev
$text_m[2] $this->from_email
$text_m[3] $this->targy

$this->tartalom
EOM;
		$this->level_kuld();
	}
	
// *******************************************************************************
// LEVEL_2 - üzenet a honlapról, másolat a feladónak
// *******************************************************************************

	public function level_2()
	{
		$this->lang_init();
		$text_m = $this->text_m;
		
		$this->tartalom = <<< EOM
$text_m[4] $this->from_nev !

$text_m[5]

$this->tartalom

$text_m[6]
$this->domain_nev
$this->domain
EOM;
		$this->level_kuld();
	}

// *******************************************************************************
// LEVEL_3 - regisztráció
// *******************************************************************************

	public function level_3()
	{
		$this->lang_init();
		$text_m = $this->text_m;
		$this->targy = $text_m[11];
		
		$this->tartalom = <<< EOM
$text_m[4] $this->to_nev !

$text_m[7]

$text_m[8]
      
$this->link
       
$text_m[10]

$text_m[6]
$this->domain_nev
$this->domain

EOM;
		$this->level_kuld();
	}
	
// *******************************************************************************
// LEVEL_4 - elfelejtett jelszó
// *******************************************************************************

	public function level_4()
	{
		$this->lang_init();
		$text_m = $this->text_m;
	echo	$this->targy = $text_m[14];
		
		$this->tartalom = <<< EOM
$text_m[12]

$text_m[13] $this->tartalom

$text_m[6]
$this->domain_nev
$this->domain

EOM;
		$this->level_kuld();
	}
	
// *******************************************************************************
// LANG_INIT - fordítás beállítása
// *******************************************************************************

	private function lang_init()
	{
		$json = file_get_contents('lang/mail_'.$this->nyelv.'.json');
		$json = json_decode($json, true); 

		$i = 1;

		foreach ($json as $kulcs => $ertek) 
		{ 
			$t = $ertek;
			foreach ($t as $kulcs => $ertek) { $this->text_m[$i] = $ertek; }
			$i++;
		}
	}
}

?>
