<?php

require_once('../../fl_core/autentication.php');
include('fl_settings.php'); // Variabili Modulo 
$evento_id = check($_GET['evento_id']); 
$evento = GRD($tabella,$evento_id);
$filename = 'Lista-verifica-'.$evento_id.'.pdf';

require_once('../../fl_set/librerie/html2pdf/html2pdf.class.php');


// get the HTML
ob_start(); ?>


<style type="text/css">
body { 

	border: none;
	background: #FFFFFF;
	padding: 4mm;
	color: #7A7A7A;
	font-size: 14px;
	text-align: center;
	font-family: sans-serif;
	line-height: 160%;
}
.elaborathio { color: #7A7A7A;  }
h2 { font-size: 14px; margin-bottom: 5px; }
h1 { font-size: 18px; }
.preventivo {}
.preventivo label { display:  inline-block; width:  30%; margin: 2px 0 ; }
.privacytxt { font-size: 7pt; text-align: center; }




.deco-title, .small_paragraf h1, .small_paragraf h2, .small_paragraf h3, .small_paragraf h4 { 
  font-size: 9pt;
 }
.small_paragraf {   font-size: 8pt; }

.dati, .dati2 {
	border-spacing: 2px;
	border-collapse: separate;
	caption-side: top;
	width: 100%;
	min-width: 600px;
	max-width: 100%;
	height: auto;
	margin: 20px auto 20px auto;
	padding: 0px;
	vertical-align: middle;
	border: none;
	background: white;
}
.dati a {
	color: #6190D5;
}
.hometab {
	border-spacing: 2px;
	border-collapse: separate;
	caption-side: top;
	width: 45%;
	float: left;
	margin: 10px;
	height: auto;
	margin: 20px auto 20px 30px;
	padding: 0px;
	vertical-align: middle;
	font-size: 12px;
	border: none;
}
.hometab h2 {
	color: rgba(65,65,65,1);
}
.hometab td {
	vertical-align: middle;
	margin: 0px;
	padding: 1px;
	border: 1px solid #F4F4F4;
}
.hometab .bgcolor {
	background: #F2F2F2;
	padding: 0px 4px;
}
.dati th {
	text-align: left;
	height: 25px;
	vertical-align: middle;
	color: rgba(65,65,65,1);
	padding: 8px 4px;
	margin: 0px;
	font-weight: normal;
}
.dati td {
	text-align: left;
	vertical-align: middle;
	margin: 0px;
	padding: 8px 4px;
	min-height: 20px;
	max-width: 400px;
	overflow: hidden;
	text-overflow: ellipsis;
}
.dati td input {
	width: 100%;
}
.dati tr {
	line-height: auto;
}
.dati tr:nth-child(even), .dati .desc {
	background: #F4F4F4;
}
.dati2 .alternate td {
	background: #F8F8F8;
}
.dati tr:hover, .dati2 tr:hover, .dati2 .alternate:hover {
	background: #E6E6E6;
	color: #424447;
}
.dati2 td {
	vertical-align: middle;
	margin: 0px;
	padding: 4px 2px 4px 4px;
	border: none;
	color: rgba(65,65,65,1);
	border-bottom: 1px solid #ececec;
}

</style>


<page backtop="5mm" backbottom="5mm" backleft="4mm" backright="4mm" style="">

<div class="elaborathio" style="font-size: 12px; margin: 0; width: 760px; padding: 0; text-align: justify;">


<h1 style="margin-bottom: 0;">Verifica Ospiti in sala</h1>
<h2>EVENTO <?php echo $evento['id'].' '.$evento['titolo_ricorrenza']; ?> DATA: <?php echo mydate($evento['data_evento']); ?></h2>

</div>


<?php
	

	
	if(isset($_GET['ordine'])) { if(!is_numeric($_GET['ordine'])){ exit; } else { $ordine = $ordine_mod[$_GET['ordine']]; }}
	
						
	$query = "SELECT * FROM  fl_tavoli  WHERE `evento_id` = '$evento_id' ORDER BY `nome_tavolo` DESC;";
	
	$risultato = mysql_query($query, CONNECT);

	?>
<table class="dati">
<tr>
  <th>Tavolo</th>
  <th>Lato</th>  
  <th>Adulti</th>
  <th>Bambini</th>
  <th>Sedie</th>
  <th>Segg.</th>
  <th>ANNOTAZIONI</th>
</tr>

<?php 
	
	
	if(mysql_affected_rows() == 0) { echo "<tr><td colspan=\"6\">Nessun ospite</td></tr>";		}
	
	while ($riga = mysql_fetch_array($risultato)) 
	{					
			

			
			$lato = '';
			$nome_tavolo = strtolower($riga['nome_tavolo']);
			
			if(strpos( $nome_tavolo, 'sposo') !== false) $lato = 'SPOSO';
			if(strpos( $nome_tavolo, 'sposa') !== false) $lato = 'SPOSA';
			$numero = urldecode(str_replace('sposo','', $nome_tavolo));
			$numero = urldecode(str_replace('sposa','', $numero));
	        
			echo "<tr>"; 
				
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6;\">".$lato."</td>"; 
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\">".$numero."</td>"; 
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\"></td>"; 
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\"></td>"; 
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\"></td>"; 
			echo "<td vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\"></td>"; 
			echo "<td width=\"420\" vertical-align=\"middle\" style=\"border: 1px solid #e6e6e6; text-align: center;\"></td>"; 
		    echo "</tr>"; 
			



	}
	echo "</table>";
	

?>

</page>

<?php 


    $content = ob_get_clean();
  	mysql_close(CONNECT);

    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'it', true, 'UTF-8', array(0, 0, 0, 0));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	    $html2pdf->Output($filename);

		//header('Location: ./scarica.php?show&file='.$folder.$filename);

	
	
    }
    catch(HTML2PDF_exception $e) {
         echo "Problema nella creazione del documento".$e;
    }
	

	exit;

?>
