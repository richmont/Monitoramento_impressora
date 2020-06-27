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
    Trim corta todos os caracteres extras, deixando apenas os numerais da porcentagem
    Retorna um número de 1 a 100
    #>
    $requisicao = Invoke-WebRequest -Uri "$ip/cgi-bin/dynamic/printer/PrinterStatus.html"
    $toner = $requisicao.allelements | Where tagname -eq "B" | Select -Skip 3 -First 1 -ExpandProperty outerText
    Return $toner.Trim("Cartucho Preto ~%")
}

function Get-Kit-do-Rolo($ip){
    <#
    Baixa o conteúdo da página de estatísticas da impressora
    Exibe todas as tags <TD> e conta até a 50ª
    O conteúdo dessa tag contém o nível do kit do rolo (não me pergunte o que é)
    Trim corta todos os caracteres extras, deixando apenas os numerais da porcentagem
    Retorna um número de 1 a 100
    #>
    $requisicao = Invoke-WebRequest -Uri "$ip/cgi-bin/dynamic/printer/PrinterStatus.html"
    $kit_do_rolo = $requisicao.allelements | Where tagname -eq "TD" | Select -Skip 50 -First 1 -ExpandProperty outerText
    Return $kit_do_rolo.Trim("%")
}

function Get-Kit-Manutencao($ip){
    <#
    Baixa o conteúdo da página de estatísticas da impressora
    Exibe todas as tags <TD> e conta até a 48ª
    O conteúdo dessa tag contém o nível do kit de manutenção
    Trim corta todos os caracteres extras, deixando apenas os numerais da porcentagem
    Retorna um número de 1 a 100
    #>
    $requisicao = Invoke-WebRequest -Uri "$ip/cgi-bin/dynamic/printer/PrinterStatus.html"
    $kit_de_manutencao = $requisicao.allelements | Where tagname -eq "TD" | Select -Skip 48 -First 1 -ExpandProperty outerText
    Return $kit_de_manutencao.Trim("%")
}

function Get-Unidade-Imagem($ip){
    <#
    Baixa o conteúdo da página de estatísticas da impressora
    Exibe todas as tags <TD> e conta até a 52ª
    O conteúdo dessa tag contém o nível da unidade de imagem
    Trim corta todos os caracteres extras, deixando apenas os numerais da porcentagem
    Retorna um número de 1 a 100
    #>
    $requisicao = Invoke-WebRequest -Uri "$ip/cgi-bin/dynamic/printer/PrinterStatus.html"
    $unidade_de_imagem = $requisicao.allelements | Where tagname -eq "TD" | Select -Skip 52 -First 1 -ExpandProperty outerText
    Return $unidade_de_imagem.Trim("%")
}

function Requisicao{
    param
    (   
        $toner,
        $api_url,
        $num_pagina,
        $hostname,
        $nome_impressora,
        $kit_de_manutencao,
        $unidade_de_imagem,
        $kit_do_rolo
       
    )
    <#
    Recebe os valores pra URL da API Rest preparada pra tratar as informações enviadas
    O número total de impressões e o nome da impressora
    Envia através de requisição GET e retorna o resultado da requisição
    #>
    #write-host $api_url"?num_pagina=$num_pagina&hostname=$hostname&nome_impressora=$nome_impressora&toner=$toner&kit_de_manutencao=$kit_de_manutencao&unidade_de_imagem=$unidade_de_imagem&kit_do_rolo=$kit_do_rolo"
    $resposta = Invoke-WebRequest -Uri $api_url"?num_pagina=$num_pagina&hostname=$hostname&nome_impressora=$nome_impressora&toner=$toner&kit_de_manutencao=$kit_de_manutencao&unidade_de_imagem=$unidade_de_imagem&kit_do_rolo=$kit_do_rolo"
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
    try{
        $num_pagina = Get-Contador-Paginas($x.ip)
    } catch{
        Write-Host "Erro ao coletar número de página para a impressora" $x.nome
    }
    try{
        $toner = Get-Toner($x.ip)
        
    } catch{
        Write-Host "Erro ao coletar o medidor de tonner"
    }

    try{
        $kit_de_manutencao = Get-Kit-Manutencao($x.ip)
        
    } catch{
        Write-Host "Erro ao coletar o medidor do kit de manutenção"
    }

    try{
        $kit_do_rolo = Get-Kit-do-Rolo($x.ip)
        
    } catch{
        Write-Host "Erro ao coletar o medidor de kit do rolo"
    }

    try{
        $unidade_de_imagem = Get-Unidade-Imagem($x.ip)
        
    } catch{
        Write-Host "Erro ao coletar o medidor da Unidade de Imagem"
    }

    # envio da requisição dentro de um try pra capturar o erro do servidor e retornar mensagem de erro
    # erro também será tratado no servidor pra notificar usuários
    try {
        
        Requisicao -num_pagina $num_pagina -hostname $hostname -nome_impressora $x.nome -api_url $api_url -toner $toner -unidade_de_imagem $unidade_de_imagem -kit_de_manutencao $kit_de_manutencao -kit_do_rolo $kit_do_rolo
    }
    catch {
        Write-Host "Erro ao enviar requisição para a impressora" $x.nome
    }
    
}

