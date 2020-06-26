# Monitoramento de impressora
Script em Powershell que executa scraping na página de estatística de impressoras do modelo Lexmark MS811  
Envia requisição GET com valor do contador de páginas para uma API em PHP que grava em banco de dados  
Concebido para ser executado diariamente em horário fixo, pra que seja feita comparação do contador  
revelando quantas páginas foram impressas nesta impressora naquele dia  