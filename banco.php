<?php 
require_once("json.php");
/**
$agora = new datetime();
$timezone = new datetimezone('America/Belem');
$agora->settimezone($timezone);
#echo $agora->format('d-m-Y H:i:s');
$agora_formatado = $agora->format('Y-m-d H:i:s');
*/
function conectar_banco($configuracao){
	$conexao = mysqli_connect(
		$configuracao["banco"]["endereco"],
		$configuracao["banco"]["login"],
		$configuracao["banco"]["senha"]
		);
	mysqli_select_db ( $conexao , $configuracao["banco"]["database"] );
	if (mysqli_connect_errno()) {
	    echo "Conexão falhou " . mysqli_connect_error();
		exit();
	} else {
		#echo "Conexão com o banco estabelecida";
		return $conexao;
	}
}

function insere_registro($conexao, $agora_formatado, $nome_impressora, $total_impressoes, $toner, $kit_manutencao, $kit_rolo, $unidade_imagem){
	global $conexao;
	$query_add_reg = "INSERT into monitoramento_impressora (hora_recebido, nome_impressora, total_impressoes, toner, kit_manutencao, kit_rolo, unidade_imagem)
	VALUES ('" . $agora_formatado. "', '". $nome_impressora . "','".$total_impressoes."','".$toner."','".$kit_manutencao."','".$kit_rolo."','".$unidade_imagem."')";
	$r_add_reg = mysqli_query($conexao, $query_add_reg);

	if(!$r_add_reg){
		print("Erro: " . mysqli_error($conexao));
	} else {
		
		echo "Registro executado em ".$agora_formatado;
	}
} 
?>