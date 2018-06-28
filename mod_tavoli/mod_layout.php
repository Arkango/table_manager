<?php


require_once('../../fl_core/autentication.php');
require_once('fl_settings.php');


include("../../fl_inc/headers.php");



$evento_id = check($_GET['evento_id']);

//recupero l'id dell'evento
$evento = GRD($tabella,$evento_id);					  //mi ritorna dati dell'evento
$tipo_tavolo = GQS('fl_tavoli_tipo','*',' id > 1');	  //mi ritona tutti i tipi di tavolo
$tipo_commensale = GQS('fl_commensali_tipo','*','1'); //mi ritorna la tipologia di commensale

$totalizzatore = GQD('`fl_tavoli_commensali` AS persone LEFT JOIN fl_tavoli AS tavolo ON persone.tavolo_id = tavolo.id','SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,adulti,0)) AS a, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,bambini,0)) AS b, SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,sedie,0)) AS s,SUM(IF(persone.tipo_commensale != 6 && persone.tipo_commensale != 5,seggioloni,0)) AS h,sum(IF(persone.tipo_commensale = 5,adulti + bambini,0)) as seraTot,sum(IF(persone.tipo_commensale = 6,adulti + bambini,0)) as opTot',' tavolo.`evento_id` = '.$evento_id);

?>

<script src="../../fl_set/jsc/fabric/fabric.min.js" type="text/javascript"></script>
<script src="../../fl_set/jsc/fabric_new/fabric.ext.js" type="text/javascript"></script>
<script src="../../fl_set/jsc/fabric_new/myCanvas.js" type="text/javascript"></script>
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



				<h1>SCHEMA TAVOLI <span id="sala"></span></h1>
				<i style="font-size:20px;float:right;cursor:grab" id="print" class="fa fa-print"></i>

				<h2><?php echo $evento['titolo_ricorrenza']; ?> DATA: <?php echo mydate($evento['data_evento']); ?></h2>



				<!-- div del form -->
				<?php if(isset($_GET['layout'])) { ?>
				<div id="formTable" style=" background: #f1eded; margin-top:2%;position:relative;margin: 0 auto;  ">
					<form id="Form"  action="#" style="float:left">
						Crea un tavolo: <select name="tipo_tavolo" id="tipo_tavolo"><?php foreach ($tipo_tavolo as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['label'].'</option>';
						} ?></select>
						<select name="categoria">
							<?php if ($evento['multievento'] == 0){ ?>
							<option value="SPOSO">SPOSO</option>
							<option value="SPOSA">SPOSA</option>
						<?php } ?>
							<option value="FAMIGLIA">FAMIGLIA</option>
						</select>
						<input type="number" name="numero_cliente" min="1" style="width: 8%;"/>
						<input type="text"   id="familyLabel" name="familyLabel" placeholder="Nome tavolo" value="<?php if($evento['multievento'] != 0 ){echo $evento['titolo_ricorrenza'];} ?>" />
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


				<h2>Totale Ospiti:
					<span id="totale-a"><?php echo $totalizzatore['a']; ?> Adulti</span>
					<span id="totale-b"><?php echo ($totalizzatore['b'] > 0) ? ' + '.$totalizzatore['b'].' Bambini' : ''; ?></span>
					<span id="totale-s"><?php echo ($totalizzatore['s'] > 0) ? ' + '.$totalizzatore['s'].' Sedie' : ''; ?></span>
					<span id="totale-h"><?php echo ($totalizzatore['h'] > 0) ? ' + '.$totalizzatore['h'].' Seggiolone' : ''; ?></span>
					<span id="totale-h"><?php echo ($totalizzatore['seraTot'] > 0) ? ' + '.$totalizzatore['seraTot'].' Serali' : ''; ?></span>
					<span id="totale-h"><?php echo ($totalizzatore['opTot'] > 0) ? ' + '.$totalizzatore['opTot'].' Operatori' : ''; ?></span>
				</h2>
				<a class="button" href="javascript:location.reload();">Ricarica Schema tavoli</a>
				<i style="font-size:20px;cursor:grab;float:right" id="print" class="fa fa-print"></i>


				<!--  modale di modifica tavolo -->
				<div id="dialog-form" title="Gestione tavolo" style="z-index: 1000;background: white;max-height:600px;text-align: left; display: hidden;">
					<div id="results"></div>
					<br>


					<div id="tableName">
					<select id="catTable" style="padding-top: 11px;width: 30%;margin-left:7px">
							<option value="SPOSO">SPOSO</option>
							<option value="SPOSA">SPOSA</option>
							<option value="FAMIGLIA">FAMIGLIA</option>
						</select>
						<input type="number" min="0" id="numTable"  style="padding-top: 11px;width: 13%;margin-left:7px"><br><br>
						<input type="text" name="U_name" id="U_name"  style="padding-top: 11px;width: 50%;margin-left:7px">
						<button id="delTable" class="button" style="color:#cb2c2c !important; background: none;margin-left:7px">Elimina tavolo</button>
						<h2 style="float:left">Nome del tavolo</h2>
					</div>
					<br><br>

					<form id="addCommesale" action="#" method="post">
						<h2>Aggiungi un ospite</h2>
						<div class="rigaOspite">

						    <input name="cognome" id="primoCampo" placeholder="Cognome*" value="" style="width: 35%; padding: 10; margin-right: 5px;" required="" type="text">
							<input name="nome" value="" style="width: 35%;padding: 10;" placeholder="Nome" type="text">

							<span style="float: right; width: 25%; text-align: right;">
								A <input style="width: 75%;" name="adulti" value="1" type="number"  min="0"><br>
								B <input style="width: 75%; margin-top: 5px;" value="0" name="bambini" type="number" min="0"><br>
								S <input style="width: 75%; margin-top: 5px;" value="0" name="sedie" type="number" min="0"><br>
								H <input style="width: 75%; margin-top: 5px;" value="0" name="seggioloni" type="number" min="0"><br>
							</span>
								<input name="note_intolleranze"  value="" style="width: 70.8%; margin-top: 5px;" placeholder="Note intolleranze" type="text"><br><br>
							Scegli la tipologia di ospite: <select name="tipo_commensale" style="margin-left: -2px;"><?php foreach ($tipo_commensale as $key => $value) {

					$selected = ($value['id'] == 4) ? 'selected="selected"': '' ; //per selezionare il valore di default selezionato

					echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['tipo_commensale'].'</option>';
				} ?></select>
				<br>
				<input type="hidden" id="tableId" name="tableId" value="">
				<input type="submit" id="Inserisci" class="button" value="Aggiungi al tavolo" >
			</div>
		</form>
		<br>
		<div id="nascosta">
		</div>
		<br>
	</div>






</div>
</div>

</body>
</html>
