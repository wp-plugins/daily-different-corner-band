<?php
/*
Plugin Name: Daily Different Corner Band
Plugin URI: http://www.dastan.biz/daily-different-corner-band/
Description: Different corner bands with different special days.
Author: Cem DASTAN
Version: 1.0
Author URI: http://www.dastan.biz/
*/

/* Create Database */
$wpdb->ddcb = $wpdb->prefix . 'ddcb';
/* 
Tablomuzun ad� ipucu olup, bunu $wpdb isimli
WP'nin veritaban� s�n�f�na $wpdb->ipucu olarak tan�t�yoruz
*/
function ddcb_install() {
	/* Kurulum i�lemini yapacak olan fonksiyonumuz, ismini istedi�iniz gibi verebilirsiniz*/
	global $wpdb;
	/* $wpdb adl�  WP'nin veritaban� s�n�f�n� fonksiyonumuza �a��r�yoruz. Fonksiyonlar�m�zda veritaban� i�lemleri yapmak i�in bunu yapmam�z gerekiyor.*/
	
	$db_sql="CREATE TABLE IF NOT EXISTS `$wpdb->ddcb` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `showdate` VARCHAR(4) NOT NULL ,
		  `bandname` VARCHAR(100) NOT NULL ,
		  `link` VARCHAR(100) NOT NULL ,
		  `bandfile` VARCHAR(100) NOT NULL ,
		   PRIMARY KEY  (`id`)
		)";
	/* ID ve ipucumetin isimli iki alana sahip, ismi ipucu olan tablomuzu olu�turacak SQL ifadesi*/
	$wpdb->query($db_sql);
	/* SQL ifademizi �al��t�r�yoruz */
	add_option('ddcb_show_on_site', 'no');
	/* �pu�lar�m�z�n yaz� sonlar�n g�sterilip/g�sterilmeyece�ini belirleyece�imiz se�ene�imizi WP'nin options tablosuna "hayir" de�erini ve "gunun_ipucu_yazida" isminde kaydediyoruz. Daha sonra bunu kullanaca��z */
}
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	/* E�er kullan�c� "Etkinle�tir" ba�lant�s�na t�klad�ysa, "ddcb_install()" fonksiyonunu �a��r */
	add_action('init', 'ddcb_install');
}

//Admin Panel - Yonetim Paneli Olusturma
add_action('admin_menu', 'add_to_administrator');
function add_to_administrator() {
	add_submenu_page('options-general.php', 'Daily Different Corner Band', 'Daily Different Corner Band', 10, __FILE__, 'ddcb_menu');
}

function ddcb_menu() {
	global $wpdb;
	// Veritaban�n� kullanaca��m�z i�in $wpdb s�n�f�n� global olarak tan�ml�yoruz.
	echo '<div class="wrap">';
	//wrap isimli stil klas� y�netim panelindeki i�erik b�l�m�n� g�steriyor
	if ($_POST['islem']== 'add') { ddcb_add (); }
	if ($_GET['islem']== 'delete') { ddcb_delete (); }
	if ($_POST['islem']== 'showsite') { ddcb_show_on_site (); }
	/* Yukar�daki kontroller ile POST ya da GET'den gelen "islem" de�i�keninin
	de�erine g�re ilgili i�lemi yapacak fonksiyonlara dallan�yoruz */
	$ddcbquery = "SELECT * FROM $wpdb->ddcb order by id desc";
	$ddcbresults = $wpdb->get_results($ddcbquery);
	/* �pu�lar�n� listelemek i�in veritaban�ndan al�yoruz. Ve a�a��daki kodlarla
	d�zenli liste �eklinde bir liste olu�turuyor, ipu�lar�n�n sonlar�na da
	i�lem yapacak linkleri ekliyoruz.*/
	$wud = wp_upload_dir();
	
	 if ($ddcbresults) {
		echo "<strong>Corner Band(s):</strong>";
		echo "<ol>";
		foreach ($ddcbresults as $ddcbresult) {
			$metin=stripslashes($ddcbresult->bandname);
	echo "<li>".$ddcbresult->showdate." : ".$metin;
	echo "- [<a href='".$_SERVER['PHP_SELF']
		."?page=ddcb.php&islem=delete&silno=".$ddcbresult->id."'>Delete</a>]<br/>";
	echo '<img src="'.$wud["baseurl"].'/'.$ddcbresult->bandfile.'">';
	echo "<hr/>";
		}
		echo "</ol>";
	} else { echo "Not found any band file!"; }
	// E�er d�zenleme i�lemi yap�lmak istenmemi�se bo� bir metin kutusu olu�turuyoruz.
	
?>
<br/>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=ddcb.php" method="post">
	   <fieldset style="border:1px solid #000000;width:400px;">
	   <legend><b>Show ON/OFF</b></legend>
	 <table width="400">
		 <tr><td><input type="submit" name="submit" value="<?php
		/* E�er gunun_ipucu_yazida isimli se�ene�in durumuna g�re se�enek
			d��memize isim veriyoruz*/
		   if (get_option('ddcb_show_on_site') == 'no') {echo "Show corner band on blog.";
 }else{ echo "Don't show corner band on blog.";} ?>" class="button" /></td></tr>
	 </table>
		  <INPUT TYPE="hidden" name="islem" value="showsite"></p>
	   </fieldset>
	 </form>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=ddcb.php" method="post" enctype="multipart/form-data">
		<fieldset style="border:1px solid #000000;width:400px;">
		<legend><b>New Corner Band</b></legend>
		<table width="400">
			<tr><td width="400" colspan="2"></td></tr>
			<tr><td>Corner Band Name:</td><td><input type="text" name="title" id="title" tabindex="4"></td></tr>
			<tr><td>Corner Band Link:</td><td><input type="text" name="link" id="link" tabindex="5"></td></tr>
			<tr><td>Corner Band File:</td><td><input type="file" name="file" id="file" tabindex="6"></td></tr>
			<tr><td>Show on day <i>(dd/mm)</i>:</td><td>
			<select name="day">
				<option >01</option>
				<option >02</option>
				<option >03</option>
				<option >04</option>
				<option >05</option>
				<option >06</option>
				<option >07</option>
				<option >08</option>
				<option >09</option>
				<option >10</option>
				<option >11</option>
				<option >12</option>
				<option >13</option>
				<option >14</option>
				<option >15</option>
				<option >16</option>
				<option >17</option>
				<option >18</option>
				<option >19</option>
				<option >20</option>
				<option >21</option>
				<option >22</option>
				<option >23</option>
				<option >24</option>
				<option >25</option>
				<option >26</option>
				<option >27</option>
				<option >28</option>
				<option >29</option>
				<option >30</option>
				<option >31</option>
			</select>
			<select name="month">
				<option >01</option>
				<option >02</option>
				<option >03</option>
				<option >04</option>
				<option >05</option>
				<option >06</option>
				<option >07</option>
				<option >08</option>
				<option >09</option>
				<option >10</option>
				<option >11</option>
				<option >12</option>
			</select>
			</td></tr>
			<tr><td><input type="submit" name="submit" value="Add this corner band" class="button" tabindex="7" /></td></tr>
		</table>
			<INPUT TYPE="hidden" name="islem" value="add"></p>
		</fieldset>
	</form>
<?php
		echo "</div>";
} // Fonksiyonun sonu

//Yeni - New
function ddcb_add (){
	global $wpdb ;
//A�a��daki kod ile POST'dan gelen metni mySQL a��s�ndan zarars�z hale getiriyoruz
// Say�sal verilerde ise (int) ifadesi ile zarars�z hale getirebiliyoruz.
	$metin=$wpdb->escape($_POST['title']);
	$day=$wpdb->escape($_POST['day']);
	$month=$wpdb->escape($_POST['month']);
	$link=$wpdb->escape($_POST['link']);
	
	
	$sorgu = "SELECT * FROM $wpdb->ddcb where showdate='".$day.$month."'";
	$sonuclar = $wpdb->get_results($sorgu);
	if (count($sonuclar)=="1"){
?>
		<div id="message" class="updated fade"><p>Your blog has a corner band for selected day and month. Please check and try another date. </p></div>
<?php	
}else{
		
	if( $_FILES['file']['error'] == 0 ) {
		$size 			= floor( $_FILES['file']['size'] / (1024*1024) );
		$mime 			= $_FILES['file']['type'];
		$name 			= $_FILES['file']['name'];
		$temp 			= $_FILES['file']['tmp_name'];
		
		move_uploaded_file( $_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/".$_FILES["file"]["name"] );
		
			$sql= "INSERT INTO ".$wpdb->ddcb." VALUES (NULL,'".$day.$month."', '".$metin."', '".$link."', '".$_FILES["file"]["name"]."')";
			$wpdb->query($sql);
			//A�a��daki koddaki "updated fade" klas� uyar� mesajlar�na fade efekti uyguluyor.
?>
			<div id="message" class="updated fade"><p>New corner band has been added. </p></div>
<?php
	}
}
}
//Delete
function ddcb_delete () {
	 global $wpdb, $_GET ;
	 $sql="DELETE FROM ".$wpdb->ddcb." WHERE id='".(int) $_GET['silno']."'";
	 $sonuc=$wpdb->query($sql);?>
	<div id="message" class="updated fade"><p><strong>Selected corner band has been deleted.</strong></p></div>
<?php
}
function ddcb_show_on_site() {
	if (get_option('ddcb_show_on_site')=='no') {
   update_option('ddcb_show_on_site', 'yes');
?>
   <div id="message" class="updated fade"><p><strong>Corner band(s) will be show on blog</strong></p></div>
<?php
	} else {
	   update_option('ddcb_show_on_site', 'no');
?>
<div id="message" class="updated fade"><p><strong>Corner band(s) will NOT be show on blog</strong></p></div>
<?php
	}
 }

 
 // Ana fonksiyon - Main Function
function ddcb_show_now(){
	global $wpdb;
	if (get_option('ddcb_show_on_site')=='yes') {
		$today = date("d").date("m");
		$sorgu = "SELECT * FROM $wpdb->ddcb where showdate='".$today."'";
		$sonuclar = $wpdb->get_results($sorgu);
		if (count($sonuclar)=="1") {
		   foreach ($sonuclar as $sonuc) { $baslik = $sonuc->bandname; $file = get_option('home')."/wp-content/uploads/".$sonuc->bandfile; $link = $sonuc->link; }
		   echo "<script language=\"javascript\">document.write(unescape('%3Cdiv%20style%3D%22position%3A%20absolute%3B%20top%3A%200px%3B%20right%3A%200px%22%3E%3Cmap%20name%3D%22ribbonmap%22%3E%3Carea%20href%3D%22".$link."%22%20shape%3D%22polygon%22%20coords%3D%223%2C%203%2C%20162%2C%20162%2C%20162%2C%20129%2C%2032%2C%200%22%20target%3D_blank%3E%3C%2Fmap%3E%3Cimg%20src%3D%22".$file."%22%20border%3D0%20usemap%3D%22%23ribbonmap%22%3E%3C%2Fdiv%3E'));</script>";
		}
	}
}

add_filter('wp_head', 'ddcb_show_now');
?>