import requests
from bs4 import BeautifulSoup

endereco_impressora = '10.127.244.21'
url_printer_status = ''

# Coletar a primeira página da lista de artistas
page = requests.get('http://10.127.244.21/cgi-bin/dynamic/printer/PrinterStatus.html')
soup = BeautifulSoup(page.text, 'html.parser')
"""
Status de suprimento de papel fica específicamente sob o estilo
padding: .75pt
0 = Bandeja 1
1 = Bandeja 2
2 = MF Alimentador
3 = Bandeja Padrão

"""
resultado = soup.findAll("table", {"style": "padding: .75pt"})
bandeja_1 = resultado[0].find_all("b")
print(bandeja_1[0].contents[0])
# finalmente consegui isolar o "ok"