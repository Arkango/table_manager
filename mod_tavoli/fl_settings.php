<?php 
	
	// Variabili Modulo
	$active = 1;
	$sezione_tab = 1;
	$tab_id = 6;
	$tabella = $tables[$tab_id];
	$select = "*";
	$step = 1000; 
	$sezione_id = -1;
	$jorel = 0;
	$text_editor = 2;
	$jquery = 1;
	$searchbox = "Cerca nome...";
	$fancybox = 1;
	$calendar = 1;
	$documentazione_auto = 8;
	$dateTimePicker = 1;
	if($_SESSION['usertype'] == 0 || $_SESSION['usertype'] == 3) { $filtri = 1; }
  	if(!isset($_GET['lead_id'])) $new_button = '<a href="../mod_leads/?action=4" class="" style="color: gray"> <i class="fa fa-plus-circle"></i>  </a>';

		
	if(isset($_GET['proprietario']) && check(@$_GET['proprietario']) != -1 && check(@$_GET['proprietario']) != '') { $proprietario_id = check($_GET['proprietario']); } else {  $proprietario_id = -1; }
	if($_SESSION['usertype'] != 0 && !isset($_GET['proprietario'])) $proprietario_id = $_SESSION['number'];
	if(isset($_GET['lead_id'])) { $lead_id = check($_GET['lead_id']);  } else {    $lead_id = 0; }

	


	if(isset($_GET['data_da']) && check($_GET['data_da']) != "" && check($_GET['data_a']) != "") { 
	 $data_da = convert_data($_GET['data_da'],1); $data_a = convert_data($_GET['data_a'],1); 
	 $data_da_t = check($_GET['data_da']); $data_a_t = check($_GET['data_a']); 
	} else {
	 $data_da_t = date('d/m/Y'); 
	 $data_a_t = date('d/m/Y',time()+2592000); 
	}


	/* Tipologie di ordinamento disponobili */
    $ordine_mod = array("data_evento ASC","potential_rel ASC","operatore ASC","id DESC");
	$ordine = $ordine_mod[0];	
	
	
	/*Impostazione automatica da tabella */
	$campi = gcolums($tabella); //Ritorna i campi della tabella
	$tipologia_main = gwhere($campi,'WHERE id != 1 ','','id','data_evento',0,2592000);//Imposta i filtri della query prendendo i dati GET e se sono tra i filtri li applica
	$basic_filters = array('titolo_ricorrenza','periodo_evento');

	
	
	/* Inclusioni Oggetti Categorie */
	include('../../fl_core/dataset/array_statiche.php');
	include('../../fl_core/dataset/proprietario.php');
	require('../../fl_core/class/ARY_dataInterface.class.php');
	$data_set = new ARY_dataInterface();
	$colors = array(9=>'#7B3DA0',5=>'#4c9ed9',6=>'#DEBA0F',7=>'#DA3235',9=>'#1DC59B;');


	$promoter = $proprietario;

	$location_evento = $data_set->data_retriever('fl_sedi','sede',"WHERE id != 1",'sede ASC');
	$anagrafica_cliente = $anagrafica_cliente2 = $data_set->data_retriever('fl_anagrafica','cognome,nome',"WHERE id != 1 AND tipo_profilo = 2 ",'id DESC,cognome ASC');
	$meeting_location[0] = 'Tutte';
	$proprietario['-1'] = "Tutti";
	$periodo_evento = $data_set->get_items_key("tipo_interesse");
	$ambienti = $data_set->get_items_key("ambienti");
	$stato_evento = array('Da confermare','Confermato','Archiviato','Annullato');
	unset($ambienti[0]);
	unset($ambienti[1]);			
	$tipo_evento  = $data_set->data_retriever('fl_cg_res','codice,label','WHERE parent_id = 0 AND tipo_voce = 1');
    $fiori = array('Fiorista Sala','Fiorista Sposi');
    $gruppo_musicale = array('Gruppo Interno','Gruppo Esterno');
    $foto_e_auguri = array('Durante Aperitivi','Specificare');


	function select_type($who){
	
	/* Gestione Oggetto Statica */	
	$textareas = array('note_intolleranze','note_servizio',"messaggio","descrizione","condizioni_aggiuntive"); 
	$select = array('anagrafica_cliente','anagrafica_cliente2','tipo_evento','periodo_evento','location_evento',"mansione","paese","proprietario");
	$disabled = array('end_meeting',"visite");
	$hidden = array('evento_id','note','customer_id',"data_creazione",'proprietario','marchio','contract_id',"data_arrived",'lead_id','is_customer',"data_aggiornamento","marchio","ip","operatore");
	$radio = array('all_day');
	$text = array();
	$calendario = array();	
	$checkbox = array('stato_evento','fiori','gruppo_musicale');
	$file = array();
	$timer = array();
	$touch = array();
	$datePicker = array('data_fine_evento','data_evento','start_date','end_date');
	$multi_selection = array("ambienti");	
	$ifYesText = array("segnaposti", "bomboniere", "dolci", "ospiti_serali", "miniclub", "stanza_sposi", "stanze_aggiuntive");	
	if(isset($_GET['id']) && check($_GET['id']) == 1) { array_push($hidden,'anagrafica_cliente','anagrafica_cliente2','condizioni_aggiuntive','estremi_acconto'); }

	$type = 1;
	
	if(in_array($who,$select)) { $type = 2; }
	if(in_array($who,$textareas)){ $type = 3; }
	if(in_array($who,$disabled)){ $type = 4; }
	if(in_array($who,$radio)){ $type = 8; }
	if(in_array($who,$calendario)){ $type = 20; }
	if(in_array($who,$file)){ $type = 18; }
	if(in_array($who,$text)){ $type = 24; }
	if(in_array($who,$timer)){ $type = 7; }
	if(in_array($who,$multi_selection)){ $type = 23; }
	if(in_array($who,$checkbox)){ $type = 19; }
	if(in_array($who,$datePicker)){ $type = 11; }
	if(in_array($who,$ifYesText)){ $type = 13; }
	if(in_array($who,$hidden)){ $type = 5; }
	
	return $type;
	}
	
	$new_button = $module_menu = '';
	$module_title = 'Planner e servizio';
	$menu = array();
		

	$b = (!isset($_GET['b'])) ? '' : check($_GET['b']);
	//if( ($_SESSION['usertype'] == 0 || $_SESSION['usertype'] == 3) && ($proprietario_id < 1 || isset($_GET['intro']))) { 
	foreach($menu as $label => $link){ // Recursione Indici di Categoria
				$selected = ($b  == $label) ? " class=\"selected\"" : "";
			    $module_menu .= "<li $selected><a href=\"$link&b=$label\">".ucfirst($label)."</a></li>\r\n"; 
	}//}




	
?>