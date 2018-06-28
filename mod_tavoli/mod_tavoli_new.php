<?php
require_once '../../fl_core/autentication.php';
include 'fl_settings.php'; // Variabili Modulo

//controllo che mi mandi un ambiente solo
$ambienti = explode(',',$_GET['ambiente_id']);
if (count($ambienti)>1) {echo 'non posso gestire più ambienti insieme';exit;}

$ambiente_id = check($_GET['ambiente_id']);
$evento_id = ($_GET['evento_id'] != '') ? check($_GET['evento_id']) : 0;
$ambiente_info = GQS('fl_ambienti','*','id = '.$ambiente_id); //info sull'ambiente


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$nome_layout = ($_POST['nome_t'] != '') ? check($_POST['nome_t']) : 'errore' ;
	$orientamento = ($_POST['orientamento'] != '') ? check($_POST['orientamento']) : 'errore' ;
	$sede_id = $ambiente_info[0]['sede_id'];

	$insert_template = "INSERT INTO `fl_tavoli_layout`(`sede_id`, `ambiente_id`, `nome_layout`, `evento_id`, `data_layout`,`orientamento`) VALUES ($sede_id,$ambiente_id,'$nome_layout',$evento_id,NOW(),$orientamento)";

	$insert_template = mysql_query($insert_template,CONNECT);
	$last_id = mysql_insert_id(CONNECT);

	if( $last_id > 0){
		header('Location: mod_layout_template.php?orientamento='.$orientamento.'&layout=1&evento_id='.$evento_id.'&ambiente_id='.$ambiente_id.'&template_id='.$last_id);
	}
   	echo mysql_error();
	mysql_close(CONNECT);
    exit;
	


}

include("../../fl_inc/headers.php");

?>

<div style="margin:10%;">

<h2 style="text-align:center;"><?php echo $ambiente_info[0]['nome_ambiente']; ?></h2>

<form  method="POST">
<div id="tb_id" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false">
<div class="form_row" id="box_nome_t"><p class="input_text"><label for="nome_t">Nome template  </label><input class="validate[required]" type="text" name="nome_t" id="nome_t" value="" required> </p>
</div>

<div class="form_row" id="box_orientamento">
<p class="input_text" style="text-align:left !important;"><label for="orientamento">Orientamento</label>


<input name="orientamento" type="radio" id="orientamento0" value="0" required>
<label for="orientamento0" style="width: auto;">Verticale</label>
<input name="orientamento" type="radio" id="orientamento1" value="1" required>
<label for="orientamento1" style="width: auto;">Orizzontale</label>
</p>
</div>
<input type="hidden" name="ambiente_id" value="<?php echo $ambiente_id; ?>" >
<input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>" >
<input type="submit" value="Salva" class="button" style="margin: 10px auto;padding: 8px 12px;" >
</form>

</div>