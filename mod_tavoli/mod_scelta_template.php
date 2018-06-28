<?php

require_once '../../fl_core/autentication.php';
include 'fl_settings.php'; // Variabili Modulo

//recupero evento
$evento_id = ($_GET['evento_id'] != '') ? check($_GET['evento_id']) : 0;

//html da mostrare
$html = '<h1 style="text-align:center;padding:20px;">Schema tavoli creati per l\'evento </h1><br><br>';

//schemi vuoti creati per l'evento
$schemi_presenti = GQS('fl_tavoli_layout tl LEFT JOIN fl_ambienti a ON tl.ambiente_id = a.id', ' a.nome_ambiente, tl.orientamento , tl.ambiente_id , tl.id as ly_id', 'evento_id = ' . $evento_id); //vari schemi già creati

$ambienti_da_rimuovere = array();

//se non ci sono schemi presenti non mostro il titolo
if (count($schemi_presenti) == 0) {$html = '';}

foreach ($schemi_presenti as $value) {

    //raccolgo ambienti dove sono già presenti schemi
    array_push($ambienti_da_rimuovere, $value['ambiente_id']);

    $html .= '<a href="mod_layout.php?layout=1&evento_id=' . $evento_id . '&ambiente_id=' . $value['ambiente_id'] . '&orientamento=' . $value['orientamento'] . '" class="button impaginazione">SCHEMA ' . $value['nome_ambiente'] . '</a><br>';
    //$html .= '<a href="../mod_tavoli_2.0/mod_layout.php?layout=1&evento_id=' . $evento_id . '&ambiente_id=' . $value['ambiente_id'] . '&orientamento=' . $value['orientamento'] . '" class="button impaginazione">' . $value['nome_ambiente'] . ' nuova versione</a><br>';
}

$html .= '<br><br><h1 style="text-align:center;margin: 30px;{{styleTitolo}}">Schema tavoli da creare per l\'evento </h1><br><br>';

//controllo quanti ambienti ci sono
$ambienti_explode = explode(',', $_GET['ambiente_id']);
$ambienti = array_diff($ambienti_explode, $ambienti_da_rimuovere);

//se non ci sono ambienti senza schema
if (count($ambienti) == 0) {$html = str_replace('{{styleTitolo}}', 'display:none;', $html);}

foreach ($ambienti as $ambiente) {

    $ambiente_id = check($ambiente);

    $ambiente_info = GQS('fl_ambienti', '*', 'id = ' . $ambiente_id); //info sull'ambiente
    $ambiente_layout = GQS('fl_tavoli_layout', '*', 'evento_id = 0 AND ambiente_id = ' . $ambiente_id); //info sull'ambiente

    $evento_info = GQD('fl_eventi_hrc  e JOIN fl_eventi_hrc e2 ON e.`data_evento` = e2.`data_evento` AND e.`ambienti` = e2.ambienti ', 'COUNT(*) as num_eventi ', 'e.id = ' . $evento_id . ' AND e.ambienti IN(' . $ambiente_id . ') ');

    $text_button = $ambiente_info[0]['nome_ambiente'] ; //testo mostrato nel bottone quando c'è un solo evento nel giorno

    if ($evento_info['num_eventi'] < 2) {

        $options = '<option value="-1">Seleziona ...</option>';
        foreach ($ambiente_layout as $value) {
            $options .= '<option value="' . $value['id'] . '">' . $value['nome_layout'] . '</option>';
        }
        $html .= '<div style="width:60%;display: inline-block;float: left;"><form method="get" action="mod_opera.php"><h2 style="text-align:center;">' . $ambiente_info[0]['nome_ambiente'] . '</h2>';
        $html .= '<input value="' . $ambiente_id . '" name="ambiente_id" type="hidden"><div class="form_row" id="box_template_id"><p class="select_text template_id"><label for="template_id">Template Evento</label>
        <select  name="template_id" id="template_id">' . $options . '</select></div><input type="hidden" name="evento_id" value="' . $evento_id . '">';
        $html .= '<input type="submit" value="Crea da template" class="button impaginazione"></form></div>';
        $text_button = 'Crea schema vuoto';

    }

    $width = ($evento_info['num_eventi'] > 1) ? '100' : '30';

    $html .= '   <div style="width:' . $width . '%;display: inline-block;float: left;"><h2 style="text-align:center;">' . $ambiente_info[0]['nome_ambiente'] . '</h2>';
    $html .= ($evento_info['num_eventi'] > 1) ? 'Per la ' . $ambiente_info[0]['nome_ambiente'] . ' in questa data sono presenti già ' . $evento_info['num_eventi'] . ' eventi' : '';
    $html .= '  <form method="GET" action="mod_opera.php"><input type="hidden" name="ambiente_id" value="' . $ambiente_id . '">
        <input type="hidden" name="evento_id" value="' . $evento_id . '">';

    $display = '';
    $checked0 = '';
    $checked1 = '';

    if (isset($ambiente_layout[0]['orientamento'])) { //se c'è già un layout per questo ambiente in questa data
        $display = 'display:none;';
        if ($value['orientamento'] == 0) {
            $checked0 = 'checked';
        } else {
            $checked1 = 'checked';
        }
    }else{
        $text_button = 'Crea schema vuoto';
    }

    $html .= ' <div class="form_row" id="box_orientamento" style="' . $display . '">
        <p class="input_text" style="text-align:left !important;"><label for="orientamento">Orientamento</label>
        <input name="orientamento" type="radio" id="orientamento' . $evento_id . $ambiente_id . '0" value="0" required ' . $checked0 . '>
        <label for="orientamento' . $evento_id . $ambiente_id . '0" style="width: auto;" >Verticale</label>

        <input name="orientamento" type="radio" id="orientamento' . $evento_id . $ambiente_id . '1" value="1" required ' . $checked1 . '>
        <label for="orientamento' . $evento_id . $ambiente_id . '1" style="width: auto;">Orizzontale</label>


        </p>
        </div>
        <input type="hidden" value="1" name="layout">
            <input type="hidden" value="-2" name="template_id">
        <input type="submit" value="'.$text_button.'" class="button"></form>
        </form>
        </div><br><br><br>';

}

include "../../fl_inc/headers.php";

?>

<style>
.impaginazione{
    display: inline-block !important;
    float: left;
    margin-left: 46% !important;
    margin-right: 150px !important;
    height: 27px;
    padding: 6px;
}

.myh1{
    text-align: center;
    background: rgb(234, 92, 24);
    padding: 10px;
    margin: 0 30%;
    color: white;
    border-radius: 4px;
    text-align:center;
}
br{
    clear:both;
}
</style>

<div style="margin:3%;">
<h1 style="text-align:center;">Creazione schema tavoli </h1><br>
<div id="tb_id" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false" style="height:90vh;">

    <?php echo $html; ?>

</div>
