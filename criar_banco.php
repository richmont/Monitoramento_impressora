<?php
require_once("banco.php");
require_once("json.php");
function criar_tabela_monitoramento_impressora($conexao){
	$query_criar_tabela_monitoramento_impressora = 
	"CREATE TABLE IF NOT EXISTS monitoramento_impressora ( 
	id int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	nome_impressora VARCHAR(10),
	toner tinyint(3),
	kit_manutencao tinyint(3),
	kit_rolo tinyint(3),
	unidade_imagem tinyint(3),
	total_impressoes int(10),
	hora_recebido datetime);";

	$r_criar_monitoramento_impressora = mysqli_query($conexao, $query_criar_tabela_monitoramento_impressora);
	if(!$r_criar_monitoramento_impressora){
		print("Erro: " . mysqli_error($conexao));
	} else {
		echo "<br>Tabela monitoramento_impressora criada com sucesso<br>";
	}
}

$configuracao = ler_json("config.json");
if(is_null($configuracao)){
    echo "Por favor, verifique o arquivo de configuração config.json";
} else{
	$conexao = conectar_banco($configuracao);
    criar_tabela_monitoramento_impressora($conexao);
}
?>