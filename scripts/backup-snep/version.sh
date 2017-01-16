#!/bin/bash
#
# backup-X.sh - Programa feito para realizar o Backup completo do Snep na sua versao 3.X
#
# Autor: Anderson Freitas <tmsi.freitas@gmail.com>
# Site: http://www.dialplanreload.com/
# Repositorio: https://github.com/DialplanReload/backup-snep
#
# Revisao: Daian Conrad <daian.conrad@gmail.com>
#
# Desenvolvido sob licensa GPL. 
# Fique a vontade para contribuir com a evolucao deste programa.
#
#-----------------------------------------------------------------------------------------------


# Versao do script (-v)

     if [ "$1" == "-v" ]; then

          echo -en "\n**** Programa para realizar backup automatico do Snep\n"
          echo -en "\n Versao $bkp_version\n\n"
          echo -en "\nPara mais opcoes, execute o script sem nenhum parametro ou com -h\n\n"

     exit 0

     fi
