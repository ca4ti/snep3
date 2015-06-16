O SNEP é um software PBX baseado em Asterisk e GNU/Linux licenciado sob GPL v2 capaz de rodar em pequenos hardwares com uma boa performance. 
Possui diversos recursos de administração que darão flexibilidade, agilidade e produtividade na comunicação de voz das empresas. Pode ser customizado de acordo com a necessidade de cada negócio. Possui todas as funcionalidades de uma central telefônica de grande porte: Voice mail, gravação, roteamento avançado de ligações, cadeado, sem limites de ramais e muito mais.
----------
# Procedimentos de instalação #
O processo de instalação está baseado em Linux Debian, versão 8 (Jessie)

## Instalação do Apache ##
```
#!bash

apt-get install apache2
```

## Instalação do MySQL ##
```
#!bash

apt-get install mysql-server
```

## Instalação das dependências para o processo de compilação ##
```
#!bash

apt-get install unixodbc unixodbc-dev libmyodbc odbcinst1debian2 libcurl3 libncurses5-dev 
apt-get install php5 php5-cgi php5-mysql php5-gd php5-curl build-essential lshw libjansson-dev
apt-get install libssl-dev sox sqlite3 libsqlite3-dev libapache2-mod-php5 libxml2-dev uuid-dev
```

## Instalação do Asterisk ##
Acesse o diretório onde será descompactado o Asterisk (por exemplo: /usr/src/) e inicie o download.
```
#!bash

cd /usr/src/
wget http://downloads.asterisk.org/pub/telephony/asterisk/asterisk-13-current.tar.gz
tar xvf asterisk-13-current.tar.gz
```

## Ajustando o PHP ##
Habilite o php-cgi para receber parâmetros, inserindo "On" no parametro register_argc_argv no arquivo /etc/php5/cgi/php.ini.
Reinicie o apache
```
#!bash

/etc/init.d/apache2 restart
```


## Compilando o Asterisk ##
```
#!bash

cd /usr/src/asterisk*
./configure 
```
Ajustando compilacao do Asterisk

```
#!bash

make menuselect
```

a) selecione a opção: **Voicemail Build Options**

b) marque a opção:   [*] ODBC_STORAGE

c) tecle **ESC** 2 vezes

d) tecle **S** para confirmar alterações

e) Execute os comandos:
```
#!bash

make
make install
```

## Instalando o SNEP ##
```
#!bash

cd /var/www/html
mkdir snep
cd snep
git init
git clone __seu_repositorio_snep_ (veja instruções de como  clonar um repositório)
```

## Ajuste nas permissões ##
cd /var/www/html 
chown -R www-data.www-data snep
chmod -R 775 snep

## Ajustando os diretórios ##
```
#!bash

mkdir /var/log/snep
cd /var/log/snep
touch ui.log 
touch agi.log 
ln -s /var/log/asterisk/full full
chown -R www-data.www-data *

cd /var/www/html/snep/
ln -s /var/log/snep logs

cd /var/lib/asterisk/agi-bin/
ln -s /var/www/html/snep/agi/ snep

cd /etc/apache2/sites-enabled/
ln -s /var/www/html/snep/install/snep.apache2 001-snep

cd /var/spool/asterisk/
rm -f monitor
ln -sf /var/www/html/snep/arquivos monitor
```

## Ajustando configurações do linux ##

```
#!bash

cd /etc
rm -rf asterisk
cp -avr /var/www/html/snep/install/etc/asterisk .
cp /var/www/html/snep/install/etc/odbc* .
```

## Ajustando configurações dos sons do Asterisk ##
```
#!bash

cd /var/lib/asterisk/moh
mkdir tmp
mkdir backup
mkdir -p snep_1/tmp
mkdir -p snep_1/backup
mkdir -p snep_2/tmp
mkdir -p snep_2/backup
mkdir -p snep_3/tmp
mkdir -p snep_3/backup
chown -R www-data.www-data 

cd /usr/src
wget -c http://www.sneplivre.com.br/downloads/asterisk-sounds.tgz
tar -xzf asterisk-sounds.tgz -C /var/lib/asterisk/
cd /var/lib/asterisk/sounds
mkdir -p pt_BR/tmp
mkdir -p pt_BR/backup
mkdir -p tmp
mkdir -p backup
chown -R www-data:www-data 

mkdir -p /var/www/html/snep/sounds
cd /var/www/html/snep/sounds/
ln -sf /var/lib/asterisk/moh/ moh
ln -sf /var/lib/asterisk/sounds/pt_BR/ pt_BR
```

Criando a base de dados, usuário e dados iniciais
```
#!bash

cd /var/www/html/snep/install/database
mysql -u root -p < database.sql
mysql -u root -p snep < schema.sql
mysql -u root -p snep < system_data.sql
mysql -u root -p snep < core-cnl.sql
```

## Ajustes finais ##
Após a instalação, é preciso efetuar as seguintes modificações no linux:

**Caso seu sistema seja 32 bits:**
Arquivo: /etc/odbcinst.ini	
  --> modificar caminho para: /usr/lib/i386-linux-gnu/odbc/libmyodbc.so
Arquivo: /etc/odbc.ini	
  --> modificar caminho para: /usr/lib/i386-linux-gnu/odbc/libmyodbc.so

 

**Caso seu sistema seja 64 bits:**
Arquivo: /etc/odbcinst.ini  
  --> modificar caminho para: /usr/lib/x86_64-linux-gnu/odbc/libmyodbc.so
Arquivo: /etc/odbc.ini	   
  --> modificar caminho para: /usr/lib/x86_64-linux-gnu/odbc/libmyodbc.so