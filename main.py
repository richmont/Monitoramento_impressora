
import json
from scraper import executar_scan
from notificacao import mostrar_notificacao
from infi.systray import SysTrayIcon
configuracao_json = 'config.json'
hover_text = "Status Impressoras"


def hello(sysTrayIcon):
    print("Hello World.")


def encerra(sysTrayIcon):
    print('Encerrando programa')


def verificar_todas(SysTrayIcon):
    for i in impressoras:
        # exibe em tela os nomes das impressoras detectadas
        print(i['nome'])
        tupla = (i['nome'], None, hello)
        lista_tuplas.append(tupla)
        resultado = executar_scan(i['ip'], i['nome'])
        if len(resultado) == 0:
            # tudo ok, impressora com papel
            pass
        else:
            for bandeja in resultado:
                titulo = str(i['nome']) + " sem papel"
                texto = "Verificar a bandeja " + str(bandeja)
                mostrar_notificacao(titulo, texto, 3)


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
        # exibe em tela os nomes das impressoras detectadas
        print(i['nome'])
        tupla = (i['nome'], None, hello)
        lista_tuplas.append(tupla)
        resultado = executar_scan(i['ip'], i['nome'])
        try:
            if len(resultado) == 0:
            # tudo ok, impressora com papel
                pass
            else:
                for bandeja in resultado:
                    titulo = str(i['nome']) + " sem papel"
                    texto = "Verificar a bandeja " + str(bandeja)
                    mostrar_notificacao(titulo, texto, 3)
        except TypeError:
            continue
    menu_options = (('Verificar agora', None, verificar_todas), ('Impressoras Monitoradas', None, tuple(lista_tuplas)))
    sysTrayIcon = SysTrayIcon("printer.ico", hover_text, menu_options, on_quit=encerra, default_menu_index=1)  
    sysTrayIcon.start()
        
