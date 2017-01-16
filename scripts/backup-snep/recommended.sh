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
# Recomendacoes de uso (-r)

     if [ "$1" == "-r" ]; then

          echo -en "\n**** Para garantir uma melhor usabilidade, recomendo\n"
          echo -en "colocar este script na sua crontrab, permitindo que sua\n"
          echo -en "execucao seja diaria.\n\n"
          echo -en "Para fazer isso, edite a crontab do root e insira a linha\n"
          echo -en "abaixo:\n\n"
          echo -en "00 23 * * * /var/www/html/snep/scripts/backup-snep/`basename $0` -bkp\n"
          echo -en "\nLembre-se de ceder permissao de execucao (chmod +x) e\n"
          echo -en "tambem de mover o script para o diretorio informado acima.\n\n"

     exit 0

     fi
