<?php
require_once '../../fl_core/autentication.php';
require_once 'fl_settings.php';

$evento_id = check($_GET['evento_id']); //recupero l'id dell'evento
$ambiente_id = check($_GET['ambiente_id']); //recupero l'id dell'ambiente
$evento = GRD($tabella, $evento_id); //mi ritorna dati dell'evento
$tipo_tavolo = GQS('fl_tavoli_tipo', '*', ' id > 1'); //mi ritona tutti i tipi di tavolo
$tipo_commensale = GQS('fl_commensali_tipo', '*', '1'); //mi ritorna la tipologia di commensale
$totalizzatore = GQD('`fl_tavoli_commensali` AS persone LEFT JOIN fl_tavoli AS tavolo ON persone.tavolo_id = tavolo.id', 'SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,adulti,0)) AS a, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,bambini,0)) AS b, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,sedie,0)) AS s,SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,seggioloni,0)) AS h,sum(IF(persone.tipo_commensale = 5,adulti + bambini,0)) as seraTot,sum(IF(persone.tipo_commensale = 6,adulti + bambini,0)) as opTot', ' tavolo.`evento_id` = ' . $evento_id);
$img = DMS_PUBLIC . 'tableau/' . $ambiente_id . $evento_id . '.png';

$filename = 'Schema-tavoli-' . $ambiente_id . $evento_id . '.pdf';

require_once '../../fl_set/librerie/html2pdf/html2pdf.class.php';

// get the HTML
ob_start();?>


<style type="text/css">

.elaborathio { color: #7A7A7A;  }


</style>


<page backtop="2mm" backbottom="0mm" backleft="2mm" backright="2mm" style="">

<?php
$style_div = '';
if ($_GET['orientamento'] == 0) {
    $style_div = 'width: 210mm;height:290mm';
} else {
    $style_div = 'width: 297mm;height:200mm';

}
?>

<div class="elaborathio" style="font-size: 12px; margin: 0;  padding: 0; text-align: center;<?php echo $style_div; ?>">

  		<h1 style="margin: 2px;font-size: 14px;">SCHEMA TAVOLI SALA</h1>
		<h2 style="margin: 2px;font-size: 14px;"><?php echo $evento['titolo_ricorrenza']; ?>  data <?php echo mydate($evento['data_evento']); ?></h2>

<?php

if ($_GET['orientamento'] == 0) {

	$height="990";
	$width="800";

} else {
    $height="650";
	$width="990";

}
?>



		<img src="<?php echo $img; ?>" alt="Schema Tavoli" style="border: 3px solid #ccc;" width="<?php echo $width; ?>" height="<?php echo $height; ?>">

		<h2 style="margin: 10px 0px 0xp 0px;font-size: 12px;">Totale Ospiti:
		<span id="totale-a"><?php echo $totalizzatore['a']; ?> Adulti</span>
		<span id="totale-b"><?php echo ($totalizzatore['b'] > 0) ? ' + ' . $totalizzatore['b'] . ' Bambini' : ''; ?></span>
		<span id="totale-s"><?php echo ($totalizzatore['s'] > 0) ? ' + ' . $totalizzatore['s'] . ' Sedie' : ''; ?></span>
		<span id="totale-h"><?php echo ($totalizzatore['h'] > 0) ? ' + ' . $totalizzatore['h'] . ' Seggiolone' : ''; ?></span>
		<span id="totale-h"><?php echo ($totalizzatore['seraTot'] > 0) ? ' + ' . $totalizzatore['seraTot'] . ' Serali' : ''; ?></span>
		<span id="totale-h"><?php echo ($totalizzatore['opTot'] > 0) ? ' + ' . $totalizzatore['opTot'] . ' Operatori' : ''; ?></span>
		</h2>
		<p style="font-size: 12px; color: red;">* Nota intolleranze e allergie. Verifica sulla lista intolleranze i dettagli per questo tavolo.</p>



</div>

</page>
<?php

$content = ob_get_clean(); 
mysql_close(CONNECT);

if ($_GET['orientamento'] == 0) {
    $orientamento = 'P';
} else {
    $orientamento = 'L';

}

try
{
    $html2pdf = new HTML2PDF($orientamento, 'A4', 'it', true, 'UTF-8', array(0, 0, 0, 0));
	$html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->pdf->SetTitle('SCHEMA TAVOLI SALA');
	$html2pdf->pdf->SetSubject($evento['titolo_ricorrenza'].' data '.mydate($evento['data_evento']));
    $html2pdf->writeHTML($content);
    $html2pdf->Output($filename);

    //header('Location: ./scarica.php?show&file='.$folder.$filename);

} catch (HTML2PDF_exception $e) {
    echo "Problema nella creazione del documento" . $e;
}

exit;

?>


