**A FAZER : PRINCIPAL**

* Tradução e revisão das traduções Pt-BR, EN, ES
* Correção no comportamento do menu principal
- Refatorar rotina de Rotas / Regras Negócio
- Separar por ABAS: Entradas, Saidas, Outras
- Sistema de Ajuda on-line
- Manual do usuário
- Ajustes CSS
- Rever/redefinir conceitos/funcionalidades para Grupos de Ramais
- Rever/redefinir conceitos/funcionalidades para Centros de Custos
- Rotina das Notificações on-line (fonte/origem)

**A Fazer : Próxima versão**

- Contatos : usando tabela core-state ao invés de core-cnl-state. Rever duplicidade de informações
- Arquivos de Som do Asterisk - rever a rotina
- Opção de NAT - DirectMEdia - Ver conceitos de cada opção, Definir DEFAULT.
- Revisar o comportamento do asterisk (AGI's) quando usa / altera Language - tem locais que o CHANNEL(language) é fixado em pt-BR. Ver rotina lib/PBX/Rule.php
- Contatos : usando tabela core-state ao invés de core-cnl-state. Rever duplicidade de informações
- Arrumar os ícones para enable/disable das permissões do usuário
- Menu Usuário fica "sob" Status do sistema. Tem que ser "sobre"
- Regras de Negócio / Ações: Mostra 10 ações e esconde a partir disso - rever rolagem
- Status do sistema: Avisar que tem problemas de permissões, links etc
- Sala de Conferências: Criar opção para exibir ou não as mensagens/ nomes dos participantes
- Music on Hold - ver os tipos de execucao (pasta, mp3, etc) no controlador
- Cadastros diversos com senha - opcao para mostrar senha
- Arquivos de som e Musicas de espera: Rever/Criar rotina para sincronizar disco com Banco de dados e vice-versa
- Arquivos de som: Limpar a lista (fisica) de arquivos - muitos duplicados. (wav e gsm)
- Arquivos de som: Redefinir conceito  - sobrescreve arquivo ou não ??? mantem backup ou não ???
- Arquivo de som: Rever/Criar descricao dos arquivos no banco de dados
- Player de audio (relatorio chamadas, arquivos de som,etc) so toca wav. não toca gsm - REVER
- Error reporting nao mostrando corretamente tratamentos (Ex.: path_voz caso nao exista nao gera o erro na tela)
- Ramais cadastrados como IAX são exibidos como SIP ao alterar
FEITO - Salas de conferencias x Menu problemas na visualização
FEITO - Lista de ramais disponíveis no Grupo de Captura exibe referencias do Grupo de Ramal
---------------------------------
**Release 1.0-Betha-rc1**

- Ajustes diversos no SQL inicial e dados iniciais
- Ajustes para leitura do arquivo de áudio de acordo com language selecionada
- Adicionado controle para exibição ou não de regras desabilitadas (Parâmetros)
- Novas opções para NAT e DirectMedia em ramais/troncos SIP
- Removidas bibliotecas e arquivos não utilizados
- Inseridos comentários de LIcenca GPL em todos os arquivos .PHP
- Renomeada pasta imagens para images
- Ajustes nas Strings de tradução
- A opção NAT pode ser combinada (checkbox). Não é única (radiobox).
- Status do Sistema - Corrigido: não está exibindo o status dos troncos corretamente
- Parâmetros - Corrigido: Não altera a variável "country-code"
- Centro de Custos - Corrigido: descrição com 30 casas somente (aumentar) / Ver tabela BD
- Grupo de Contatos - Corrigido:   Trunca o nome (view x tabela BD)
- Musica de espera - Corrigido : Erro no Banco de dados, Erro ao gravar arquivo/criar sessão
- Relatorio de chamadas - Corrigido:  não está paginando, css para ouvir gravações/fazer download está desalinhado
- Relatório de serviços - NOVO: incluir cadeado, agenda, etc
- Regra de negócio padrão na instalação - Corrigido: Não cria ações para a Regra "Internas - Ramal a Ramal"
- Asterisk - Corrigido: quando parado não dá mensagem de erro, a tela fica em branco somente (Ex. Cadastro Ramais, troncos, etc)
- Ramais Cadastro - Corrigido: Usa grupo Ramal = Usuários mas exibe sempre como "Administradores"
- Estacionamento  - Ajustado: Não funciona como está documentado (700). Novo padrão: #72
- Tronco SNEPSip - Corrigido:  não funciona
- Cadeado - Corrigido : Ao incluir senha e marcar checkbox não funciona (ERRO de AGI). Ao alterar desmarcando o checkbox e deixando somente a senha, funciona normalmente
- Filas - Corrigido: Não funciona os anuncios ao chamador (tipo: Você é o proximo a ser atendido....")
---------------------------------
**Release 1.0-alpha**

- Nova interface
- Conectado com a ITC  (www.intercomunexao.com.br)
- Novo sistema de controle de usuarios e permissões de acesso
- Novo sistema de visualização de logs
- Padronização do código fonte (jQuery, Zend, Bootstrap, etc)
- Preparado para multi-idiomas
- Novo sistema para atualização do CNL (Cadastro Nacional de Localidades / Anatel)
