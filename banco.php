<?php 
require_once("json.php");
function conectar_banco($configuracao){
	$conexao = mysqli_connect(
		$configuracao["endereco"],
		$configuracao["login"],
		$configuracao["senha"]
		);
	mysqli_select_db ( $conexao , $configuracao["database"] );
	if (mysqli_connect_errno()) {
	    echo "Conexão falhou " . mysqli_connect_error();
		exit();
	} else {
		#echo "Conexão com o banco estabelecida";
		return $conexao;
	}
}



?>