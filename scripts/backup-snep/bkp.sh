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

# Execucao do backup (-bkp)

# abre if parametro -bkp

     if [ "$1" == "-bkp" ]; then


#Maos a massa!!!!
	
     if [ -d $dir_bkp ] ;

          then
               cd $dir_bkp
          else
               $path_mk $dir_bkp
     fi

# PROXIMA ACAO - BACKUP ASTERISK

     if [ -d $dir_bkp/asterisk ] ;

          then
               cd $dir_bkp/asterisk
          else
               $path_mk $dir_bkp/asterisk &&
               cd $dir_bkp/asterisk
     fi

               $path_tar -cvf asterisk_${date}.tar.gz $dir_ast

# EFETUAR ROTATIVIDADE DE ARQUIVOS

     ult_bkp_ast=$( 

          $path_ls -tl $dir_bkp/asterisk |
          $path_egrep -v total |
          $path_awk '{print $9}' |
          $path_head -$NUM_BKP |
          $path_tr '\n' '|' |
          $path_sed  "s/.$//"
     )

     for check_ast in `${path_ls} -lt $dir_bkp/asterisk |
          ${path_awk} '{print $9}' |
          ${path_egrep} -v "${ult_bkp_ast}"`;

     do

          `${path_rm} -f $dir_bkp/asterisk/$check_ast`;

     done

# PROXIMA ACAO - BACKUP ASTERISK SOUNDS

     if [ -d $dir_bkp/asterisk_sounds ] ;

          then

               cd $dir_bkp/asterisk_sounds

          else

               $path_mk $dir_bkp/asterisk_sounds &&
               cd $dir_bkp/asterisk_sounds

     fi

	$path_tar -cvf asterisk_sounds_${date}.tar.gz $dir_ast_sounds

# EFETUAR ROTATIVIDADE DE ARQUIVOS

     ult_bkp_ast_sounds=$(

          $path_ls -tl $dir_bkp/asterisk_sounds |
          $path_egrep -v total |
          $path_awk '{print $9}' |
          $path_head -$NUM_BKP |
          $path_tr '\n' '|' |
          $path_sed  "s/.$//"

     )

     for check_ast_sounds in `${path_ls} -lt $dir_bkp/asterisk_sounds |
          ${path_awk} '{print $9}' |
          ${path_egrep} -v "${ult_bkp_ast_sounds}"`; 	

     do 

          `${path_rm} -f $dir_bkp/asterisk_sounds/$check_ast_sounds`;

     done

# PROXIMA ACAO - BACKUP DO MYSQL

     if [ -d $dir_bkp/mysql ] ;

          then

               cd $dir_bkp/mysql

          else

               $path_mk $dir_bkp/mysql &&
               cd $dir_bkp/mysql

     fi

               $path_mdump -u$user -p$passwd_user snep > snep_${date}.sql 

# EFETUAR ROTATIVIDADE DE ARQUIVOS

     ult_bkp_mysql=$(

          $path_ls -tl $dir_bkp/mysql |
          $path_egrep -v total |
          $path_awk '{print $9}' |
          $path_head -$NUM_BKP |
          $path_tr '\n' '|' |
          $path_sed  "s/.$//"

     )	

     for check_mysql in `${path_ls} -lt $dir_bkp/mysql |
          ${path_awk} '{print $9}' |
          ${path_egrep} -v "${ult_bkp_mysql}"`;

     do

          `${path_rm} -f $dir_bkp/mysql/$check_mysql`;

     done

# PROXIMA ACAO - BACKUP DO SNEP

     if [ -d $dir_bkp/snep ] ;

          then

               cd $dir_bkp/snep/

          else

               $path_mk $dir_bkp/snep &&
               cd $dir_bkp/snep
     fi
	
               $path_tar -cvf snep_${date}.tar.gz $dir_snep --exclude=arquivos

# EFETUAR ROTATIVIDADE DE ARQUIVOS

     ult_bkp_snep=$(

          $path_ls -tl $dir_bkp/snep |
          $path_egrep -v total |
          $path_awk '{print $9}' |
          $path_head -$NUM_BKP |
          $path_tr '\n' '|' |
          $path_sed  "s/.$//"

     )

     for check_snep in `${path_ls} -lt $dir_bkp/snep |
          ${path_awk} '{print $9}' |
          ${path_egrep} -v "${ult_bkp_snep}"`;

     do 

          `${path_rm} -f $dir_bkp/snep/$check_snep`;

     done

# fecha parametro -bkp

exit 0

fi

##################################################################################################
# Thats all folk's
##################################################################################################
