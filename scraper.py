import requests
from bs4 import BeautifulSoup

"""
page = requests.get('http://10.127.244.21/cgi-bin/dynamic/printer/PrinterStatus.html')
soup = BeautifulSoup(page.text, 'html.parser')

Status de suprimento de papel fica específicamente sob o estilo
padding: .75pt
0 = Bandeja 1
1 = Bandeja 2
2 = MF Alimentador
3 = Bandeja Padrão

"""


def scraper_pagina(ip):
    """
    Recebe um endereço e retorna um objeto beautifulsoup
    """
    try:
        endereco = str('http://' + ip + '/cgi-bin/dynamic/printer/PrinterStatus.html')
        pagina = requests.get(endereco)
        soup = BeautifulSoup(pagina.text, 'html.parser')
        return soup
    except:
        return None


def filtragem(soup):
    """
    recebe um objeto beautifulsoup
    retorna 4 variáveis com o status das bandejas em forma de texto
    """
    if soup is None:
        print("Impressora offline")
        return None
    resultado = soup.findAll("table", {"style": "padding: .75pt"})
    bandeja_1 = resultado[0].find_all("b", text=True)[0].text
    bandeja_2 = resultado[1].find_all("b", text=True)[0].text
    return bandeja_1, bandeja_2


def executar_scan(ip, nome):
    """
    cria uma lista com o número da bandeja que não está OK
    caso a lista esteja vazia, ambas estão ok
    """
    try:
        soup = scraper_pagina(ip)
        bandeja_1, bandeja_2 = filtragem(soup)
        resultado = []
        if bandeja_1 != "OK":
            resultado.append(1)
        if bandeja_2 != "OK":
            resultado.append(2)
        return resultado
    except TypeError:
        print("Erro de conexão com a impressora ", nome)

# executar_verificacao('10.127.244.22')

"""
print("Bandeja 1: ", bandeja_1)
print("Bandeja 2: ", bandeja_2)
print("MF Alimentador: ", mf_alimentador)
print("Bandeja Padrão: ", bandeja_padrao)
"""
