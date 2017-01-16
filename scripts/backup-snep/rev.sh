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

     if [ "$1" == "-rev" ]; then


#Criando o diretorio para descompactar os bkps

dir_rev_bkp=/home/rev_bkp

     if [ -d "$dir_rev_bkp" ] ; then
               echo -en "\nOK.... Processando...\n"
          else
               $path_mk $dir_rev_bkp
     fi

# Opcoes de restauracao

          echo -en "\nDigite a opcao desejada\n"
          echo " ( 1 ) Recuperar backup somente do Asterisk"
          echo " ( 2 ) Recuperar backup somente do SNEP"
          echo " ( 3 ) Recuperar backup somente do MySQL do Snep"
          echo " ( 4 ) Recuperar backup somente do Asterisk Sounds"
          echo -en "\nInsira a opcao:\n"

          read opcao_bkp

# Se digitar 1, vamos voltar o backup do Asterisk

               if [ "$opcao_bkp" != 1 ] ; then

                    echo "Por favor, tente novamente com uma opcao valida"

               fi

                    if [ "$opcao_bkp" == "1" ] ; then

                         echo "Beleza, vamos voltar o backup do Asterisk. Vou precisar que voce me informe qual arquivo de backup"
                         echo -en "Informe o diretorio completo, exemplo: /home/backup/asterisk/arquivodebackup.tar.gz\n"
                         echo -en "\nLembre-se, o arquivo deve estar compactado no formato tar.gz\n"

                    read rev_ast
                              if [ "$rev_ast" != "" ] ; then

# Iniciando a substituicao de arquivos 


                                   $path_tar -xvf $rev_ast -C $dir_rev_bkp
                                   $path_mv $dir_ast $dir_ast-$date/
                                   $path_mv $dir_rev_bkp/$dir_ast $dir_ast/

                              else

                                   echo Por favor, tente novamente informando um caminho valido.

                              fi

                    fi

# Se digitar 2, vamos voltar o backup do Snep

               if [ "$opcao_bkp" != 2 ] ; then

                    echo "Por favor, tente novamente com uma opcao valida"

               fi

                    if [ "$opcao_bkp" == "2" ] ; then

                         echo "Beleza, vamos voltar o backup do SNEP. Vou precisar que voce me informe qual arquivo de backup"
                         echo -en "Informe o diretorio completo, exemplo: /home/backup/asterisk/arquivodebackup.tar.gz\n"
                         echo -en "\nLembre-se, o arquivo deve estar compactado no formato tar.gz\n"

                    read rev_snep
                              if [ "$rev_snep" != "" ] ; then

# Iniciando a substituicao de arquivos 


                                   $path_tar -xvf $rev_snep -C $dir_rev_bkp
                                   $path_mv $dir_snep $dir_snep-$date/
                                   $path_mv $dir_rev_bkp/$dir_snep $dir_snep/

                              else

                                   echo Por favor, tente novamente informando um caminho valido.

                              fi

                    fi


# Se digitar 3, vamos voltar o backup do MySQL

               if [ "$opcao_bkp" != 3 ] ; then

                    echo "Por favor, tente novamente com uma opcao valida"

               fi

                    if [ "$opcao_bkp" == "3" ] ; then

                         echo "Beleza, vamos voltar o backup do MySQL. Vou precisar que voce me informe qual arquivo de backup"
                         echo -en "Informe o diretorio completo, exemplo: /home/backup/asterisk/arquivodebackup.tar.gz\n"
                         echo -en "\nLembre-se, o arquivo deve estar compactado no formato tar.gz\n"

                    read rev_mysql
                              if [ "$rev_mysql" != "" ] ; then

# Iniciando a substituicao de arquivos 


                                   $path_mdump -u$user -p$passwd_user snep > $dir_bkp/backup_snep_$date.sql -v
                                   $path_mysql -u$user -p$passwd_user snep < $rev_mysql -v

                              else

                                   echo Por favor, tente novamente informando um caminho valido.

                              fi

                    fi

# Se digitar 4, vamos voltar o backup do Asterisk Sounds

               if [ "$opcao_bkp" != 4 ] ; then

                    echo "Por favor, tente novamente com uma opcao valida"

               fi

                    if [ "$opcao_bkp" == "4" ] ; then

                         echo "Beleza, vamos voltar o backup do Asterisk Sounds. Vou precisar que voce me informe qual arquivo de backup"
                         echo -en "Informe o diretorio completo, exemplo: /home/backup/asterisk/arquivodebackup.tar.gz\n"
                         echo -en "\nLembre-se, o arquivo deve estar compactado no formato tar.gz\n"

                    read rev_ast_sounds
                              if [ "$rev_ast_sounds" != "" ] ; then

# Iniciando a substituicao de arquivos 


                                   $path_tar -xvf $rev_ast_sounds -C $dir_rev_bkp
                                   $path_mv $dir_ast_sounds $dir_ast_sounds-$date/
                                   $path_mv $dir_rev_bkp/$dir_ast_sounds $dir_ast_sounds/

                              else

                                   echo Por favor, tente novamente informando um caminho valido.

                              fi

                    fi

# Remover arquivos descompactados

     $path_rm $dir_rev_bkp
     echo -en "\nOs arquivos temporarios foram removidos....\n"


# Fechamento -rev

     exit 0

     fi
















##################################################################################################
# Thats all folk's
##################################################################################################
