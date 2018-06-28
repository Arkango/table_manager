<?php
require_once('../../fl_core/autentication.php');
require_once('fl_settings.php');


include("../../fl_inc/headers.php");



$evento_id = check($_GET['evento_id']);
$ambiente_id = check($_GET['ambiente_id']);
$template_id = check($_GET['template_id']);

//recupero l'id dell'evento
$evento = GRD($tabella,$evento_id);					  //mi ritorna dati dell'evento
$tipo_tavolo = GQS('fl_tavoli_tipo','*',' id > 1');	  //mi ritona tutti i tipi di tavolo
$tipo_commensale = GQS('fl_commensali_tipo','*','1'); //mi ritorna la tipologia di commensale

$totalizzatore = GQD('`fl_tavoli_commensali` AS persone LEFT JOIN fl_tavoli AS tavolo ON persone.tavolo_id = tavolo.id','SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,adulti,0)) AS a, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,bambini,0)) AS b, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,sedie,0)) AS s,SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,seggioloni,0)) AS h,sum(IF(persone.tipo_commensale = 5,adulti + bambini,0)) as seraTot,sum(IF(persone.tipo_commensale = 6,adulti + bambini,0)) as opTot',' tavolo.`evento_id` = '.$evento_id);

?>

<script src="../../fl_set/jsc/fabric/fabric.min.js" typre="text/javascript"></script>
<script src="../../fl_set/jsc/fabric/fabric.ext.js" typre="text/javascript"></script>
<script src="../../fl_set/jsc/fabric/myCanvas_template.js" typre="text/javascript"></script>
<style>

	.ui-dialog{
		top:60px;
		z-index: 1112 !important;
		background: #f1f1f1;
	}

.ui-widget-header {
    color: #000;
    font-weight: bold;
}

	.rigaOspite { background: #f1eded; padding: 15px 15px 42px 15px;  margin: 5px 0; }

	.rigaOspite input  { border: none;

	}


	.canvas-container { margin: 100px auto;}
</style>
<body>



	<body style=" background: rgb(241, 241, 241) none repeat scroll 0% 0%;">
		<div id="container" >
			<div id="content_scheda" style="max-width:60%">



				<h1>SCHEMA TAVOLI SALA</h1>

				<!--

				<?php if($evento_id != 0){ ?>
				<a href="mod_opera.php?template_id=<?php echo $template_id; ?>&ambiente_id=<?php echo $ambiente_id; ?>&evento_id=<?php echo $evento_id; ?>&layout=1" class="button" style="margin: 10px 38%;background: rgb(234, 92, 24);font-size: 16px;">Associa all'evento corrente</a>
				<?php }?>

			-->

				<i style="font-size:20px;float:right;cursor:grab" id="print" class="fa fa-print"></i>



				<!-- div del form -->
				<?php if(isset($_GET['layout'])) { ?>
				<div id="formTable" style=" background: #f1eded; margin-top:2%;position:relative;margin: 0 auto;  ">
					<form id="Form"  action="#" style="float:left">
						Crea un tavolo: <select name="tipo_tavolo" id="tipo_tavolo"><?php foreach ($tipo_tavolo as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['label'].'</option>';
						} ?></select>
						<input type="text" name="categoria" placeholder="SPOSO,SPOSA,FAMIGLIA" style="text-transform: uppercase;">
						<input type="button" id="createElement" value="Crea tavolo">
					</form>

				</div>
				<?php } ?>

				<!-- div canvas -->
				<div style="position:relative; width: 100%; margin: 0 auto; text-align:center;">
					<canvas id="disposizioneTavoli" style="border: 1px solid grey;position:absolute;"></canvas>
					<!-- cambiando width ed height tutto si adatta -->
				</div>
				<!--fine div canvas -->


				<a class="button" href="javascript:location.reload();">Ricarica Schema tavoli</a>
				<i style="font-size:20px;cursor:grab;float:right" id="print" class="fa fa-print"></i>


				<!--  modale di modifica tavolo -->
				<div id="dialog-form" title="Gestione tavolo" style="z-index: 1000;background: white;max-height:600px;text-align: left; display: hidden;">
					<div id="results"></div>
					<br>

					<input type="hidden" id="tableId" name="tableId" value="">
					<div id="tableName">
						<button id="delTable" class="button" style="color:#cb2c2c !important; background: none;margin-left:7px">Elimina tavolo</button>
					</div>
	</div>






</div>
</div>

</body>
</html>
