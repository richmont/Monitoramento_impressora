<?php 
function ler_json($arquivo){
    $arquivo_aberto = file_get_contents($arquivo);
    $array_json = json_decode($arquivo_aberto, true);
    return $array_json;
}
?>