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

$tabella_su_cui_operare = 'fl_tavoli_template';



function mysql_fetch_all_mia($result, $result_type = MYSQL_BOTH){
	$rows = array();
	while ($row = mysql_fetch_array($result, $result_type)) {
		$rows[] = $row;
	}
	return $rows;
}

if(isset($_GET['orientamento'])){

	$layout_info = GQD('fl_tavoli_layout','*','layout_id='.check($_GET['template_id']));

	echo json_encode(array('orientamento'=>$layout_info['orientamento'] )); exit;

}


if (isset($_GET['insertTableId'])) { //richiesta di inserire un tavolo
	$insertTableId = check($_GET['insertTableId']); //recupero e controllo variabili
	$insertEventId = $_GET['evento_id']; //recupero e controllo variabili
	$x = check($_GET['x']); //recupero e controllo variabili
	$y = check($_GET['y']); //recupero e controllo variabili
	$type = check($_GET['type']); //recupero e controllo variabili
	$categoria = check($_GET['categoria']); //recupero e controllo variabili
	$layout_id = check($_GET['template_id']); //recupero e controllo variabili
	
	if ($_GET['diverso'] != 'false') {$insertEventId = check($_GET['diverso']);}
	//vedo se un tavolo è già stato inserito
	$contoTavoli = "SELECT count(*) AS quanti FROM $tabella_su_cui_operare WHERE numero_tavolo = $insertTableId AND layout_id = $layout_id";
	$contoTavoli = mysql_query($contoTavoli, CONNECT);
	$contoTavoli = mysql_fetch_array($contoTavoli);
	if ($contoTavoli['quanti'] == 0) //se non ci sono tavoli con id ed vento uguale lo aggiungo
	{

		

		$inseriscoTavolo = "INSERT INTO $tabella_su_cui_operare (layout_id,tipo_tavolo_id,numero_tavolo,nome_tavolo,asse_x,asse_y) VALUES($layout_id,$type,$insertTableId,'$categoria',$x,$y)";
		$inseriscoTavolo = mysql_query($inseriscoTavolo, CONNECT);
	} else { //sennò recupero i dati dello stesso
		if ($_GET['diverso'] == 'false') {

			$selezionoDatiTavolo = "SELECT id,numero_tavolo,nome_tavolo,asse_x,asse_y,tipo_tavolo_id,angolare FROM $tabella_su_cui_operare WHERE layout_id = $layout_id AND numero_tavolo = $insertTableId";
			
			$selezionoDatiTavolo = mysql_query($selezionoDatiTavolo, CONNECT);
			$selezionoDatiTavolo = mysql_fetch_assoc($selezionoDatiTavolo);

		} else { $selezionoDatiTavolo = "SELECT id,numero_tavolo,nome_tavolo,asse_x,asse_y,tipo_tavolo_id,angolare FROM $tabella_su_cui_operare WHERE layout_id = $layout_id AND numero_tavolo = $insertTableId";
			
			$selezionoDatiTavolo = mysql_query($selezionoDatiTavolo, CONNECT);
			$selezionoDatiTavolo = mysql_fetch_assoc($selezionoDatiTavolo);
		}
		echo json_encode($selezionoDatiTavolo, true);
		exit;
	}
	
}



/* --------------------   richieste relative ai tavoli ------------------*/

if (isset($_GET['deleteTable'])) { //cancella il tavolo
	
	$delTable = check($_GET['delTable']);
	$evento_id = check($_GET['evento_id']);
	$layout_id = check($_GET['template_id']); //recupero e controllo variabili
	
	$updateCampo = "DELETE FROM $tabella_su_cui_operare WHERE layout_id = $layout_id AND numero_tavolo = $delTable";
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;
}

if (isset($_GET['updateTable'])) { //update coords tavolo
	
	$idTable = check($_GET['TableId']);
	$idEvento = check($_GET['evento_id']);
	$x = check($_GET['x']);
	$y = check($_GET['y']);
	$angle = check($_GET['angle']);
	$layout_id = check($_GET['template_id']); //recupero e controllo variabili
	
	
	$updateCampo = "UPDATE $tabella_su_cui_operare SET asse_x = '$x',asse_y = '$y',angolare = '$angle' WHERE layout_id = $layout_id AND numero_tavolo = $idTable";
	$updateCampo = mysql_query($updateCampo, CONNECT);
	exit;
}




if (isset($_GET['categoria']) && isset($_GET['tableId']) ) { //richiesta di cambiare un tavolo per i commensali
	$tableId = check($_GET['tableId']);
	$categoria = check($_GET['categoria']);
	$evento = check($_GET['evento_id']);
	$layout_id = check($_GET['template_id']); //recupero e controllo variabili
	
	//aggiorna il nome del tavolo di categoria
	$updateTavolo = "UPDATE $tabella_su_cui_operare SET nome_tavolo = '$categoria' WHERE layout_id = $layout_id AND numero_tavolo = $tableId ";
	$updateTavolo = mysql_query($updateTavolo, CONNECT);
	exit;
}



if (isset($_GET['evento_id'])) { //in base all'ebento recupero il numero di tavoli
	
	$evento = false;
	$evento_principale = check($_GET['evento_id']);
	$ambiente_id = check($_GET['ambiente_id']);
	$DatiIniziali = mysql_query($DatiIniziali, CONNECT);
	$DatiIniziali = mysql_fetch_assoc($DatiIniziali);
	$layout_id = check($_GET['template_id']); //recupero e controllo variabili

	
	//se ce ne sono già
	$ciSonoGiaTavoli = "SELECT numero_tavolo as ciSono FROM $tabella_su_cui_operare WHERE layout_id = $layout_id ORDER by numero_tavolo DESC LIMIT 1";
	$ciSonoGiaTavoli = mysql_query($ciSonoGiaTavoli, CONNECT);
	$ciSonoGiaTavoli = mysql_fetch_assoc($ciSonoGiaTavoli);
	
	if ($ciSonoGiaTavoli['ciSono'] != 0) {
		
		$Tavoli = "SELECT numero_tavolo FROM  $tabella_su_cui_operare  WHERE layout_id = $layout_id ORDER BY numero_tavolo DESC";
		$Tavoli = mysql_query($Tavoli, CONNECT);
		$Tavoli = mysql_fetch_all_mia($Tavoli, MYSQL_NUM);
	}
	

	
	$DatiIniziali['ciSono'] = $ciSonoGiaTavoli['ciSono'] + 1;
	$DatiIniziali['idTavoli'] = $Tavoli;
	$DatiIniziali['idTavoliOpachi'] = null;
	
	echo json_encode($DatiIniziali, true);
	exit;
}


/* -------------------- fine richieste relative ai tavoli ------------------*/


if (isset($_POST["img"])) { // crea l'immagine
	
	$encodedData = explode(',', $_POST["img"]);
	$data = base64_decode($encodedData[1]);
	$urlUploadImages = DMS_PUBLIC . 'tableau/';
	$nameImage = check($_POST["evento_id"]) . '.png';
	$img = imagecreatefromstring($data);
	if ($img) {
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
