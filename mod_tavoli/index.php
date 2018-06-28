<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../fl_core/autentication.php');

include('fl_settings.php'); // Variabili Modulo 

 
include("../../fl_inc/headers.php");?>

<?php include('../../fl_inc/testata.php'); ?>
<?php include('../../fl_inc/menu.php'); ?>
<?php include('../../fl_inc/module_menu.php'); ?>


<?php /* Inclusione Pagina */
if(isset($_GET['action'])) { include($pagine[$_GET['action']]); } else { 


include("mod_home.php"); 
} 
include("../../fl_inc/footer.php"); ?>