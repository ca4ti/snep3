/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as
 *  published by the Free Software Foundation, either version 3 of
 *  the License, or (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 */

INSERT INTO `expr_alias` VALUES (1,'Fixo Local');
INSERT INTO `expr_alias` VALUES (2,'Celular Local - VC1');
INSERT INTO `expr_alias` VALUES (3,'Fixo DDD');
INSERT INTO `expr_alias` VALUES (4,'Celular Interurbano - VC2/VC3');

INSERT INTO `expr_alias_expression` VALUES (1,'[2-5]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (2,'[6-9]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (3,'0|XX[2-5]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (4,'0|XX[6-9]XXXXXXX');

INSERT INTO groups VALUES ('all',null);
INSERT INTO groups VALUES ('admin','all');
INSERT INTO groups VALUES ('users','all');
INSERT INTO groups VALUES ('NULL',null);

INSERT INTO `contacts_group` VALUES (1, 'Default');

INSERT INTO `ccustos` VALUES ('1','E','ENTRADAS','Ligacoes de Entrada');
INSERT INTO `ccustos` VALUES ('2','S','SAIDAS','Ligacoes de Saida');
INSERT INTO `ccustos` VALUES ('5','O','FUNCIONALIDADES','Funcionalidades do Sistema');
INSERT INTO `ccustos` VALUES ('5.01','O','Conferencias','Ligacoes para Salas de de Conferencias');
INSERT INTO `ccustos` VALUES ('5.02','O','Logon de Agentes','Logon de Agentes na Fila (*01)');
INSERT INTO `ccustos` VALUES ('5.03','O','Logoff de Agentes','Logoff de Agentes na Fila (*02)');
INSERT INTO `ccustos` VALUES ('5.04','O','Pausa de Agentes - Inicio','Pausa de Agente na Fila (*03)');
INSERT INTO `ccustos` VALUES ('5.05','O','Pausa de Agente - Fim','Pausa de Agente na Fila - Fim (*04)');
INSERT INTO `ccustos` VALUES ('5.10','O','Emergencias','Ligacoes para telefones de Emergencia (190, 192, 191, etc)');
INSERT INTO `ccustos` VALUES ('9','O','Internas','Ligacoes Internas entre Ramais');

INSERT INTO `sounds` VALUES ('fpm-calm-river.wav','Som de Musica em Espera - Calm River','2008-07-25 10:51:42','MOH','default');
INSERT INTO `sounds` VALUES ('fpm-sunshine.wav','Som de Musica em Espera - Sunshine','2008-07-25 10:51:56','MOH','default');
INSERT INTO `sounds` VALUES ('fpm-world-mix.wav','Som de Musica em Espera - World Mix','2008-07-25 10:52:13','MOH','default');
INSERT INTO `sounds` VALUES ('Acre.wav','Acre','2008-08-11 14:14:35','AST','');
INSERT INTO `sounds` VALUES ('Alagoas.wav','Alagoas','2008-08-11 14:14:40','AST','');
INSERT INTO `sounds` VALUES ('Amapa.wav','Amapá','2008-08-11 14:14:45','AST','');
INSERT INTO `sounds` VALUES ('Amazonas.wav','Amazonas','2008-08-11 14:14:49','AST','');
INSERT INTO `sounds` VALUES ('Aracaju.wav','Aracaju','2008-08-11 14:14:54','AST','');
INSERT INTO `sounds` VALUES ('Bahia.wav','Bahia','2008-08-11 14:14:57','AST','');
INSERT INTO `sounds` VALUES ('Belem.wav','Belém','2008-08-11 14:15:01','AST','');
INSERT INTO `sounds` VALUES ('Belo-Horizonte.wav','Belo Horizonte','2008-08-11 14:15:22','AST','');
INSERT INTO `sounds` VALUES ('Boa-Vista.wav','Boa Vista','2008-08-11 14:15:31','AST','');
INSERT INTO `sounds` VALUES ('Brasilia.wav','Brasilia','2008-08-11 14:15:38','AST','');
INSERT INTO `sounds` VALUES ('Campo-Grande.wav','Campo Grande','2008-08-11 14:15:46','AST','');
INSERT INTO `sounds` VALUES ('Ceara.wav','Ceara','2008-08-11 14:15:50','AST','');
INSERT INTO `sounds` VALUES ('Cuiaba.wav','Cuiaba','2008-08-11 14:15:57','AST','');
INSERT INTO `sounds` VALUES ('Curitiba.wav','Curitiba','2008-08-11 14:16:01','AST','');
INSERT INTO `sounds` VALUES ('Distrito-Federal.wav','Distrito Federal','2008-08-11 14:16:14','AST','');
INSERT INTO `sounds` VALUES ('Espirito-Santo.wav','Espirito Santo','2008-08-11 14:16:25','AST','');
INSERT INTO `sounds` VALUES ('Florianopolis.wav','Florianopolis','2008-08-11 14:17:03','AST','');
INSERT INTO `sounds` VALUES ('Fortaleza.wav','Fortaleza','2008-08-11 14:17:10','AST','');
INSERT INTO `sounds` VALUES ('Goiania.wav','Goiania','2008-08-11 14:17:15','AST','');
INSERT INTO `sounds` VALUES ('Goias.wav','Goais','2008-08-11 14:17:19','AST','');
INSERT INTO `sounds` VALUES ('Joao-Pessoa.wav','Joao pessoa','2008-08-11 14:17:22','AST','');
INSERT INTO `sounds` VALUES ('Macapa.wav','Macapa','2008-08-11 14:26:06','AST','');
INSERT INTO `sounds` VALUES ('Maceio.wav','Maceio','2008-08-11 14:17:32','AST','');
INSERT INTO `sounds` VALUES ('Manaus.wav','Manaus','2008-08-11 14:17:35','AST','');
INSERT INTO `sounds` VALUES ('Maranhao.wav','Maranhão','2008-08-11 14:17:39','AST','');
INSERT INTO `sounds` VALUES ('Mato-Grosso-do-Sul.wav','Mato grosso do Sul','2008-08-11 14:17:44','AST','');
INSERT INTO `sounds` VALUES ('Mato-Grosso.wav','Mato Grosso','2008-08-11 14:17:51','AST','');
INSERT INTO `sounds` VALUES ('Minas-Gerais.wav','Minas Gerais','2008-08-11 14:17:55','AST','');
INSERT INTO `sounds` VALUES ('Natal.wav','Natal','2008-08-11 14:17:59','AST','');
INSERT INTO `sounds` VALUES ('Palmas.wav','Palmas','2008-08-11 14:18:02','AST','');
INSERT INTO `sounds` VALUES ('Para.wav','Para','2008-08-11 14:18:25','AST','');
INSERT INTO `sounds` VALUES ('Paraiba.wav','Paraiba','2008-08-11 14:18:33','AST','');
INSERT INTO `sounds` VALUES ('Parana.wav','Paraná','2008-08-11 14:18:47','AST','');
INSERT INTO `sounds` VALUES ('Pernambuco.wav','Pernambuco','2008-08-11 14:18:57','AST','');
INSERT INTO `sounds` VALUES ('Piaui.wav','Piaui','2008-08-11 14:19:01','AST','');
INSERT INTO `sounds` VALUES ('Porto-Alegre.wav','Porto Alegre','2008-08-11 14:19:09','AST','');
INSERT INTO `sounds` VALUES ('Porto-Velho.wav','Porto velho','2008-08-11 14:19:15','AST','');
INSERT INTO `sounds` VALUES ('Real.wav','Real','2008-08-11 14:19:21','AST','');
INSERT INTO `sounds` VALUES ('Recife.wav','Recife','2008-08-11 14:19:26','AST','');
INSERT INTO `sounds` VALUES ('Rio-Branco.wav','Rio Branco','2008-08-11 14:19:30','AST','');
INSERT INTO `sounds` VALUES ('Rio-Grande-do-Norte.wav','Rio Grande do Norte','2008-08-11 14:19:36','AST','');
INSERT INTO `sounds` VALUES ('Rio-Grande-do-Sul.wav','Rio Grande do Sul','2008-08-11 14:19:42','AST','');
INSERT INTO `sounds` VALUES ('Rio-de-Janeiro.wav','Rio de Janeiro','2008-08-11 14:19:46','AST','');
INSERT INTO `sounds` VALUES ('Rondonia.wav','Rondonia','2008-08-11 14:19:51','AST','');
INSERT INTO `sounds` VALUES ('Roraima.wav','Roraima','2008-08-11 14:19:55','AST','');
INSERT INTO `sounds` VALUES ('Salvador.wav','Salvador','2008-08-11 14:19:59','AST','');
INSERT INTO `sounds` VALUES ('Santa-Catarina.wav','Santa Catarina','2008-08-11 14:20:03','AST','');
INSERT INTO `sounds` VALUES ('Sao-Luis.wav','São Luiz','2008-08-11 14:20:10','AST','');
INSERT INTO `sounds` VALUES ('Sao-Paulo.wav','São Paulo','2008-08-11 14:20:13','AST','');
INSERT INTO `sounds` VALUES ('Sergipe.wav','Sergipe','2008-08-11 14:20:16','AST','');
INSERT INTO `sounds` VALUES ('Teresina.wav','Teresina','2008-08-11 14:20:20','AST','');
INSERT INTO `sounds` VALUES ('Tocantins.wav','Tocantins','2008-08-11 14:20:46','AST','');
INSERT INTO `sounds` VALUES ('Vitoria.wav','Vitória','2008-08-11 14:20:53','AST','');
INSERT INTO `sounds` VALUES ('access-password.wav','Digite a senha de acesso e pressione cerca','2008-08-11 14:21:30','AST','');
INSERT INTO `sounds` VALUES ('activated.wav','Ativado','2008-08-11 14:21:42','AST','');
INSERT INTO `sounds` VALUES ('afternoon.wav','Tarde','2008-08-11 14:21:53','AST','');
INSERT INTO `sounds` VALUES ('agent-alreadyon.wav','Atendentes apresente, digite seu número e pressione cerca','2008-08-11 14:22:29','AST','');
INSERT INTO `sounds` VALUES ('agent-incorrect.wav','Numero incorreto, digite seu numero e pressione cerca','2008-08-11 14:22:59','AST','');
INSERT INTO `sounds` VALUES ('agent-loggedoff.wav','Atendente ausente','2008-08-11 14:23:14','AST','');
INSERT INTO `sounds` VALUES ('agent-loginok.wav','Atendente presente','2008-08-11 14:23:29','AST','');
INSERT INTO `sounds` VALUES ('agent-newlocation.wav','Digite seu ramal e pressione cerca','2008-08-11 14:26:25','AST','');
INSERT INTO `sounds` VALUES ('agent-pass.wav','Digite sua senha e pressione cerca','2008-08-11 14:26:40','AST','');
INSERT INTO `sounds` VALUES ('agent-user.wav','Digite seu numero e pressione cerca','2008-08-11 14:26:51','AST','');
INSERT INTO `sounds` VALUES ('all-circuits-busy-now.wav','Aguarde, todas as linhas ocupadas no momento','2008-08-11 14:25:35','AST','');
INSERT INTO `sounds` VALUES ('an-error-has-occured.wav','Ocorreu um erro','2008-08-11 15:48:50','AST','');
INSERT INTO `sounds` VALUES ('astcc-accountnum.gsm','Digite o numero do seu carto seguido de #','2008-08-11 15:50:04','AST','');
INSERT INTO `sounds` VALUES ('astcc-badaccount.gsm','Cartão inválido','2008-08-11 15:50:50','AST','');
INSERT INTO `sounds` VALUES ('astcc-badphone.gsm','Número inválido','2008-08-11 15:51:02','AST','');
INSERT INTO `sounds` VALUES ('astcc-cents.gsm','Centavos','2008-08-11 15:51:17','AST','');
INSERT INTO `sounds` VALUES ('astcc-connectcharge.gsm','Uma caixa de conexão de','2008-08-11 15:51:39','AST','');
INSERT INTO `sounds` VALUES ('astcc-dollar.gsm','Real','2008-08-11 15:52:02','AST','');
INSERT INTO `sounds` VALUES ('astcc-dollars.gsm','Reais','2008-08-11 15:52:06','AST','');
INSERT INTO `sounds` VALUES ('astcc-down.gsm','Não está disponível no momento','2008-08-11 15:52:34','AST','');
INSERT INTO `sounds` VALUES ('astcc-forfirst.gsm','PAra os primeiros','2008-08-11 15:52:48','AST','');
INSERT INTO `sounds` VALUES ('astcc-isbusy.gsm','O número está ocupado no momento','2008-08-11 15:53:09','AST','');
INSERT INTO `sounds` VALUES ('astcc-minute.gsm','Minuto','2008-08-11 15:53:23','AST','');
INSERT INTO `sounds` VALUES ('astcc-minutes.gsm','Minutos','2008-08-11 15:53:26','AST','');
INSERT INTO `sounds` VALUES ('astcc-noanswer.gsm','O número chamado não atende','2008-08-11 15:53:46','AST','');
INSERT INTO `sounds` VALUES ('astcc-notenough.gsm','Sem créditos suficientes p/ efetuar a chamada','2008-08-11 15:54:16','AST','');
INSERT INTO `sounds` VALUES ('astcc-nothing.gsm','Nada','2008-08-11 15:54:26','AST','');
INSERT INTO `sounds` VALUES ('astcc-perminute.gsm','Centavos por minuto','2008-08-11 15:54:41','AST','');
INSERT INTO `sounds` VALUES ('astcc-phonenum.gsm','Disque o número a ser chamado seguido de #','2008-08-11 15:55:09','AST','');
INSERT INTO `sounds` VALUES ('astcc-pleasewait.gsm','Aguarde enquanto efetuamos sua chamada','2008-08-11 15:55:31','AST','');
INSERT INTO `sounds` VALUES ('astcc-point.gsm','Ponto','2008-08-11 15:55:47','AST','');
INSERT INTO `sounds` VALUES ('astcc-remaining.gsm','Está sobrando','2008-08-11 15:55:59','AST','');
INSERT INTO `sounds` VALUES ('astcc-secounds.gsm','Segundos','2008-08-11 15:56:22','AST','');
INSERT INTO `sounds` VALUES ('astcc-unavail.gsm','Número não disponível no momento','2008-08-11 15:57:12','AST','');
INSERT INTO `sounds` VALUES ('astcc-welcome.gsm','Bem-vindo','2008-08-11 15:57:23','AST','');
INSERT INTO `sounds` VALUES ('astcc-willapply.gsm','Será debitada','2008-08-11 15:57:39','AST','');
INSERT INTO `sounds` VALUES ('astcc-willcost.gsm','Chamada vai custar','2008-08-11 15:57:55','AST','');
INSERT INTO `sounds` VALUES ('astcc-youhave.gsm','Você tem','2008-08-11 15:58:08','AST','');
INSERT INTO `sounds` VALUES ('at-tone-time-exactly.wav','Quando houvir o tom a hora exata será','2008-08-11 15:58:36','AST','');
INSERT INTO `sounds` VALUES ('auth-incorrect.wav','Senha incorreta','2008-08-11 15:58:54','AST','');
INSERT INTO `sounds` VALUES ('auth-thankyou.wav','Obrigado','2008-08-11 15:59:11','AST','');
INSERT INTO `sounds` VALUES ('call-fwd-no-ans.wav','Redicionar ligação quando não atende','2008-08-11 15:59:43','AST','');
INSERT INTO `sounds` VALUES ('call-fwd-on-busy.wav','Redicionar ligação quando ocupado','2008-08-11 15:59:51','AST','');
INSERT INTO `sounds` VALUES ('call-fwd-unconditional.wav','Redicionar ligação sempre','2008-08-11 16:00:10','AST','');
INSERT INTO `sounds` VALUES ('conf-adminmenu.wav','Conferência - Tecle 1 p/ lig/des microfone ou 2 para bloq/desbl a Sala de Conf','2008-08-12 08:30:34','AST','');
INSERT INTO `sounds` VALUES ('conf-enteringno.wav','Conferência - Sala de Conferência número','2008-08-12 08:30:57','AST','');
INSERT INTO `sounds` VALUES ('conf-errormenu.wav','Conferência - Opção inválida','2008-08-12 08:31:11','AST','');
INSERT INTO `sounds` VALUES ('conf-getchannel.wav','Conferência - Digite o canal da Sala de conferência seguido de #','2008-08-12 08:31:47','AST','');
INSERT INTO `sounds` VALUES ('conf-getpin.wav','Conferência - Digite a senha da sala de conferência','2008-08-12 08:32:15','AST','');
INSERT INTO `sounds` VALUES ('conf-hasjoin.wav','Conferência - Entrou na sala de conferência','2008-08-12 08:32:28','AST','');
INSERT INTO `sounds` VALUES ('conf-hasleft.wav','Conferência - Saiu da Sala de Conferência','2008-08-12 08:32:42','AST','');
INSERT INTO `sounds` VALUES ('conf-invalid.wav','Conferência - Sala de Conferência inválida','2008-08-12 08:32:55','AST','');
INSERT INTO `sounds` VALUES ('conf-invalidpin.wav','Conferência - Senha da Sala de Conferência inválida','2008-08-12 08:33:06','AST','');
INSERT INTO `sounds` VALUES ('conf-kicked.wav','Conferência - Você foi excluido desta Sala de Conferência','2008-08-12 08:33:16','AST','');
INSERT INTO `sounds` VALUES ('conf-leaderhasleft.wav','Conferência - Líder saiu da sala de conferência','2008-08-12 08:33:29','AST','');
INSERT INTO `sounds` VALUES ('conf-locked.wav','Conferência - Sala de Conferência Bloqueada','2008-08-12 08:33:46','AST','');
INSERT INTO `sounds` VALUES ('conf-muted.wav','Conferência - Microfone desativado','2008-08-12 08:34:08','AST','');
INSERT INTO `sounds` VALUES ('conf-noempty.wav','Conferência - Todos Canais da Sala de Conferência estão ocupados','2008-08-12 08:34:46','AST','');
INSERT INTO `sounds` VALUES ('de-activated.wav','Desativado','2008-08-11 17:02:25','AST','');
INSERT INTO `sounds` VALUES ('queue-callswaiting.wav','Filas - Aguarde para falar com um atendente','2008-08-12 08:52:07','AST','');
INSERT INTO `sounds` VALUES ('queue-holdtime.wav','Filas - O tempo estimado de espera é de','2008-08-12 08:52:16','AST','');
INSERT INTO `sounds` VALUES ('queue-periodic-announce.wav','Filas - Atendentes ocupados, por favor aguarde ...','2008-08-12 08:52:26','AST','');
INSERT INTO `sounds` VALUES ('queue-thankyou.wav','Filas - Aguarde ser atendido','2008-08-12 08:52:36','AST','');
INSERT INTO `sounds` VALUES ('conf-getconfno.wav','Conferência - Digite o número da Sala de Conferência e pressione #','2008-08-12 08:32:01','AST','');
INSERT INTO `sounds` VALUES ('conf-lockednow.wav','Conferência - Sala de Conferência Bloqueada','2008-08-12 08:33:58','AST','');
INSERT INTO `sounds` VALUES ('conf-onlyone.wav','Conferência - Existe apenas 1 participante na Sala','2008-08-12 08:24:47','AST','');
INSERT INTO `sounds` VALUES ('conf-onlyperson.wav','Conferência - Você é a única pessoa nesta Sala de Conferência','2008-08-12 08:25:18','AST','');
INSERT INTO `sounds` VALUES ('conf-otherinparty.wav','Conferência - Outros participantes na Sala de Conferência','2008-08-12 08:25:49','AST','');
INSERT INTO `sounds` VALUES ('conf-placeINTOconf.wav','Conferência - Você entrará agora na Sala de Conferência','2008-08-12 08:26:23','AST','');
INSERT INTO `sounds` VALUES ('conf-thereare.wav','Conferência - Existem atualmente','2008-08-12 08:26:49','AST','');
INSERT INTO `sounds` VALUES ('conf-unlockednow.wav','Conferência - Sala de Conferência desbloqueada','2008-08-12 08:27:14','AST','');
INSERT INTO `sounds` VALUES ('conf-unmuted.wav','Conferência - Microfone ativado','2008-08-12 08:27:36','AST','');
INSERT INTO `sounds` VALUES ('conf-usermenu.wav','Conferência - Pressione 1 para Ligar ou Desligar o Microfone','2008-08-12 08:28:09','AST','');
INSERT INTO `sounds` VALUES ('conf-userswilljoin.wav','Conferência - Algumas pessoas entrarão na Sala de Conferência','2008-08-12 08:28:46','AST','');
INSERT INTO `sounds` VALUES ('conf-userwilljoin.wav','Conferência - Uma pessoa entrara na Sala de Conferência','2008-08-12 08:35:23','AST','');
INSERT INTO `sounds` VALUES ('conf-waitforleader.wav','Conferência - Sala de Conferência iniciará quando líder chegar','2008-08-12 08:35:55','AST','');
INSERT INTO `sounds` VALUES ('do-not-disturb.wav','Não perturbe','2008-08-12 08:36:51','AST','');
INSERT INTO `sounds` VALUES ('ent-target-attendant.wav','Entre com o número do','2008-08-12 08:37:19','AST','');
INSERT INTO `sounds` VALUES ('ext-disabled.wav','Ramal não habilitado para receber chamadas','2008-08-12 08:37:38','AST','');
INSERT INTO `sounds` VALUES ('hour.wav','Hora','2008-08-12 08:38:11','AST','');
INSERT INTO `sounds` VALUES ('im-sorry.wav','Desculpe','2008-08-12 08:38:20','AST','');
INSERT INTO `sounds` VALUES ('info-about-last-call.wav','Informação sobre a última chamada','2008-08-12 08:38:43','AST','');
INSERT INTO `sounds` VALUES ('incorrect-password.wav','Senha incorreta','2008-08-12 08:39:00','AST','');
INSERT INTO `sounds` VALUES ('invalid.wav','Número inválido, tente novamente','2008-08-12 08:39:25','AST','');
INSERT INTO `sounds` VALUES ('is-in-use.wav','Está em uso','2008-08-12 08:39:42','AST','');
INSERT INTO `sounds` VALUES ('location.wav','Posição','2008-08-12 08:40:05','AST','');
INSERT INTO `sounds` VALUES ('is.wav','É','2008-08-12 08:40:09','AST','');
INSERT INTO `sounds` VALUES ('minute.wav','Minuto','2008-08-12 08:40:22','AST','');
INSERT INTO `sounds` VALUES ('is-set-to.wav','Está marcado como','2008-08-12 08:40:43','AST','');
INSERT INTO `sounds` VALUES ('morning.wav','Manhã','2008-08-12 08:41:04','AST','');
INSERT INTO `sounds` VALUES ('night.wav','Noite','2008-08-12 08:41:11','AST','');
INSERT INTO `sounds` VALUES ('no-rights.wav','Você não tem direito de acesso à rota sainte','2008-08-12 08:41:33','AST','');
INSERT INTO `sounds` VALUES ('number.wav','Número','2008-08-12 08:41:44','AST','');
INSERT INTO `sounds` VALUES ('one-moment-please.wav','Um momento , por favor','2008-08-12 08:42:03','AST','');
INSERT INTO `sounds` VALUES ('pbx-invalid.wav','Ramal inválido, por favor tente novamente','2008-08-12 08:42:30','AST','');
INSERT INTO `sounds` VALUES ('pbx-transfer.wav','Transferência','2008-08-12 08:42:46','AST','');
INSERT INTO `sounds` VALUES ('pbx-invalidpark.wav','Não existe chamada estacionada neste ramal','2008-08-12 08:43:11','AST','');
INSERT INTO `sounds` VALUES ('pls-try-call-later.wav','Tente mais tarde','2008-08-12 08:43:38','AST','');
INSERT INTO `sounds` VALUES ('pm-invalid-option.wav','Você escolheu uma opção inválida','2008-08-12 08:43:58','AST','');
INSERT INTO `sounds` VALUES ('press-1.wav','Pressione 1','2008-08-12 08:44:16','AST','');
INSERT INTO `sounds` VALUES ('press-2.wav','Pressione 2','2008-08-12 08:44:24','AST','');
INSERT INTO `sounds` VALUES ('press-3.wav','Pressione 3','2008-08-12 08:44:30','AST','');
INSERT INTO `sounds` VALUES ('press-star.wav','Pressione estrela','2008-08-12 08:44:44','AST','');
INSERT INTO `sounds` VALUES ('queue-less-than.wav','Filas - Menos que','2008-08-12 08:45:03','AST','');
INSERT INTO `sounds` VALUES ('queue-minutes.wav','Filas - Minutos','2008-08-12 08:45:23','AST','');
INSERT INTO `sounds` VALUES ('queue-reporthold.wav','Filas - Tempo de espera','2008-08-12 08:45:42','AST','');
INSERT INTO `sounds` VALUES ('queue-seconds.wav','Filas - Segundos','2008-08-12 08:45:50','AST','');
INSERT INTO `sounds` VALUES ('queue-thereare.wav','Filas - Sua chamada é a','2008-08-12 08:48:21','AST','');
INSERT INTO `sounds` VALUES ('queue-youarenext.wav','Filas - Sua chamada é a primeira da fila','2008-08-12 08:48:44','AST','');

INSERT INTO profiles (name, created, updated) VALUES ('default',now(),now());
INSERT INTO users (name, password,email,profile_id, created, updated) VALUES ('admin','0192023a7bbd73250516f069df18b500','suporte@opens.com.br',1,now(),now());

INSERT INTO `grupos` (`cod_grupo`, `nome`) VALUES
(1, 'GERAL');
