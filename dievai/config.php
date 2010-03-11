<?php

/**
 * @Projektas: MightMedia TVS
 * @Puslapis: www.coders.lt
 * @$Author: p.dambrauskas $
 * @copyright CodeRS ©2008
 * @license GNU General Public License v2
 * @$Revision: 360 $
 * @$Date: 2009-11-20 17:27:27 +0200 (Fri, 20 Nov 2009) $
 **/

/*if (!defined("LEVEL") || LEVEL > 1 || !defined("OK") || $_SESSION['id'] != 1) {
	die($lang['system']['error']);
}*/

if (isset($_POST) && !empty($_POST) && isset($_POST['Konfiguracija'])) {
		$q = array();
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape($_POST['Apie']) . ",'Apie')  ON DUPLICATE KEY UPDATE `val`=" . escape($_POST['Apie'])."";
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(input(strip_tags($_POST['keywords']))) . ",'keywords')  ON DUPLICATE KEY UPDATE `val`=" . escape(input(strip_tags($_POST['keywords'])))."";
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(input(strip_tags($_POST['Pavadinimas']))) . ",'Pavadinimas')  ON DUPLICATE KEY UPDATE `val`=" . escape(input(strip_tags($_POST['Pavadinimas'])));
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape($_POST['Copyright']) . ",'Copyright')  ON DUPLICATE KEY UPDATE `val`=" . escape($_POST['Copyright']);
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(input(strip_tags($_POST['Pastas']))) . ",'Pastas')  ON DUPLICATE KEY UPDATE `val`=" . escape(input(strip_tags($_POST['Pastas'])));
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape((int)$_POST['Palaikymas']) . ",'Palaikymas')  ON DUPLICATE KEY UPDATE `val`=" . escape((int)$_POST['Palaikymas']);
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape((int)$_POST['News_limit']) . ",'News_limit')  ON DUPLICATE KEY UPDATE `val`=" . escape((int)$_POST['News_limit']);
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape($_POST['Maintenance']). ",'Maintenance')  ON DUPLICATE KEY UPDATE `val`=" . escape($_POST['Maintenance']);
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(input(strip_tags($_POST['Stilius']))) . ",'Stilius')  ON DUPLICATE KEY UPDATE `val`=" . escape(input(strip_tags($_POST['Stilius'])));
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(basename($_POST['pirminis'],'.php')) . ",'pirminis')  ON DUPLICATE KEY UPDATE `val`=" . escape(basename($_POST['pirminis'],'.php'));
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape(basename($_POST['kalba'])) . ",'kalba')  ON DUPLICATE KEY UPDATE `val`=" . escape(basename($_POST['kalba']));
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape((int)$_POST['keshas']) . ",'keshas')  ON DUPLICATE KEY UPDATE `val`=" . escape((int)$_POST['keshas']);
	$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape((int)$_POST['koment']) . ",'kmomentarai_sveciams')  ON DUPLICATE KEY UPDATE `val`=" . escape((int)$_POST['koment']);
	foreach ($q as $sql) {
		mysql_query1($sql);
	}
	delete_cache("SELECT id, reg_data, gim_data, login_data, nick, vardas, levelis, pavarde FROM `" . LENTELES_PRIESAGA . "users` WHERE levelis=1 OR levelis=2");
	redirect('?id,999;a,'.$_GET['a'].'');
}

$stiliai = getDirs(ROOT.'stiliai/', 'remontas');
$kalbos = getFiles(ROOT.'lang/');
foreach ($kalbos as $file) {
	if ($file['type'] == 'file') {
		$kalba[basename($file['name'])] = basename($file['name']);
	}
}
$puslapiai = array_keys($conf['puslapiai']);
foreach ($puslapiai as $key) {
	$psl[$key] = (isset($lang['pages'][$key])?$lang['pages'][$key]:nice_name(basename($key,'.php')));
}

$nustatymai = array("Form" => array("action" => "", "method" => "post", "enctype" => "", "id" => "", "class" => "", "name" => "reg"), 
	"{$lang['admin']['sitename']}:" => array("type" => "text", "value" => input($conf['Pavadinimas']), "name" => "Pavadinimas", "class" => "input"), 
	"{$lang['admin']['homepage']}:" => array("type" => "select", "value" => $psl, "selected" => input($conf['pirminis']), "name" => "pirminis", "class" => "select"), 
	//"{$lang['admin']['about']}:" => array("type" => "string", "value" => editorius('spaw', 'mini', 'Apie', (isset($conf['Apie']) ? $conf['Apie'] : '')), 
	"{$lang['admin']['about']}:" => array("type" => "textarea", "name" => "Apie", "value" => (isset($conf['Apie']) ? $conf['Apie'] : ''), "extra" => "rows=5", "class" => "input"), 
	"{$lang['admin']['keywords']}:" => array("type" => "text", "value" => input($conf['Keywords']), "name" => "keywords", "rows" => "3", "class" => "input"), 
	//"{$lang['admin']['generation']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}",	"{$lang['admin']['no']}" => "Ne"), "selected" => input($conf['Render']), "name" => "Render", "class" => "select"), 
	"{$lang['admin']['copyright']}:" => array("type" => "text", "value" => input($conf['Copyright']), "name" => "Copyright", "class" => "input"), 
	"{$lang['admin']['email']}:" => array("type" => "text", "value" => input($conf['Pastas']), "name" => "Pastas", "class" => "input"), 
	//"{$lang['admin']['allow registration']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}"), "selected" => input($conf['Registracija']), "name" => "Registracija", "class" => "select"), 
	"{$lang['admin']['maintenance']}?:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}"), "selected" => input($conf['Palaikymas']), "name" => "Palaikymas", "class" => "select"), 
	"{$lang['admin']['maintenancetext']}:" =>	array("type" => "textarea", "name" => "Maintenance", "value" => (isset($conf['Maintenance']) ? $conf['Maintenance'] : ''), "extra" => "rows=5", "class" => "input"), //"Kiek rodyti ChatBox pranešimu?:"=>array("type"=>"select","value"=>array("5"=>"5","10"=>"10","15"=>"15","20"=>"20","25"=>"25","30"=>"30","35"=>"35","40"=>"40"),"selected"=>input($conf['Chat_limit']),"name"=>"Chat_limit"),
	"{$lang['admin']['comm_guests']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}","3"=>"{$lang['admin']['comments_off']}"), "selected" => input(@$conf['kmomentarai_sveciams']), "name" => "koment", "class" => "select"), 
	"{$lang['admin']['newsperpage']}:" => array("type" => "text", "value" => input($conf['News_limit']), "name" => "News_limit", 'extra' => "on`key`up=\"javascript:this.value=this.value.replace(/[^0-9]/g, '');\"", "class" => "select"), 
	"{$lang['admin']['cache']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}"), "selected" => input($conf['keshas']), "name" => "keshas", "class" => "select"), 
	"{$lang['admin']['theme']}:" => array("type" => "select", "value" => $stiliai, "selected" => input($conf['Stilius']), "name" => "Stilius", "class" => "select"), 
	"{$lang['admin']['lang']}:" => array("type" => "select", "value" => $kalba, "selected" => input($conf['kalba']), "name" => "kalba", "class" => "select"), 
	"" => array("type" => "submit", "name" => "Konfiguracija", "value" => "{$lang['admin']['save']}", "class" => "submit")
);

//"Aprašymas:"=>array("type"=>"string","value"=>editorius('spaw','mini','Aprasymas',(isset($extra['aprasymas']))?$extra['aprasymas']:'')),

include_once (ROOT."priedai/class.php");
$bla = new forma();
lentele($lang['admin']['config'], $bla->form($nustatymai));
//unset($_POST['Konfiguracija']);

?>