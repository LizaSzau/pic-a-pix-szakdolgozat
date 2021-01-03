<?php

switch ($akt)
{
	case 'uzen_kuld':                                              
		$fajl = 'index';
		$smarty->assign('email_kontakt', Constants::$EMAIL_1);
		$smarty->assign('domain', Constants::$DOMAIN_1);
		$smarty->assign('telefon', Constants::$TELEFON);
		email_kuld();
		break;
	default:
		$fajl = 'index';
		$smarty->assign('email_kontakt', Constants::$EMAIL_1);
		$smarty->assign('domain', Constants::$DOMAIN_1);
		$smarty->assign('telefon', Constants::$TELEFON);
}

$smarty->assign('fajl', 'templates/'.$modul_akt.'/'.$fajl.'.html');

// *******************************************************************************
// EMAIL_KULD - e-mail küldése
// *******************************************************************************

function email_kuld()
{ 
	global $smarty;
	global $lang_m;
	global $nyelv;
	
	$post = array();
	
	foreach ($_POST as $kulcs => $ertek) 
	{ 
		$post[Utils::tisztit($kulcs)] = Utils::tisztit($ertek); 
	}

	// Kitöltési hibák
	if (!Utils::email_ell($post['email'])) $hiba[] = $lang_m[13]; 
	if ($post['nev'] == '') $hiba[] = $lang_m[14]; 
	if ($post['targy'] == '') $hiba[] = $lang_m[15]; 
	if ($post['tartalom'] == '') $hiba[] = $lang_m[16]; 
	if ($post['kapcsa'] != $_SESSION["kapcsa"]) $hiba[] = $lang_m[17]; 

	if (count($hiba) == 0)
	{ 
	
		$uzen = array();
		$uzen[]  = $lang_m[18]; ;  // köszi üzenet
		
		require_once("class/email_class.php");
		$email = new Email;	
		
		$email->set_nyelv($nyelv);
		$email->set_from_nev($post['nev']);
		$email->set_from_email($post['email']);
		$email->set_targy($post['targy']);
		$email->set_to_email(Constants::$EMAIL);
		$email->set_reply_email(Constants::$EMAIL);
		$email->set_targy($post['targy']);
		$email->set_tartalom($post['tartalom']);
		$email->level_1();
		

		if ($post['masolat'])
		{
			$email->set_from_email(Constants::$EMAIL);
			$email->set_reply_email($post['email']);
			$email->set_to_email($post['email']);
			$email->level_2();
		}
		
		$smarty->assign("uzen_tip", 2);
		$smarty->assign("uzen", $uzen);
	}
	else
	{
		foreach ($post as $kulcs => $ertek) 
		{ 
			$v = Utils::tisztit_1($kulcs);
			$$v = Utils::tisztit_1($ertek); 
			$smarty->assign($v, $ertek);
		}
		
		$smarty->assign("uzen", $hiba);
	}
}

?>
