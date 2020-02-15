
import json
import logging
from infi.systray import SysTrayIcon
configuracao_json = 'config.json'
hover_text = "Status Impressoras"


def hello(sysTrayIcon):
    print("Hello World.")


# tupla base onde serão acrescentadas as impressoras detectadas



def encerra(sysTrayIcon):
    print('Encerrando programa')


def abrir_json(configuracao_json):

    """
    lê as configurações do usuário para as impressoras
    """
    try:
        file = open(configuracao_json)
        dados = json.load(file)
        return dados
    except FileNotFoundError:
        raise FileNotFoundError("Arquivo de configuração não encontrado")


if __name__ == "__main__":
    print("Inicializando")
    impressoras = abrir_json(configuracao_json)
    print("Abrindo configurações do arquivo json")
    print("Número de impressoras detectadas: ", len(impressoras))
    print()
    """
    Recebe o nome de todas as impressoras do json
    armazena em uma lista temporária no formato
    suportado pela biblioteca SystrayIcon
    que depois será convertido em uma tupla
    esta tupla é exibida na forma de submenu Impressoras
    """
    lista_tuplas = []
    for i in impressoras:
        print(i['nome'])
        tupla = (i['nome'], None, hello)
        lista_tuplas.append(tupla)
    menu_options = (('Verificar agora', None, hello), ('Impressoras', None, tuple(lista_tuplas)))

    
    sysTrayIcon = SysTrayIcon("printer.ico", hover_text, menu_options, on_quit=encerra, default_menu_index=1)
    sysTrayIcon.start()
