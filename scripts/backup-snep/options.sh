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
#
# OPCOES DE EXECUCAO - NAO ALTERE!!
#
#
# Execucao simples - ./backup-X.sh e execucao com -h

     if
          [ "$1" == "" ] ||
          [ "$1" == "-h" ] ; 

               then

          echo -en "\n**** Para iniciar o processo de backup\n"
          echo -en "Use: ./`basename $0` -bkp ou bash `basename $0` -bkp\n"
          echo -en "\n"
          echo -en "\n**** Para iniciar o processo de restauracao backup\n"
          echo -en "Use: ./`basename $0` -rev ou bash `basename $0` -rev\n"
          echo -en "\n"
          echo -en "\n**** DEMAIS PARAMETROS ****\n"
          echo -en "\n -v Versao do programa"
          echo -en "\n -r Recomendacoes de uso"
          echo -en "\n -a Informacoes sobre autor\n\n"

     exit 0

     fi
