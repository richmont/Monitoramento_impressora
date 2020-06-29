ip_impressora=$1
# Armazena o conteúdo da página em um arquivo temporário
tmp_suprimento=$(curl -s http://$ip_impressora/cgi-bin/dynamic/printer/PrinterStatus.html)
# filtra e exibe o conteúdo da tag sobre papel
echo "$tmp_suprimento" | grep "padding: .75pt" | egrep -o "<b>.*?</b>" | head -2
# filtra e exibe as tags de cartucho
echo "$tmp_suprimento" | grep "Cartucho Preto" | egrep -o "<B>.*?</B>"
# Armazena o conteúdo da página em um arquivo temporário
tmp_total_impressoes=$(curl -s http://$ip_impressora/cgi-bin/dynamic/printer/config/reports/devicestatistics.html)
# filtra e exibe a quantidade total de impressões
echo "$tmp_total_impressoes" | grep "Total" | head -5 | tail -1|  egrep -o "<p>.*?</p>"
