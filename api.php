<?php
require_once("json.php");

function email($destinatario,$remetente,$assunto,$mensagem){
    $mensagem_completa = "
    <html>
    <head>
    <title>This is a test HTML email</title>
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
    http_response_code(500);
} else{
    # configura o endereço de email
    $destinatario = $configuracao["email"];
    $remetente = $destinatario;
    # Se o parâmetro num_pagina não foi definido ou não tem conteúdo (empty() retorna true)
    # Mostra uma mensagem de erro, envia um email avisando no CPD e retorna status 400

    if(!isset($_REQUEST["num_pagina"]) || empty($_REQUEST["num_pagina"])){
        $assunto = "[Monitoramento Impressora] Total de páginas ausente <inserir hora>";
        $mensagem = "Número de páginas ausente na requisição";
        #email($destinatario, $remetente, $assunto, $mensagem);
        http_response_code(400); 
        echo "Verifique a variável 'num_pagina' e tente novamente";
    } else {
        if(!isset($_REQUEST["nome_impressora"]) || empty($_REQUEST["nome_impressora"])){
            $assunto = "[Monitoramento Impressora] Nome da impressora ausente <inserir hora>";
            $mensagem = "Nome da impressora ausente na requisição";
            #email($destinatario, $remetente, $assunto, $mensagem);
            http_response_code(400); 
            echo "Verifique a variável 'nome_impressora' e tente novamente";
        } else {
            if(!isset($_REQUEST["hostname"]) || empty($_REQUEST["hostname"])){
                $assunto = "[Monitoramento Impressora] Hostname ausente <inserir hora>";
                $mensagem = "Hostname ausente na requisição";
                #email($destinatario, $remetente, $assunto, $mensagem);
                http_response_code(400); 
                echo "Verifique a variável 'hostname' e tente novamente";
            } else {
        echo "Recebido com sucesso";
            }
        }
    }
}

?>