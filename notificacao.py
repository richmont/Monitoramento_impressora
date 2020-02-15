from win10toast import ToastNotifier
import time


def mostrar_notificacao(titulo, texto, duracao):
    """
    Exibe notificação na área de trabalho Windows
    """
    toaster = ToastNotifier()
    toaster.show_toast(titulo,
                       texto,
                       icon_path=None,
                       duration=duracao,
                       threaded=True)
    while toaster.notification_active():
        time.sleep(0.1)


# mostrar_notificacao("titulo massa", "kkkkkkkkkkkkkkkkkkkk", 20)
