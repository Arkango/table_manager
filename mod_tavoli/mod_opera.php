<?php
// $tables[128] tavoli_commensali
// $tables[125] tavoli
// da sostituire `fl_eventi_hrc`


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$meeting_page = 1;
require_once '../../fl_core/autentication.php';
include 'fl_settings.php'; // Variabili Modulo


$tabella_su_cui_operare = 'fl_tavoli';



//controlli per la tabella su cui operare basati sull'evento
if((isset($_GET['evento_id']) && $_GET['evento_id'] == 0)) {
	$tabella_su_cui_operare = 'fl_tavoli_template';
}


function mysql_fetch_all_mia($result, $result_type = MYSQL_BOTH){
	$rows = array();
	while ($row = mysql_fetch_array($result, $result_type)) {
		$rows[] = $row;
	}
	return $rows;
}




//crea il template dell'evento
if (isset($_GET['template_id'])) {


	//mi aspetto anche un parametro ambiente

	$evento_id = check($_GET['evento_id']);
	$template_id = check($_GET['template_id']);
	$tavoli = GQD('fl_tavoli', 'id,evento_id', ' evento_id = ' . $evento_id.' AND layout_id = '.$template_id);
	$ambienti = explode(',',$_GET['ambiente_id']);

	if(!isset($_GET['orientamento'])){
		$orientamento_info = GQS('fl_tavoli_layout', 'orientamento', 'id = '.$template_id);

		$orientamento = $orientamento_info[0]['orientamento'];

	}else{
		$orientamento = check($_GET['orientamento']);
	}


	if (count($ambienti) > 1) {
		$ambiente_id = $ambienti[0];
	}else{
		$ambiente_id = check($_GET['ambiente_id']);
	}

	
	$insert = "INSERT INTO fl_tavoli_layout (nome_layout,ambiente_id,evento_id,orientamento) VALUES ('nuovo".$ambiente_id.$evento_id."', ".$ambiente_id.",".$evento_id.",".$orientamento.")";
	$insert = mysql_query($insert,CONNECT);
	if($template_id == -2){
		$template_id = mysql_insert_id(CONNECT);
		
	}
	$new_layout =  mysql_insert_id(CONNECT);



	$layout_info = GQS('fl_tavoli_layout', '*', 'id = '.$template_id);


	if ($tavoli['id'] < 1) { // SOLO SE NON ESISTONO TAVOLI CARICO IL TEMPLATE!!!



		$tavoli = GQS('fl_tavoli_template', '*', 'layout_id = '.$template_id);

		

		foreach ($tavoli as $chiave => $valore) {

			$query = "INSERT INTO `fl_tavoli` (`layout_id`, `evento_id`, `tipo_tavolo_id`, `numero_tavolo`, `nome_tavolo`, `data_creazione`, `asse_x`, `asse_y`, `angolare`,`numero_tavolo_utente`)
			VALUES ( '" . $new_layout . "', '" . $evento_id . "', '" . $valore['tipo_tavolo_id'] . "', '" . $valore['numero_tavolo'] . "', '" . $valore['nome_tavolo'] . "', NOW(),  '" . $valore['asse_x'] . "', '" . $valore['asse_y'] . "', '" . $valore['angolare'] . "','" . $valore['numero_tavolo_utente'] . "');";
			mysql_query($query, CONNECT);
		}
	}else{ echo 'tavoli  già presenti'; exit;}


	mysql_close(CONNECT);
	header('Location: mod_layout.php?orientamento='.$layout_info[0]['orientamento'].'&ambiente_id='.$ambiente_id.'&layout=1&evento_id=' . $evento_id);
	exit;
}


if (isset($_GET['insertTableId'])) { //richiesta di inserire un tavolo
	$insertTableId = check($_GET['insertTableId']); //recupero e controllo variabili
	$insertEventId = $_GET['evento_id']; //recupero e controllo variabili
	$text = check($_GET['text']); //recupero e controllo variabili
	$x = check($_GET['x']); //recupero e controllo variabili
	$y = check($_GET['y']); //recupero e controllo variabili
	$type = check($_GET['type']); //recupero e controllo variabili
	$categoria = check($_GET['categoria']); //recupero e controllo variabili
	$numero = check($_GET['numero']); //recupero e controllo variabili
	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili

	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$insertEventId.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout

	if ($_GET['diverso'] != 'false') {$insertEventId = check($_GET['diverso']);}
	//vedo se un tavolo è già stato inserito
	$contoTavoli = "SELECT count(*) AS quanti FROM $tabella_su_cui_operare tv LEFT JOIN fl_tavoli_layout l ON l.id = tv.layout_id  WHERE numero_tavolo = $insertTableId AND l.evento_id = $insertEventId AND ambiente_id =".$ambiente_id;
	$contoTavoli = mysql_query($contoTavoli, CONNECT);
	$contoTavoli = mysql_fetch_array($contoTavoli);
	if ($contoTavoli['quanti'] == 0) //se non ci sono tavoli con id ed vento uguale lo aggiungo
	{//attenzione layout

		$inseriscoTavolo = "INSERT INTO $tabella_su_cui_operare (layout_id,evento_id,tipo_tavolo_id,numero_tavolo,nome_tavolo,numero_tavolo_utente,nome_tavolo_utente,data_creazione,asse_x,asse_y) VALUES(".$layout_info[0]['id'].",$insertEventId,$type,$insertTableId,'$categoria','$numero','$text',NOW(),$x,$y)";
		$inseriscoTavolo = mysql_query($inseriscoTavolo, CONNECT);
	} else { //sennò recupero i dati dello stesso
		if ($_GET['diverso'] == 'false') {

			$selezionoDatiTavolo = "SELECT id,numero_tavolo,nome_tavolo,numero_tavolo_utente,nome_tavolo_utente,asse_x,asse_y,tipo_tavolo_id,angolare FROM $tabella_su_cui_operare WHERE evento_id = $insertEventId AND numero_tavolo = $insertTableId AND layout_id = ".$layout_info[0]['id'];

			$selezionoDatiTavolo = mysql_query($selezionoDatiTavolo, CONNECT);
			$selezionoDatiTavolo = mysql_fetch_assoc($selezionoDatiTavolo);
			$sommaCoperti = "SELECT sum(IF(tipo_commensale != 5,adulti,0)) a, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,bambini,0)) b, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,sedie,0)) s, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,seggioloni,0)) as h,SUM(if(note_intolleranze != '',1,0)) as noteInt,sum(IF(tipo_commensale = 5,adulti + bambini,0)) as seraTot FROM " . $tables[128] . " WHERE tavolo_id = " . $selezionoDatiTavolo['id'];
			$sommaCoperti = mysql_query($sommaCoperti, CONNECT);
			$sommaCoperti = mysql_fetch_assoc($sommaCoperti);
		} else { 
			$selezionoDatiTavolo = "SELECT tv.id,numero_tavolo,nome_tavolo,numero_tavolo_utente,nome_tavolo_utente,asse_x,asse_y,tipo_tavolo_id,angolare FROM $tabella_su_cui_operare tv LEFT JOIN fl_tavoli_layout l ON l.id = tv.layout_id WHERE l.evento_id = $insertEventId AND numero_tavolo = $insertTableId AND ambiente_id =".$ambiente_id;

			$selezionoDatiTavolo = mysql_query($selezionoDatiTavolo, CONNECT);
			$selezionoDatiTavolo = mysql_fetch_assoc($selezionoDatiTavolo);
			$sommaCoperti = array();
		}
		
		echo json_encode(array_merge($selezionoDatiTavolo, $sommaCoperti), true);
		exit;
	}

	exit;
}



/* --------------------   richieste relative ai tavoli ------------------*/

if (isset($_GET['deleteTable'])) { //cancella il tavolo

	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili
	$evento_id = check($_GET['evento_id']);

	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout

	$delTable = check($_GET['delTable']);
	$evento_id = check($_GET['evento_id']);
	$updateCampo = "DELETE FROM $tabella_su_cui_operare WHERE evento_id = $evento_id AND numero_tavolo = $delTable AND layout_id = ".$layout_info[0]['id'];
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;
}

if (isset($_GET['updateTable'])) { //update coords tavolo

	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili
	$evento_id = check($_GET['evento_id']);
	

	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout

	$idTable = check($_GET['TableId']);
	$idEvento = check($_GET['evento_id']);
	$x = check($_GET['x']);
	$y = check($_GET['y']);
	$angle = check($_GET['angle']);

	$updateCampo = "UPDATE $tabella_su_cui_operare SET asse_x = '$x',asse_y = '$y',angolare = '$angle' WHERE evento_id = $idEvento AND numero_tavolo = $idTable AND layout_id = ".$layout_info[0]['id'];
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;
}




if (isset($_GET['tableNameUser'])){

	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili
	$evento_id = check($_GET['evento_id']);
	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout

	//richiesta di cambiare un tavolo per i commensali
	$tableId = check($_GET['tableId']);
	$tableNameUser = check($_GET['tableNameUser']);
	$evento = check($_GET['evento_id']);
	//aggiorna il nome del tavolo scritto dall'utente
	$updateTavolo = "UPDATE $tabella_su_cui_operare SET nome_tavolo_utente = '$tableNameUser' WHERE evento_id = $evento AND numero_tavolo = $tableId AND layout_id = ".$layout_info[0]['id'];
	$updateTavolo = mysql_query($updateTavolo, CONNECT);
	exit;
}


if (isset($_GET['categoria']) && isset($_GET['tableId']) ) { //richiesta di cambiare un tavolo per i commensali
	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili
	$evento_id = check($_GET['evento_id']);
	
	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout
	$tableId = check($_GET['tableId']);
	$categoria = check($_GET['categoria']);
	$evento = check($_GET['evento_id']);
	//aggiorna il nome del tavolo di categoria
	$updateTavolo = "UPDATE $tabella_su_cui_operare SET nome_tavolo = '$categoria' WHERE evento_id = $evento AND numero_tavolo = $tableId  AND layout_id = ".$layout_info[0]['id'];
	$updateTavolo = mysql_query($updateTavolo, CONNECT);
	exit;
}
if (isset($_GET['numero']) && isset($_GET['tableId'])) { //richiesta di cambiare un tavolo per i commensali
	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili

	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout
	$tableId = check($_GET['tableId']);
	$numero = check($_GET['numero']);
	$evento = check($_GET['evento_id']);
	//aggiorna il numero del tavolo
	$updateTavolo = "UPDATE $tabella_su_cui_operare SET numero_tavolo_utente = '$numero' WHERE evento_id = $evento AND numero_tavolo = $tableId AND layout_id = ".$layout_info[0]['id'];
	$updateTavolo = mysql_query($updateTavolo, CONNECT);
	exit;
}


if (isset($_GET['chiedoOspiti']) && $_GET['chiedoOspiti'] == 1) {

	$table = check($_GET['table']);
	$evento_id = check($_GET['evento_id']);
	$idPersone = '';

	//recupero commensali del tavolo
	$selezionOspiti = "SELECT	tc.id as tcId,cognome,nome,note_intolleranze,adulti,
	bambini,sedie,seggioloni,ct.tipo_commensale  as tipo_commensale
	FROM fl_tavoli_commensali tc
	JOIN fl_tavoli t ON t.id = tc.tavolo_id
	JOIN fl_commensali_tipo ct ON ct.id = tc.tipo_commensale
	WHERE t.evento_id = $evento_id AND t.numero_tavolo = $table ORDER BY tc.id DESC";

	$selezionOspiti = mysql_query($selezionOspiti, CONNECT);

	$result['result'] = ''; //verrà riempito con i commensali
	$result['resultTOT'] = ''; //verrà riempito con i totali di adulti,bambini e sedie
	$arrayAppogio = array(); //variabile d'appoggio per il push

	while ($row = mysql_fetch_assoc($selezionOspiti)) { //riempio il vettore con i risultati della query
		array_push($arrayAppogio, $row);
		$idPersone .= $row['tcId'] . ',';
	}

	$result['result'] = $arrayAppogio; //reimpio result con tutti i commensali
	$idPersone = rtrim($idPersone, ','); //rimuovo l'ultima vigola dalla stringa

	//recupero totali del tavolo
	$sumOspitiTavolo = " SELECT sum(IF(tipo_commensale != 6 && tipo_commensale != 5,adulti,0)) aTot, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,bambini,0)) bTot, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,sedie,0)) sTot, sum(IF(tipo_commensale != 6 && tipo_commensale != 5,seggioloni,0)) as hTot,sum(IF(tipo_commensale = 5, adulti + bambini,0)) as
	seraTot,sum(IF(tipo_commensale = 6, adulti + bambini,0)) as opTot,SUM(if(note_intolleranze != '',1,0)) as noteInt FROM fl_tavoli_commensali WHERE id IN($idPersone)";
	$sumOspitiTavolo = mysql_query($sumOspitiTavolo, CONNECT);
	$sumOspitiTavolo = mysql_fetch_assoc($sumOspitiTavolo);
	$result['resultTOT'] = $sumOspitiTavolo;

	echo json_encode($result, true);
	exit;
}




/* -------------------- fine richieste relative ai tavoli ------------------*/



/* --------------------   richieste relative ai commensali ------------------*/
if (isset($_GET['cognome']) && isset($_GET['tableId'])) { //ritorna i commensali

	$ambiente_id = check($_GET['ambiente_id']); //recupero e controllo variabili
	$evento_id = check($_GET['evento_id']);
	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_id.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout

	$cognome = check($_GET['cognome']);
	$nome = check($_GET['nome']);
	$intolleranze = check($_GET['intolleranze']);
	$a = check($_GET['a']); //adulti
	$b = check($_GET['b']); //bambini
	$s = check($_GET['s']); //sedie
	$h = check($_GET['h']); //sedioloni
	$tipo_commensale = check($_GET['tipo_commensale']); //tipo_commensale
	$tableId = check($_GET['tableId']); //id del tavolo nel canvas
	$eventoId = check($_GET['evento_id']); //id dell'evento

	$insertCommensale = "INSERT INTO fl_tavoli_commensali (id,tavolo_id,cognome,nome,adulti,bambini,sedie,seggioloni,note_intolleranze,tipo_commensale,data_creazione,data_aggiornamento) VALUES(NULL,(SELECT id FROM fl_tavoli WHERE evento_id = $eventoId AND numero_tavolo = $tableId AND layout_id = ".$layout_info[0]['id']."),'$cognome','$nome','$a','$b','$s','$h','$intolleranze','$tipo_commensale',CURDATE(),NOW())";
	$insertCommensale = mysql_query($insertCommensale, CONNECT);
	exit;
}



if (isset($_GET['idCommensale'])) {

	$idCommensale = check($_GET['idCommensale']);
	$nomeCampo = check($_GET['nomeCampo']);
	$valoreCampo = check($_GET['valoreCampo']);

	$updateCampo = "UPDATE fl_tavoli_commensali SET $nomeCampo = '$valoreCampo' WHERE id = $idCommensale";
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;

}

if (isset($_GET['delete'])) { //cancella un commensale

	$idCommensale = check($_GET['commensaleId']);
	$updateCampo = "DELETE FROM fl_tavoli_commensali WHERE id = $idCommensale";
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;

}
/* -------------------- fine  richieste relative ai commensali ------------------*/


if (isset($_GET['evento_id'])) { //in base all'ebento recupero il numero di tavoli

	$evento = false;
	$evento_principale = check($_GET['evento_id']);
	$ambiente_id = check($_GET['ambiente_id']);

	$ambiente_info = GQS('fl_ambienti', ' nome_ambiente ', 'id = ' . $ambiente_id);
	$layout_info = GQS('fl_tavoli_layout', '*', 'evento_id = '.$evento_principale.' AND ambiente_id = '.$ambiente_id); //mi ritorna info sul layout


	$condition = ' ambiente_principale = ' . $ambiente_id . ' OR ambiente_1 = ' . $ambiente_id . ' OR ambiente_2 = ' . $ambiente_id . ' OR notturno = ' . $ambiente_id . '	OR ambienti = ' . $ambiente_id . '  AND id = ' . $evento_principale;
	if($_SERVER['HTTP_HOST'] == 'calderonimartini.condivision.cloud') $condition = 'ambienti = ' . $ambiente_id . '  AND id = ' . $evento_principale;

	$evento_hrc = GQS('fl_eventi_hrc', ' multievento, data_evento ', $condition);

	if ($evento_hrc[0]['multievento'] == 1) {

		$data_evento = substr($evento_hrc[0]['data_evento'], 0, 10);

		$condition_1 =  '( ambienti = ' . $ambiente_id . ' OR  ambiente_1 = ' . $ambiente_id . '  OR ambiente_2 = ' . $ambiente_id . '  OR notturno = ' . $ambiente_id . ' )';
		if($_SERVER['HTTP_HOST'] == 'calderonimartini.condivision.cloud') $condition_1 = ' ambienti = ' . $ambiente_id ;

		$eventi_coinvolti = GQS('fl_eventi_hrc', 'id', $condition_1.' AND DATE(data_evento) = "' . $data_evento . '"');


		$evento = implode(',', array_column($eventi_coinvolti, 'id'));
	}

	//se ce ne sono già
	$ciSonoGiaTavoli = "SELECT numero_tavolo as ciSono FROM $tabella_su_cui_operare WHERE evento_id = $evento_principale AND layout_id = ".$layout_info[0]['id']."  ORDER by numero_tavolo DESC LIMIT 1";
	$ciSonoGiaTavoli = mysql_query($ciSonoGiaTavoli, CONNECT);
	$ciSonoGiaTavoli = mysql_fetch_assoc($ciSonoGiaTavoli);

	if ($ciSonoGiaTavoli['ciSono'] != 0) {

		$Tavoli = "SELECT numero_tavolo FROM  $tabella_su_cui_operare  WHERE evento_id = $evento_principale AND layout_id = ".$layout_info[0]['id']."  ORDER BY numero_tavolo DESC";
		$Tavoli = mysql_query($Tavoli, CONNECT);
		$Tavoli = mysql_fetch_all_mia($Tavoli, MYSQL_NUM);
	}

	if ($evento) {
		$daTogliere = array($evento_principale . ',', $evento_principale, ',' . $evento_principale);
		$evento = str_replace($daTogliere, '', $evento);
		$evento = trim($evento, ',');

		//SELECT tv.evento_id, numero_tavolo FROM fl_tavoli tv LEFT JOIN fl_tavoli_layout l ON l.id = tv.`layout_id` WHERE tv.evento_id IN ( 483, 488 )
		//AND ambiente_id =2


		$Tavoli_opachi = "SELECT l.evento_id,numero_tavolo FROM $tabella_su_cui_operare tv LEFT JOIN fl_tavoli_layout l ON l.id = tv.`layout_id`  WHERE l.evento_id IN($evento) AND ambiente_id = ".$ambiente_id."  ORDER BY numero_tavolo DESC";
		$Tavoli_opachi = mysql_query($Tavoli_opachi, CONNECT);
		$Tavoli_opachi = mysql_fetch_all_mia($Tavoli_opachi, MYSQL_NUM);

	}

	$DatiIniziali['ciSono'] = $ciSonoGiaTavoli['ciSono'] + 1;
	$DatiIniziali['idTavoli'] = $Tavoli;
	$DatiIniziali['idTavoliOpachi'] = $Tavoli_opachi;
	$DatiIniziali['data'] = substr($evento_hrc[0]['data_evento'], 0, 10);
	$DatiIniziali['nome_ambiente'] = $ambiente_info[0]['nome_ambiente'];

	echo json_encode($DatiIniziali, true);
	exit;
}



if (isset($_POST["img"])) { // crea l'immagine

	$encodedData = explode(',', $_POST["img"]);
	$data = base64_decode($encodedData[1]);
	$urlUploadImages = DMS_PUBLIC . 'tableau/';
	$nameImage = check($_POST["ambiente_id"]).check($_POST["evento_id"]) . '.png';
	$img = imagecreatefromstring($data);
	if ($img) {
		header('Content-Type: image/png');
		imagepng($img, $urlUploadImages . $nameImage, 0);
		imagedestroy($img);
		$url['esito'] = 1;
		echo json_encode($url);
	} else {
		$url['esito'] = 0;
		echo json_encode($url);
	}
	exit;
}


mysql_close(CONNECT);
exit;
