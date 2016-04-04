**A FAZER : PRINCIPAL**

- Tradução e revisão das traduções Pt-BR, EN, ES
- Correção no comportamento do menu principal
- Refatorar rotina de Rotas / Regras Negócio
- Regras de NEgócio: Separar por ABAS: Entradas, Saidas, Outras
- Sistema de Ajuda on-line
- Manual do usuário
- Ajustes CSS
- Calendario de feriados
- Rotina das Notificações on-line (fonte/origem)
- Rever/redefinir conceitos/funcionalidades para Centros de Custos
- Rever/redefinir conceitos/funcionalidades para Audios
- Rever/redefinir conceitos/funcionalidades para Language
- Rever/redefinir conceitos/funcionalidades para Segurança
- Rever/Redefinir conceitos/funcionalidades agents / Grupos de agentes
- Rever/Redefinir conceitos/funcionalidades Grupos de Captura
- Arquivos de Som do Asterisk - rever a rotina
- Opção de NAT - DirectMEdia - Ver conceitos de cada opção, Definir DEFAULT.
- Status do sistema: Avisar que tem problemas de permissões, links etc
- Sala de Conferências: Criar opção para exibir ou não as mensagens/ nomes dos participantes
- Music on Hold - ver os tipos de execucao (pasta, mp3, etc) no controlador
- Arquivos de som: Redefinir conceito  - sobrescreve arquivo ou não ??? mantem backup ou não ???
- Error reporting nao mostrando corretamente tratamentos (Ex.: path_voz caso nao exista nao gera o erro na tela)
- os perfis de acesso devem permitir vinculos
- Status dos Sistemas Ramais : Rever comportamento
- Rever/redefinir conceitos/funcionalidades para Grupos de Ramais
  - discar para um grupo de ramais
  - Uso em espionar e sussurrar
  - Grupos para destino (ringall)   
  - Grupos de Captura : Ajustar a view para exibir numero de ramais no grupo (mesmo modelo do Grupo de ramais)
  - Asterisk cli> suprimir mensagens de erro: ast_carefulwrite: write() returned error: Broken pipe

---------------------------------
**Release - betha-rc3 **

FEITO - % de uso da CPU nao esta sendo exibido
FEITO - Menu Usuário fica "sob" Status do sistema. Tem que ser "sobre"
FEITO - Criada view para o menu de Informações
FEITO - Arrumar os ícones para enable/disable das permissões do usuário
FEITO - Corrigido problema que exibia mensagem de erro abaixo da view em execução
FEITO - Rever/redefinir conceitos/funcionalidades para Grupos de Ramais : + de 1 grupo por ramal
FEITO - Corrigido Erro na apresentacao do espaço usado em disco para HD's com mais de 1T
FEITO - Corrigido erros na exibicao de status e latencia de troncos em Status >> Status IP
FEITO - Cadastro usuário portal ITC agora permite qualquer caracter para a senha - comaptibilizar com portal ITC
FEITO - Status do Sistema -  Refatorada toda a rotina usando interações com AMI/Actions do Asterisk
FEITO - Removido do Dashboard : Atalho para "buscar gravacoes" (record-report) 
FEITO - Ajustes em strings de tradução
FEITO - Corrigido ERRO no Cadastro troncos que não obrigava a definir o tipo de tronco (peer,user,friend)
FEITO - Ajustes para remover diversos Warning e Notices do console do apache

---------------------------------
<<<<<<< HEAD
**Release Betha-rc2**
=======
**Release 1.0-Betha-rc3**

- Corrigido: - % de uso da CPU nao esta sendo exibido
- Corrigido: Menu Usuário fica "sob" Status do sistema. Tem que ser "sobre"
- Criada view para o menu de Informações
- Corrigido os ícones para enable/disable das permissões do usuário
- Corrigido problema que exibia mensagem de erro abaixo da view em execução
- Redefinido Grupos de Ramais para permitir + de 1 grupo por ramal
- Corrigido Erro na apresentacao do espaço usado em disco para HD's com mais de 1T
- Corrigido erros na exibicao de status e latencia de troncos em Status >> Status IP
- Cadastro usuário portal ITC agora permite qualquer caracter para a senha - compatibilizar com portal ITC
- Status do Sistema -  Refatorada toda a rotina usando interações com AMI/Actions do Asterisk
- Removido do Dashboard : Atalho para "buscar gravacoes" (record-report)
- Ajustes em strings de tradução    
- Corrigido ERRO no Cadastro troncos que não obrigava a definir o tipo de tronco (peer,user,friend)
- Ajustes para remover diversos Warning e Notices do console do apache


---------------------------------
**Release 1.0-Betha-rc2**
>>>>>>> 4cf2c75cf71d7b613a3ea80a1425a4d8d7bde5bc

- Contatos: Alterada base de leitura de cidades e estados, retirada a obrigatoriedade de cidade e estado do cadastro.
- Corrigido: Ramais cadastrados como IAX sendo exibidos como SIP ao alterar
- Corrigido: Salas de conferencias x Menu - problemas na visualização
- Corrigido: Lista de ramais disponíveis no Grupo de Captura exibe referencias do Grupo de Ramal
- Revisado/corrigido: comportamento do asterisk (AGI's) quando usa/altera Language (exige ajustes em extensions.conf e snep-features.conf)
- Corrigido: Erro quando usa/habilita cadeado no ramal
- Arquivos de som: Limpar a lista (fisica) de arquivos - muitos duplicados. (wav e gsm)
- Corrigido: Arquivo de som - descricao dos arquivos no banco de dados - Sincronizado - descricões zeradas
- Nova funcionalidade: Cadastros diversos com senha - opcao para mostrar senha
- Ajustes na exibição do status do sistema / controle do temporizador para atualização do status

---------------------------------
**Release Betha-rc1**

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
**Release Alpha**

- Nova interface
- Conectado com a ITC  (www.intercomunexao.com.br)
- Novo sistema de controle de usuarios e permissões de acesso
- Novo sistema de visualização de logs
- Padronização do código fonte (jQuery, Zend, Bootstrap, etc)
- Preparado para multi-idiomas
- Novo sistema para atualização do CNL (Cadastro Nacional de Localidades / Anatel)
