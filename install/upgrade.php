<?php
/**
 * @Projektas: MightMedia TVS
 * @license GNU General Public License v2
 * @$Revision: 121 $
 * @$Date: 2009-05-23 17:22:13 +0300 (Št, 23 Geg 2009) $
 * @Apie: upgrade.php - TVS atnaujinimo įrankis
 * */
session_start();
header( "Content-type: text/html; charset=utf-8" );
ob_start();
$root     = '';
$out_page = TRUE;
$inc      = "priedai/conf.php";
while ( !file_exists( $root . $inc ) && strlen( $root ) < 70 ) {
	$root = "../" . $root;
}
if ( !defined( 'ROOT' ) ) {
	//_ROOT = $root;
	define( 'ROOT', '../' );
} else {
	define( 'ROOT', $root );
}
include_once( "" . ROOT . "priedai/conf.php" );
include_once( "" . ROOT . "priedai/prisijungimas.php" );
$versija        = versija();
$step           = 1;
$chmod_files[0] = "" . ROOT . "blokai";
//ką trinam
$delete_files[] = "" . ROOT . "paneles";

//į kokią versiją atnaujinam
/*// Sarašas failų kurių teisės turi suteikti svetainei įrašymo galimybę
	$chmod_files[0] = "" . ROOT . "siuntiniai/media";
	$chmod_files[]  = "" . ROOT . "sandeliukas";
	$chmod_files[]  = "" . ROOT . "images/avatars";
	//ką trinam
	$delete_files[] = "" . ROOT . "puslapiai/dievai/";
	$delete_files[] = "" . ROOT . "javascript/htmlarea/Xinha0.96beta2/";
	$delete_files[] = "" . ROOT . "javascript/forum/perview.php";*/
// Sarašas failų kurių teisės turi suteikti svetainei įrašymo galimybę

// Diegimo stadijų registravimas
if ( !isset( $_GET['step'] ) || empty( $_GET['step'] ) ) {
	$_SESSION['step'] = 1;
	$step             = 1;
} else {
	if ( $_GET['step'] != 0 && $_GET['step'] > 1 ) {
		$step = (int)$_GET['step'];
		if ( $_SESSION['step'] == ( $step - 1 ) ) {
			$_SESSION['step'] = $step;
		}
	} else {
		header( "Location: upgrade.php?step=" . $_SESSION['step'] );
	}
}

// Duomenų bazės prisijungimo tikrinimo ir lentelių sukūrimo dalis
if ( isset( $_POST['next_msyql'] ) ) {
	// Sukuriamos visos MySQL leneteles is SVN Trunk

	if ( !empty( $_POST['sql'] ) ) {
		$sql = ( file_exists( "" . ROOT . "sql-upgrade-beta.sql" ) ? file_get_contents( "" . ROOT . "sql-upgrade-beta.sql" ) : file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/install/sql-upgrade-beta.sql' ) );

		/*switch ( $_POST['sql'] ) {
								   case '1.28-1.3':
									   {
									   $sql = ( file_exists( "" . ROOT . "sql-upgrade.sql" ) ? file_get_contents( "" . ROOT . "sql-upgrade.sql" ) : file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade.sql' ) );
									   break;
									   }
								   case '1.3-1.4':
									   {
									   $sql = ( file_exists( "" . ROOT . "sql-upgrade-1.3to1.4.sql" ) ? file_get_contents( "" . ROOT . "sql-upgrade-1.3to1.4.sql" ) : file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql' ) );
									   break;
									   }

								   default:
									   {
									   if ( versija() >= '1.4' ) {
										   $sql = ( file_exists( "" . ROOT . "sql-upgrade-1.3to1.4.sql" ) ? file_get_contents( "" . ROOT . "sql-upgrade-1.3to1.4.sql" ) : file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql' ) );
									   } elseif ( versija() >= '1.3' ) {
										   $sql = ( file_exists( "" . ROOT . "sql-upgrade.sql" ) ? file_get_contents( "" . ROOT . "sql-upgrade.sql" ) : file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade.sql' ) );
									   }
									   break;
									   }*/
	} else {
		/*	if ( !file_exists( "" . ROOT . "sql-upgrade-1.3to1.4.sql" ) ) {
					  $sql = file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql' );
				  } else {
					  $sql = file_get_contents( "" . ROOT . "sql-upgrade-1.3to1.4.sql" );
				  }*/
		if ( !file_exists( "" . ROOT . "sql-upgrade-beta.sql" ) ) {
			$sql = file_get_contents( 'http://code.assembla.com/mightmedia/subversion/node/blob/v1/install/sql-upgrade-beta.sql' );
		} else {
			$sql = file_get_contents( "" . ROOT . "sql-upgrade-beta.sql" );
		}
	}

	// Paruošiam užklausas
	$sql = str_replace( "CREATE TABLE IF NOT EXISTS `", "CREATE TABLE IF NOT EXISTS `" . LENTELES_PRIESAGA, $sql );
	$sql = str_replace( "CREATE TABLE `", "CREATE TABLE IF NOT EXISTS `" . LENTELES_PRIESAGA, $sql );
	$sql = str_replace( "INSERT INTO `", "INSERT INTO `" . LENTELES_PRIESAGA, $sql );
	$sql = str_replace( "ALTER TABLE `", "ALTER TABLE `" . LENTELES_PRIESAGA, $sql );
	$sql = str_replace( "UPDATE `", "UPDATE `" . LENTELES_PRIESAGA, $sql );

	// Prisijungiam prie duombazės
	mysqli_query( $prisijungimas_prie_mysql, "SET NAMES utf8" );

	// Atliekam SQL apvalymą
	$match = '';
	preg_match_all( "/(?:CREATE|UPDATE|INSERT|ALTER).*?;[\r\n]/s", $sql, $match );

	$mysql_info  = "<ol>";
	$mysql_error = 0;
	foreach ( $match[0] as $key => $val ) {
		if ( !empty( $val ) ) {
			$query = mysqli_query( $prisijungimas_prie_mysql, $val );
			if ( !$query ) {
				$mysql_info .= "<li><b>Klaida:" . mysqli_errno($prisijungimas_prie_mysql) . "</b> " . mysqli_error($prisijungimas_prie_mysql) . "<hr><b>Užklausa:</b><br/>" . $val . "</li><hr>";
				$mysql_error++;
			}
		}
	}
	$mysql_info .= "</ol>";

	if ( $mysql_error == 0 ) {
		$mysql_info = 'Lentelės sėkmingai atnaujintos. Galite tęsti atnaujinimą.';
		$next_mysql = '<center><input type="reset" value="Toliau" onClick="Go(\'3\');" class="submit"></center>';
	} else {
		$next_mysql = '<center><input type="reset" value="Bandyti dar kartą" onClick="Go(\'2\');" class="submit"></center>';
	}

}


if ( !isset( $next_mysql ) ) {
	/*$next_mysql = '<select name="sql">
			<option value="1.28-1.3">1.28 atnaujinimas į 1.3</option>
			<option value="1.3-1.4">1.3 atnaujinimas į 1.4</option>
			</select>
			<br />';*/
	$next_mysql = '<form method="post" action="" name="sql">
			1.4 atnaujinimas į 1.46
			<br />';
	$next_mysql .= '<input name="next_msyql" type="submit" value="Atnaujinti lenteles" class="submit" style="margin-top: 5px;">
	</form>
	';
}

// Administratoriaus sukūrimo dalis
// Diegimo pabaiga
if ( !empty( $_POST['finish'] ) ) {

	unlink( "upgrade.php" );
	//}
	header( "Location: " . ROOT . "index.php" );
	//}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="resource-type" content="document" />
	<meta name="distribution" content="global" />
	<meta name="author" content="CodeRS - MightMedia TVS" />
	<meta name="copyright" content="copyright (c) by CodeRS www.coders.lt" />
	<meta name="rating" content="general" />
	<meta name="generator" content="notepad" />
	<link rel="shortcut icon" href="<?php echo ROOT; ?>images/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="<?php echo ROOT; ?>images/favicon.ico" type="image/x-icon" />
	<script src="<?php echo ROOT; ?>javascript/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script src="<?php echo ROOT; ?>javascript/jquery/tooltip.js" type="text/javascript"></script>
	<title>MightMedia TVS/CMS</title>
	<link rel="stylesheet" type="text/css" media="all" href="default.css" />
</head>
<body>
<body>
<div id="plotis">
<div id="kaire">
	<div class="skalpas"><a href="?" title="<?php echo adresas(); ?>">
		<div class="logo"></div>
	</a></div>


	<div class='pavadinimas'>Įdiegimo stadijos</div>
	<div class='vidus'>
		<div class='text'>
			<ul>
				<?php
				$menu_pavad = array( 1 => "Failų tikrinimas", 2 => "Duomenų bazės atnaujinimas", 3 => "Failų keitimas", 4 => "Pabaiga" );
				foreach ( $menu_pavad as $key => $value ) {
					if ( $key <= $step ) {
						echo "\t\t\t<li><img src=\"" . ROOT . "images/icons/tick_circle.png\" style=\"vertical-align: middle;\" /><font color=\"green\"><b>" . $value . "</b></font></li>";
					} else {
						echo "\t\t\t<li><img src=\"" . ROOT . "images/icons/cross_circle.png\" style=\"vertical-align: middle;\" /><b>" . $value . "</b></li>";
					}
				}
				?>
			</ul>
		</div>
	</div>
</div>
<div id="kunas">
<div id="meniu_juosta">MightMedia TVS atnaujinimas</div>
<div id="centras">

<?php if ( versija() < $versija ) {
	echo "<div class=\"msg\"><b>Nepakeisti senosios versijos failai!</b><br />
<small>Naudodamiesi FTP naršykle, atnaujinkite senos <b>" . versija() . "</b> versijos failus į naująją <b>{$versija}</b> versiją.</div>";
}
?>
<?php
// HTML DALIS - Failų tikrinimas
if ( $step == 1 ) {
	?>
<div class='pavadinimas'>Failų tikrinimas</div>
<div class='vidus'>
<div class='text'>
	<h2 style="color: red;">Dėmesio, nepamirškite pasidaryti pilnas failų ir MySql duomenų bazės kopijas </h2>
	Žemiau surašyti failai, kurių keitimas bus reikalingas atnaujinant
	šią sistemą. Jei sistema surado klaidų prašome jas ištaisyti ir
	spausti atnaujinti. Kitu atveju jums nebus leidžiama tęsti įdiegimo.
	<br />
	<br />
	<h2>Legenda</h2>
	<img src="<?php echo ROOT; ?>images/icons/tick.png" /> Jei prie failo nustatyta ši
	ikonėlė vadinasi failas yra paruoštas sistemai.<br />
	<img src="<?php echo ROOT; ?>images/icons/cross.png" /> Jei rasite šią ikonėlę prie
	nurodyto failo tuomet reikia atlikti užduotį, aprašytą „Klaidos
	aprašymas“ stulpelyje.
	<br />
	<br />
	<h2>Dėmesio, nepamirškite visų blokų iš "paneles" direktorijos perkelti į "blokai" direktoriją</h2>
	<table border="0" class="table">

		<tr>
			<th class="th" valign="top" width="10%">Failas</th>
			<th class="th" valign="top" width="5%">Būsena</th>
			<th class="th" valign="top" width="35%">Klaidos aprašymas</th>
		</tr>
		<?php
		$kartot = count( $chmod_files ) - 1;
		for ( $i = 0; $i <= $kartot; $i++ ) {
			$teises = substr( sprintf( '%o', fileperms( $chmod_files[$i] ) ), -4 );
			if ( $teises != 777 && $teises != 666 && !is_writable( $chmod_files[$i] ) ) {
				$file_error = 'Y';
			}
			echo "
<tr class=\"tr\">
<td>" . $chmod_files[$i] . "</td>
<td>" . ( ( $teises == 777 ) || ( $teises == 666 ) || is_writable( $chmod_files[$i] ) ? "<img src=\"" . ROOT . "images/icons/tick.png\" />" : "<img src=\"" . ROOT . "images/icons/cross.png\" />" ) . "</td>
<td>" . ( ( $teises == 777 ) || ( $teises == 666 ) || is_writable( $chmod_files[$i] ) ? "-" : "Būtina nurodyti chmod 777 failui <strong>" . $chmod_files[$i] . "</strong> kadangi esamas chmod yra <strong>" . $teises . "</strong>" ) . "</td>
</tr>";
		}
		foreach ( $delete_files as $file ) {
			if ( file_exists( $file ) ) {
				$file_error = 'Y';
			}
			echo "
<tr class=\"tr\">
<td>" . $file . "</td>
<td>" . ( !file_exists( $file ) ? "<img src=\"" . ROOT . "images/icons/tick.png\" />" : "<img src=\"" . ROOT . "images/icons/cross.png\" />" ) . "</td>
<td>" . ( !file_exists( $file ) ? "-" : "Būtina ištrinti <strong>" . $file . "</strong> " ) . "</td>
</tr>";
		}
		?>
	</table>
	<br />
	<br />
	<?php
	if ( isset( $file_error ) && $file_error == 'Y' ) {
		echo '<center><input type="reset" class="submit" value="Atnaujinti" onClick="JavaScript:location.reload(true);"> <input type="reset" class="submit" value="Jeigu esate isitikines, kad viskas gerai" onClick="Go(\'2\');"><center>';
	} else {
		echo '<center><input type="reset" class="submit" value="Toliau" onClick="Go(\'2\');"></center>';
	}
}
//END
// HTML DALIS - MySQL duomenų bazės nustatymai
if ( $step == 2 ) {
	?>
	<div class='pavadinimas'>MySQL Duomenų bazės atnaujinimas</div>
<div class='vidus'>
<div class='text'>
Norėdami atnaujinti lenteles, spauskite mygtuką, esantį žemiau.
	<form name="mysql" method="post" action="?step=2">
		<p id="mysql_response" style="text-align: center"><?php echo $next_mysql; ?></p>
		<?php if ( isset( $mysql_info ) ): ?> <br />
		<table border="0" width="50%">
			<tr>
				<td class="title"><b>Informacija</b></td>
			</tr>
			<tr>
				<td>
					<div id="info"><?php echo $mysql_info; ?></div>
				</td>
			</tr>
		</table>
		<?php endif; ?></form>
	<?php
}
//END
// HTML DALIS - TVS administratoriaus sukūrimas
if ( $step == 3 ) {
	?>
	<div class='pavadinimas'>Failų keitimas</div>
<div class='vidus'>
<div class='text'>
		<h2>Legenda</h2>
	<img src="<?php echo ROOT; ?>images/icons/tick.png" /> Jei prie užduoties matote šią
	ikoną, ji atlikta teisingai. <br />
	<img src="<?php echo ROOT; ?>images/icons/cross.png" /> Jei prie užduoties matote šią
	ikoną, ji atlikta neteisingai. <br />
	<hr />
	<br />
	<h2>priedai/conf.php failo keitimas</h2>
	<div class="tr">
		<img src="<?php $amslpts = SLAPTAS; echo ( isset( $amslpts ) ? "" . ROOT . "images/icons/tick.png" : "" . ROOT . "images/icons/cross.png" ); ?>" />
		Prieš <br />
		<textarea rows="2" cols="100">//Admin paneles vartotojas ir slaptazodis</textarea>
		<br />
		įklijuokite šį kodą
		<br />
		<textarea rows="2" cols="100">define('SLAPTAS', $slaptas);</textarea>
	</div>
	<div class="tr2">
		<img src="<?php echo ( isset( $_SESSION[SLAPTAS]['lang'] ) ? "" . ROOT . "images/icons/tick.png" : "" . ROOT . "images/icons/cross.png" ); ?>" />
		Apie 32 eilutėje, vietoje šio<br />
		<textarea rows="7" cols="100">require_once (realpath(dirname(__file__)) . &#039;/../lang/&#039; . (empty($_SESSION[&#039;lang&#039;])?basename($conf[&#039;kalba&#039;],&#039;.php&#039;):$_SESSION[&#039;lang&#039;]). &#039;.php &#039;);</textarea>
		<br />
		įklijuokite šį kodą
		<br />
		<textarea rows="7" cols="100">require_once (realpath(dirname(__file__)) . &#039;/../lang/&#039; . (empty($_SESSION[SLAPTAS][&#039;lang&#039;])?basename($conf[&#039;kalba&#039;],&#039;.php&#039;):$_SESSION[SLAPTAS][&#039;lang&#039;]). &#039;.php &#039;);</textarea>
	</div>

	<h2>stiliai/<?php echo $conf['Stilius'];?>/index.php failo keitimas</h2>
	<div class="tr">
		Vietoje
		<br />
		<textarea rows="2" cols="100">include ( "priedai/kairespaneles.php" );</textarea>
		<br />
		įklijuokite šį kodą
		<br />
		<textarea rows="2" cols="100">include "priedai/kaires_blokai.php";</textarea>
		<br />
		Vietoje
		<br />
		<textarea rows="2" cols="100">include ( "priedai/desinespaneles.php" );</textarea>
		<br />
		įklijuokite šį kodą
		<br />
		<textarea rows="2" cols="100">include "priedai/desines_blokai.php";</textarea>
		<br />
		Virš
		<br />
		<textarea rows="2" cols="100">include ( $page . ".php" );</textarea>
		<br />
		įklijuokite šį kodą
		<br />
		<textarea rows="2" cols="100">include "priedai/centro_blokai.php";</textarea>
		<br />
	</div>
	<div class="tr2">
		Atlikę šias užduotis spauskite „Atnaujinti“ ir
		įsitikinkite, kad užduotys atliktos teisingai (žr. į ikonas šalia
		užduoties)
	</div>
	<br />
	<center>
		<input type="reset" value="Atnaujinti" class="submit" onclick="JavaScript:location.reload(true);" />
		<input type="reset" value="Toliau" class="submit" onclick="Go('4');" />
	</center>
	<?php
}
// HTML DALIS - Pabaiga
if ( $step == 4 ) {
	?>
	<div class='pavadinimas'>Pabaiga</div>
<div class='vidus'>
<div class='text'>
Sveikiname įdiegus MightMedia TVS (Turinio Valdymo Sistemą).<br />
	Spauskite "Pabaigti" galutinai užbaigti instaliaciją. Bus ištrintas <b>upgrade.php</b>
	failas. Prisijungę prie FTP serverio būtinai ištrinkite "install" direktoriją.<br />
	<br />
	<form name="finish_install" method="post">
		<center>
			<input name="finish" type="submit" value="Pabaigti" class="submit" />
		</center>
	</form>
	<?php
}
//END
?>

	<script type="text/javascript">
		function Go(id) {
			document.location.href = "upgrade.php?step=" + id;
		}
	</script>

</div>
</div>
</div>
	<div class="sonas"></div>
	<div id="kojos">
		<div class="tekstas">MightMedia CMS / MightMedia TVS</div>
		<a href="http://mightmedia.lt" target="_blank" title="Mightmedia">
			<div class="logo"></div>
		</a>
	</div>
</div>
</div>
	<div id="another" class="clear">
		<div class="lygiuojam">
			<div class="taisom"></div>
		</div>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>