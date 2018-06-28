<?php 

require_once('../../fl_core/autentication.php');
include('fl_settings.php'); // Variabili Modulo 
$id = check($_GET['id']);
$lead_id = (isset($_GET['lead_id'])) ? check($_GET['lead_id']) : 1;
$GeneraContratto = '#';
$GeneraAllegato = '#';

if($id > 1) { 

$evento = GRD($tabella,$id); 
$lead_id = $evento['lead_id'];
$tab_div_labels = array('id'=>'Scheda Evento','anagrafica_cliente'=>"Anagrafica Cliente",'../mod_menu_portate/mod_user.php?evento_id=[*ID*]'=>"Menù",'../mod_linee_prodotti/mod_user.php?categoria_prodotto=30&evento_id=[*ID*]'=>"Allestimenti della sala",'../mod_linee_prodotti/mod_user.php?categoria_prodotto=32&evento_id=[*ID*]'=>"Vini e Bevande",'../mod_linee_prodotti/mod_user.php?categoria_prodotto=33&evento_id=[*ID*]'=>"Servizi Aggiuntivi");
if($evento['anagrafica_cliente'] > 1)  $GeneraContratto = "mod_stampa.php?id=".$id;
$GeneraAllegato =  "mod_allegato1.php?evento_id=".$id;

}


include("../../fl_inc/headers.php");
if(!isset($_GET['goto'])) include("../../fl_inc/testata_mobile.php");

 ?>




<body style=" background: rgb(241, 241, 241) none repeat scroll 0% 0%;">
<div id="container" >
<div id="content_scheda">

<?php 


$status = '';

if($lead_id > 1) { 

$potential = GRD($tables[106],$lead_id); 

$colore = '';
if($potential['status_potential'] == 0) { $colore = "class=\" msg tab_blue\"";  }
if($potential['status_potential'] == 1) { $colore = "class=\"msg tab_orange\"";  }
if($potential['status_potential'] == 2) { $colore = "class=\"msg tab_orange\"";  }
if($potential['status_potential'] == 3) { $colore = "class=\"msg tab_red\"";  } 
if($potential['status_potential'] == 4)  { $colore = "class=\"msg tab_green\"";  }
if($potential['status_potential'] == 5)  { $colore = "class=\"msg tab_orange\"";  }
if($potential['status_potential'] == 6) { $colore = "class=\"msg tab_red\"";  } 
if($potential['status_potential'] == 7)  { $colore = "class=\"msg tab_orange\"";  }

$status = '<span '.$colore.'>'.$status_potential[$potential['status_potential']].'</span>';  


$changestatus = ($potential['status_potential'] < 2) ? 'status_potential = 1 , `in_use` = '.$_SESSION['number'].' ,' : '' ;
$telefono = phone_format($potential['telefono'],'39');

echo '<div class="info_dati"><h1 style="display: inline-block; margin: 0 0 5px;"><strong>'.$potential['nome'].' '.$potential['cognome'].'</strong></h1> ';
echo '<p style="margin: 5px 0px;"><i class="fa fa-phone" style="padding: 3px;"></i> <a href="tel:'.@$telefono.'" class="setAction" style="color: black;" data-gtx="'.base64_encode($tab_id).'" data-id="'.base64_encode($potential['id']).'" data-azione="2"  data-esito="2" data-note="Avvio Chiamata">'.@$telefono.'</a> <i class="fa fa-envelope-o" style="padding: 3px;"></i> <a style="color: black;" href="mailto:'.@$potential['email'].'" class="setAction" data-gtx="'.base64_encode($tab_id).'" data-id="'.base64_encode($potential['id']).'" data-azione="3"  data-esito="5"  data-note="Avvio Composizione Email">'.@$potential['email'].'</a></p>';
echo $status.'</div>';

} 

?>

<?php if($id > 1) { ?>

<div class="module_icon"><a href="<?php echo $GeneraContratto; ?>" title="Visualizza/Stampa Contratto" data-fancybox-type="iframe" class="fancybox_view"><i class="fa fa-file-text-o"></i> Contratto </a> </div>
<div class="module_icon"><a href="<?php echo $GeneraAllegato; ?>" title="Visualizza/Stampa Allegato" data-fancybox-type="iframe" class="fancybox_view"><i class="fa fa-file-text-o"></i> Allegato Contratto </a></div>
<div class="module_icon"><a href="../mod_anagrafica/?j=<?php echo base64_encode($lead_id); ?>&associa_evento=<?php echo $id; ?>"><i class="fa fa-plus-circle"></i> Crea Cliente </a> </div>

<?php } ?>

<?php if(isset($_GET['esito'])) { $class = (isset($_GET['success'])) ? 'green' : 'red'; echo '<p class="esito '.$class.'">'.check($_GET['esito']).'</p>'; }  ?>


<form id="scheda" action="../mod_basic/action_modifica.php" method="post" enctype="multipart/form-data">
<?php include('../mod_basic/action_estrai.php');  

 echo '<input type="hidden" name="reload" value="../mod_eventi/mod_inserisci.php?id=" />';


?>
</form>



<?php if(check($_GET['id']) != 1) { 
echo "<a  href=\"../mod_basic/action_elimina.php?POST_BACK_PAGE=../mod_appuntamenti/&gtx=$tab_id&amp;unset=".$id."\" title=\"Elimina\"  onclick=\"return conferma_del();\"><i class=\"fa fa-trash-o\"></i> Elimina </a>";
}


if(isset($_GET['lead_id'])) {
	
	$dataEvento = check($_GET['data_evento']);

	echo "<script type=\"text/javascript\">	
	$('#periodo_evento').val('".$potential['tipo_interesse']."');
	$('#tipo_evento').val('".$potential['interessato_a']."');
	$('#numero_adulti').val('".$potential['numero_persone']."');";
	if($potential['tipo_interesse'] == 102 && isset($_GET['data_evento']) ) {
	
	echo "$('#data_evento').val('".substr($dataEvento,8,2)."/".substr($dataEvento,5,2)."/".substr($dataEvento,0,4)." 21:00 ');";
	echo "$('#data_fine_evento').val('".substr($dataEvento,8,2)."/".substr($dataEvento,5,2)."/".substr($dataEvento,0,4)." 23:59 ');";
	} else {
	echo "$('#data_evento').val('".substr($dataEvento,8,2)."/".substr($dataEvento,5,2)."/".substr($dataEvento,0,4)." 13:00 ');";
	echo "$('#data_fine_evento').val('".substr($dataEvento,8,2)."/".substr($dataEvento,5,2)."/".substr($dataEvento,0,4)." 21:00 ');";

	}
	echo "</script>";
}
?>


</div></div></body></html>
