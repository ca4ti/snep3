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
# GRUPO DE VARIAVEIS 2 - Neste trecho, temos todos os diretorios que serao salvos pelo programa;
# ALTERE SOMENTE SE SOUBER O QUE ESTA FAZENDO

dir_bkp=/home/backup
dir_ast=/etc/asterisk
dir_ast_sounds=/var/lib/asterisk/sounds
dir_snep=/var/www/html/snep

dir_rev_bkp=/home/rev_bkp
