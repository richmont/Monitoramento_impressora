import requests
from bs4 import BeautifulSoup


# Coletar a primeira página da lista de artistas
page = requests.get('http://10.127.244.21/cgi-bin/dynamic/printer/PrinterStatus.html')
soup = BeautifulSoup(page.text, 'html.parser')
# print(soup)
tabela = soup.findAll("table", {"class": "status_table"})
b_tag = tabela[2].find_all("b")
print(b_tag)

"""
contador = 0
for b in b_tag:
	print(b)
	contador += 1
	print("contador: ",contador)
"""