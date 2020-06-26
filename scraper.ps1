<# 
Script em Powershell que executa scraping na página de estatística de impressoras do modelo Lexmark MS811
Envia requisição GET com valor do contador de páginas para uma API em PHP que grava em banco de dados
#>

function Get-Config-Json($arquivo){
    <#
    recebe as configurações do sistema através do config.json
    Retorna uma lista
    #>
    $jason = Get-Content $arquivo | ConvertFrom-Json
    return $jason
}

function Get-Contador-Paginas($ip) {
    <#
    Baixa o conteúdo da página de estatísticas da impressora
    Exibe todas as tags <p> e conta até a 16ª
    O conteúdo dessa tag contém o total de impressões feitas pela impressora
    Corta um caractere de espaço no fim do número de impressões, retorna
    #>
    $requisicao = Invoke-WebRequest -Uri "$ip/cgi-bin/dynamic/printer/config/reports/devicestatistics.html"
    $contagem = $requisicao.allelements | Where tagname -eq "p" | Select -Skip 15 -First 1 -ExpandProperty outerText
    Return $contagem.TrimEnd(' ')
}

function Get-Toner($ip){
    <#
    Baixa o conteúdo da página de estatísticas da impressora
    Exibe todas as tags <b> e conta até a 3ª
    O conteúdo dessa tag contém o nível do toner
    Corta o texto entre "~" e "%", pra sobrar apenas o numeral
    #>
    $toner = $requisicao.allelements | Where tagname -eq "B" | Select -Skip 3 -First 1 -ExpandProperty outerText
    Return $toner.Split("~")[1].TrimEnd("%")
}

function Requisicao{
    param
    (
        $api_url,
        $num_pagina,
        $hostname,
        $nome_impressora
       
    )
    <#
    Recebe os valores pra URL da API Rest preparada pra tratar as informações enviadas
    O número total de impressões e o nome da impressora
    Envia através de requisição GET e retorna o resultado da requisição
    #>
    #write-host $api_url"?num_pagina=$num_pagina&hostname=$hostname&nome_impressora=$nome_impressora"
    $resposta = Invoke-WebRequest -Uri $api_url"?num_pagina=$num_pagina&hostname=$hostname&nome_impressora=$nome_impressora"
    Return $resposta

}
# Lê as configurações do arquivo
$configuracao = Get-Config-Json("config.json")
$api_url = $configuracao.api_url
# O nome de máquina do computador onde o script foi executado
$hostname = $env:computername
# percorre a lista de impressoras e realiza a coleta do contador de páginas em cada uma delas
foreach($x in $configuracao.impressoras){
    # parâmetros individuais da impressora são executados dentro do foreach
    # nome e ip
    $num_pagina = Get-Contador-Paginas($x.ip)
    # envio da requisição dentro de um try pra capturar o erro do servidor e retornar mensagem de erro
    # erro também será tratado no servidor pra notificar usuários
    try {
        Requisicao -num_pagina $num_pagina -hostname $hostname -nome_impressora $x.nome -api_url $api_url
    }
    catch {
        Write-Host "Erro ao enviar requisição para a impressora" $x.nome
    }
    
}

