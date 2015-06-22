*------------------------------------------------------------------------------------
* Opens Tecnologia - Projeto Snep Livre
* Processo para incluir novas strings de traducao no arquivo messages.
* Processo para gerar/atualizar o arquivo .mo da lingua desejada
* Autor: Flavio Henrique Somensi - flavio@opens.com.br - 2015.
*------------------------------------------------------------------------------------
1) Instalar o gettext
  - para debian: apt-get install gettext

2) Execute o script shell gettext.sh que está neste diretório
   ==>> IMPORTANTE: execute o programa definindo o caminho da pesquisa
                    Exemplo: bash ./gettext.sh ../  (procura a partir da rtaiz do SNEP)
   - Este programa irá varrer todos os arquivos .php, .phtml e .xml a procura
     de strings do tipo "translate"
   - Ao final irá gerar um arquivo chamado messages.po

3) Utilize um programa do tipo Poedit ou similar para gerar as traduções na 
   linguagem desejada.
   Exemplo: Usando o Poedit
   a) Abra o arquivo .po desejado (Ex: pt_BR.po)
   b) No menu Catálogo, acesse a opção: Atualizar com base em ficheiro POT
      -> Selecione o arquivo:  messages.po
   c) Ao final, ao salvar, será gerado um arquivo pt_BR.mo

4) Copie arquivo .mo salvo pelo programa de tradução para o diretório langs 

5) Dúvidas: desenvolvimento@opens.com.br ou então no fórum do snep.
