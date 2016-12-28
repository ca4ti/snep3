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
# [WARNING] A execucao e por sua conta e risco!
#
# Para executar, use;
#
# bash backup-X.sh -bkp
# ou
# ./backup-X.sh -bkp 
#
# Este script tem como objetivo fazer o backup dos seguintes:
#
# * Banco de dados completo do Snep - COM CDR
# * Arquivos de configuracao do Asterisk
# * Arquivos de SOM do Asterisk
# * Arquivos de configuracao do SNEP
# * Restauracao de backups ja realizados
#
BASEDIR=$(dirname "$0")
#
# Importar variaiveis com ferramentas do sistema
source $BASEDIR/variables.sh
# Importar dados que devem ser salvos
source $BASEDIR/directories.sh
# Importar configuracoes de usuario
source $BASEDIR/info.conf
# Importar funcionalidades
source $BASEDIR/options.sh
# Importar funcionalidade -a
source $BASEDIR/author.sh
# Importar funcionalidade de recomendacao
source $BASEDIR/recommended.sh
# Importar funcionalidade de versao
source $BASEDIR/version.sh
# Importar funcionalidade de backup
source $BASEDIR/bkp.sh
# Importar funcionalidade de restauracao de backup
source $BASEDIR/rev.sh
