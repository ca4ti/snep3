#!/bin/bash 
# -----------------------------------------------------------------------------
# Programa: movefiles - Move Arquivos de gravacao para diretorio especifico
# Copyright (c) 2008 - Opens Tecnologia - Projeto SNEP
# Licenciado sob Creative Commons. Veja arquivo ./doc/licenca.txt
# Autor: Flavio Henrique Somensi <flavio@opens.com.br>
# Comentarios: - le todos os arquivos com extensao wav que estao
#                dentro de /var/www/snep/arquivos; 
#              - faz uma lista baseada na data destes arquivos; 
#              - cria um sub-diretorio para cada data encontrada
#              - move os arquivos para seus respectivos sub-diretorios
#              - Se estiver definido, converte arquivos para MP3
#                ( Define-se na interface: Configuracoes >> Parametros)
#              _ Tenta encontrar outro disco montado em: 
#                /var/www/snep/arquivos/storage
#
# A execucao pode estar agendada no cron (/var/spool/cron/crontabs/root)
# Exemplo: 
# 59 23 * * * /var/www/snep/scripts/movefiles.sh
# -----------------------------------------------------------------------------

CAT=$(which  cat)
CUT=$(which cut)
MP3=`${CAT} /var/www/snep/includes/setup.conf | grep record_mp3 | ${CUT} -d'=' -f 2 | tr -d ' ' | tr -d '"'`

# Funcao que move arquivos para storage
mover() {
cd $1
dst=$2

find -maxdepth 2 -type f -iregex '.*\.\(WAV\|wav\)$' -exec ls -lh --time-style=long-iso  '{}' \; | while read per o owner group size date time file
do

   if [ ! -d $dst"/"$date ];then
      echo -en "Criando diretorio $date..."
      mkdir  $dst"/"$date
   fi

   echo "Nome do arquivo" $file

   if [ "$MP3" == "true" ];then

      lame -b 32 $file $dst"/"$date"/"`basename $file .wav`.mp3
	
      arquivo=$dst"/"$date"/"`basename $file .wav`.mp3

      if [ ! -f $arquivo ];then
          sox $file -t wav -s - | lame -b 32 - $dst"/"$date"/"`basename $file .wav`.mp3
      fi

      siz=`du $arquivo |cut -f1`
      if [ -f $arquivo ];then
          if [ $siz != "0" ];then
                rm $file
          fi
      fi

   else
      mv $file $dst"/"$date"/"
   fi
done

chown -R www-data.www-data *
}


# verifica discos montados em como storage {storage1, storage2}
echo | df -P -h | grep storage | awk '{print $6 ":"$5}' > storages.swp
#verifica espoaço disponivel onde esta montado /var/www/snep/arquivos
echo | df -P /var/www/snep/arquivos | tail -1 | awk '{print $6 ":"$5}' >> storages.swp

# percorre cada um dos registros encontrados
for i in `cat storages.swp ` ; do

    # retorna posicao de caracteries dentro da string ':' e '%'
    if [ -n $i ]; then
    	xx=`echo | expr index $i :`
	xy=`echo | expr index $i %`
        
	# corta strings
        string=${i:0:$xy-1}
        mount=${string:0:$xx-1}
        porcent=${string:$xx:$xy}
        host=$(cat /etc/hostname )

        if [ $porcent -lt "97" ]; then
            mover "/var/www/snep/arquivos" $mount
        else
            echo "Storage perto do limite de espaco | mail -s "Storage perto do limite de espaço em $host " suporte-snep@opens.com.br"
	    echo "Storage: " $mount "com espaco insuficiente."
            break
        fi
    else
        echo "Nenhum Storage encontrado | mail -s  "Nenhum Storage encontrado em $host "  rafael@opens.com.br"
	echo "Nenhum Storage montado em /var/www/snep/arquivos/"
    fi

done
