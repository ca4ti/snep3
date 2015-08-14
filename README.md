**TODO**

- Tradução e revisão das traduções Pt-BR, EN, ES
- Correção no comportamento do menu principal
- Refatorar rotina de Rotas / Regras Negócio
- Separar por ABAS: Entradas, Saidas, Outras
- Sistema de Ajuda on-line
- Manual do usuário
- Ajustes CSS
- Rever/redefinir conceitos/funcionalidades para:
  - Grupos de Ramais
  - Centros de Custos
---------------------------------
**Bugs/Ajustes**
FEITO - A opção NAT pode ser combinada (checkbox). Não é única (radiobox). 
FEITO - Status do Sistema - não está exibindo o status dos troncos corretamente
FEITO - Parâmetros : Não altera a variável "country-code"
FEITO - Centro de Custos - descrição com 30 casas somente (aumentar) / Ver tabela BD
FEITO - Grupo de Contatos:  Trunca o nome (view x tabela BD)
FEITO - Musica de espera : Erro no Banco de dados, Erro ao gravar arquivo/criar sessão
FEITO - Relatorio de chamadas:  não está paginando, css para ouvir gravações/fazer download está desalinhado
FEITO - Relatório de serviços: incluir cadeado, agenda, etc
FEITO - Ajustar Regra de negócio padrão na instalação - Não cria ações para a Regra "Internas - Ramal a Ramal"
FEITO - Asterisk parado não dá mensagem de erro, a tela fica em branco somente (Ex. Cadastro Ramais, troncos, etc)
FEITO - Ramais Cadastro - Usa grupo Ramal = Usuários mas exibe sempre como "Administradores"

*IMPORTANTES*
- Tronco SNEPSip não funciona
- Ajustar o /etc/hosts na instalação
- Ramais:
  - Cadeado : Ao incluir senha e marcar checkbox não funciona (ERRO de AGI). Ao alterar desmarcando o checkbox e deixando somente a senha, funciona normalmente
- Estacionamento: Não funciona como está documentado (700). Rever documentação/definir novo padrão ou ajustar funcionamento padrão
- Contatos : usando tabela core-state ao invés de core-cnl-state. Rever duplicidade de informações
- Arquivos de Som do Asterisk - rever a rotina
- Filas: Não funciona os anuncios ao chamador (tipo: Você é o proximo a ser atendido....")

*RELEVANTES*
- Opção de NAT - DirectMEdia - Ver conceitos de cada opção, Definir DEFAULT.
- Revisar o comportamento do asterisk (AGI's) quando usa / altera Language - tem locais que o CHANNEL(language) é fixado em pt-BR. Ver rotina lib/PBX/Rule.php
- Arrumar os ícones para enable/disable das permissões do usuário
- Menu Usuário fica "sob" Status do sistema. Tem que ser "sobre"
- Regras de Negócio / Ações: Mostra 10 ações e esconde a partir disso - rever rolagem
- Status do sistema: Avisar que tem problemas de permissões, links etc
- Sala de Conferências: Criar opção para exibir ou não as mensagens/ nomes dos participantes
- Music on Hold - ver os tipos de execucao (pasta, mp3, etc) no controlador
- Cadastros diversos com senha - opcao para mostrar senha 
- Arquivos de som e Musicas de espera: Rever/Criar rotina para sincronizar disco com Banco de dados e vice-versa
---------------------------------
Release 1.0-Betha-rc1
- Ajustes diversos no SQL inicial e dados iniciais
- Ajustes para leitura do arquivo de áudio de acordo com language selecionada
- Adicionado controle para exibição ou não de regras desabilitadas (Parâmetros)
- Novas opções para NAT e DirectMedia em ramais/troncos SIP
- Removidas bibliotecas e arquivos não utilizados 
- Inseridos comentários de LIcenca GPL em todos os arquivos .PHP
- Renomeada pasta imagens para images 
- Ajustes nas Strings de tradução
---------------------------------
Release 1.0-alpha
- Nova interface
- Conectado com oa ITC  (www.intercomunexao.com.br)
- Novo sistema de controle de usuarios e permissões de acesso
- Novo sistema de visualização de logs
- Padronização do código fonte (jQuery, Zend, Bootstrap, etc)
- Preparado para multi-idiomas
- Novo sistema para atualização do CNL (Cadastro Nacional de Localidades / Anatel)
