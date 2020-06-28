<?php
require_once("json.php");
require_once("banco.php");
function email($destinatario,$remetente,$assunto,$mensagem){
    $mensagem_completa = "
    <html>
    <head>
    <title>[Monitoramento Impressora]</title>
    </head>
    <body>
    <p>$mensagem</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    # remetente do email
    $headers .= "From: <$remetente>" . "\r\n";

    mail($destinatario,$assunto,$mensagem_completa,$headers);
}
# Avisa se a leitura do arquivo json não funcionou
# decodificação incorreta do json gera uma variável NULL
$configuracao = ler_json("config.json");
if(is_null($configuracao)){
    echo "Por favor, verifique o arquivo de configuração config.json";
    # retorna erro do servidor
    http_response_code(500);
} else{
    $agora = new datetime();
    $timezone = new datetimezone($configuracao["fuso_horario"]);
    $agora->settimezone($timezone);
    $agora_formatado = $agora->format('Y-m-d H:i:s');

    # configura o endereço de email
    $destinatario = $configuracao["email"];
    $remetente = $destinatario;
    # Se o parâmetro total_impressoes não foi definido ou não tem conteúdo (empty() retorna true)
    # Mostra uma mensagem de erro, envia um email avisando no CPD e retorna status 400
    if(!isset($_REQUEST["total_impressoes"]) || empty($_REQUEST["total_impressoes"])){
        $assunto = "[Monitoramento Impressora] Total de páginas ausente $agora_formatado";
        $mensagem = "Número de páginas ausente na requisição";
        # operador ternário verifica se na config.json, o envio de email está autorizado
        $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
        http_response_code(400); 
        echo "Verifique a variável 'total_impressoes' e tente novamente";
    } else {
        if(!isset($_REQUEST["nome_impressora"]) || empty($_REQUEST["nome_impressora"])){
            $assunto = "[Monitoramento Impressora] Nome da impressora ausente $agora_formatado";
            $mensagem = "Nome da impressora ausente na requisição";
            $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
            http_response_code(400); 
            echo "Verifique a variável 'nome_impressora' e tente novamente";
        } else {
            if(!isset($_REQUEST["hostname"]) || empty($_REQUEST["hostname"])){
                $assunto = "[Monitoramento Impressora] Hostname ausente $agora_formatado";
                $mensagem = "Hostname ausente na requisição";
                $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
                http_response_code(400); 
                echo "Verifique a variável 'hostname' e tente novamente";
            } else {
                if(!isset($_REQUEST["toner"]) || empty($_REQUEST["toner"])){
                    $assunto = "[Monitoramento Impressora] toner ausente $agora_formatado";
                    $mensagem = "toner ausente na requisição";
                    $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
                    http_response_code(400); 
                    echo "Verifique a variável 'toner' e tente novamente";
                } else {
                    if(!isset($_REQUEST["kit_manutencao"]) || empty($_REQUEST["kit_manutencao"])){
                        $assunto = "[Monitoramento Impressora] kit_manutencao ausente $agora_formatado";
                        $mensagem = "kit_manutencao ausente na requisição";
                        $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
                        http_response_code(400); 
                        echo "Verifique a variável 'kit_manutencao' e tente novamente";
                    } else {
                        if(!isset($_REQUEST["unidade_imagem"]) || empty($_REQUEST["unidade_imagem"])){
                            $assunto = "[Monitoramento Impressora] unidade_imagem ausente $agora_formatado";
                            $mensagem = "unidade_imagem ausente na requisição";
                            $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
                            http_response_code(400); 
                            echo "Verifique a variável 'unidade_imagem' e tente novamente";
                        } else {
                            if(!isset($_REQUEST["kit_rolo"]) || empty($_REQUEST["kit_rolo"])){
                                $assunto = "[Monitoramento Impressora] kit_rolo ausente $agora_formatado";
                                $mensagem = "kit_rolo ausente na requisição";
                                $configuracao["envio_email"] ? email($destinatario, $remetente, $assunto, $mensagem) :
                                http_response_code(400); 
                                echo "Verifique a variável 'kit_rolo' e tente novamente";
                            } else {
                                $conexao = conectar_banco($configuracao);
                                insere_registro($conexao, $agora_formatado, $_REQUEST["nome_impressora"], $_REQUEST["total_impressoes"], $_REQUEST["toner"], $_REQUEST["kit_manutencao"], $_REQUEST["kit_rolo"], $_REQUEST["unidade_imagem"]);
                                echo "Recebido com sucesso ";
                        }
                    }
                }
            }
        }
    }
}}
?>