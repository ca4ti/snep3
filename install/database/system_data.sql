/*
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

/**
 * Default data  
 *
 * @category  Snep
 * @package   Snep
 * @copyright Copyright (c) 2014 OpenS Tecnologia
 * @author    Opens Tecnologia <desenvolvimento@opens.com.br>
 */

--
-- Default expression alias
--
INSERT INTO `expr_alias` VALUES (1,'Fixo Local');
INSERT INTO `expr_alias` VALUES (2,'Celular Local - VC1');
INSERT INTO `expr_alias` VALUES (3,'Fixo DDD');
INSERT INTO `expr_alias` VALUES (4,'Celular Interurbano - VC2/VC3');

--
-- Default values expression alias
--
INSERT INTO `expr_alias_expression` VALUES (1,'[2-5]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (2,'[6-9]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (3,'0|XX[2-5]XXXXXXX');
INSERT INTO `expr_alias_expression` VALUES (4,'0|XX[6-9]XXXXXXX');

--
-- Default group extension
--
INSERT INTO groups VALUES ('all',null);
INSERT INTO groups VALUES ('admin','all');
INSERT INTO groups VALUES ('users','all');
INSERT INTO groups VALUES ('NULL',null);

--
-- Default contacts group
--
INSERT INTO `contacts_group` VALUES (1, 'Default');

--
-- Default cost center
--
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

--
-- Default sounds
--
INSERT INTO `sounds` VALUES ('access-password.wav','Digite a senha de acesso e pressione cerca','2008-08-11 14:21:30','AST','','pt_BR'),('Acre.wav','Acre','2008-08-11 14:14:35','AST','','pt_BR'),('activated.wav','Ativado','2008-08-11 14:21:42','AST','','pt_BR'),('afternoon.wav','Tarde','2008-08-11 14:21:53','AST','','pt_BR'),('agent-alreadyon.wav','Atendentes apresente, digite seu número e pressione cerca','2008-08-11 14:22:29','AST','','pt_BR'),('agent-incorrect.wav','Numero incorreto, digite seu numero e pressione cerca','2008-08-11 14:22:59','AST','','pt_BR'),('agent-loggedoff.wav','Atendente ausente','2008-08-11 14:23:14','AST','','pt_BR'),('agent-loginok.wav','Atendente presente','2008-08-11 14:23:29','AST','','pt_BR'),('agent-newlocation.wav','Digite seu ramal e pressione cerca','2008-08-11 14:26:25','AST','','pt_BR'),('agent-pass.wav','Digite sua senha e pressione cerca','2008-08-11 14:26:40','AST','','pt_BR'),('agent-user.wav','Digite seu numero e pressione cerca','2008-08-11 14:26:51','AST','','pt_BR'),('Alagoas.wav','Alagoas','2008-08-11 14:14:40','AST','','pt_BR'),('all-circuits-busy-now.wav','Aguarde, todas as linhas ocupadas no momento','2008-08-11 14:25:35','AST','','pt_BR'),('Amapa.wav','Amapá','2008-08-11 14:14:45','AST','','pt_BR'),('Amazonas.wav','Amazonas','2008-08-11 14:14:49','AST','','pt_BR'),('an-error-has-occured.wav','Ocorreu um erro','2008-08-11 15:48:50','AST','','pt_BR'),('Aracaju.wav','Aracaju','2008-08-11 14:14:54','AST','','pt_BR'),('astcc-accountnum.gsm','Digite o numero do seu carto seguido de #','2008-08-11 15:50:04','AST','','pt_BR'),('astcc-badaccount.gsm','Cartão inválido','2008-08-11 15:50:50','AST','','pt_BR'),('astcc-badphone.gsm','Número inválido','2008-08-11 15:51:02','AST','','pt_BR'),('astcc-cents.gsm','Centavos','2008-08-11 15:51:17','AST','','pt_BR'),('astcc-connectcharge.gsm','Uma caixa de conexão de','2008-08-11 15:51:39','AST','','pt_BR'),('astcc-dollar.gsm','Real','2008-08-11 15:52:02','AST','','pt_BR'),('astcc-dollars.gsm','Reais','2008-08-11 15:52:06','AST','','pt_BR'),('astcc-down.gsm','Não está disponível no momento','2008-08-11 15:52:34','AST','','pt_BR'),('astcc-forfirst.gsm','PAra os primeiros','2008-08-11 15:52:48','AST','','pt_BR'),('astcc-isbusy.gsm','O número está ocupado no momento','2008-08-11 15:53:09','AST','','pt_BR'),('astcc-minute.gsm','Minuto','2008-08-11 15:53:23','AST','','pt_BR'),('astcc-minutes.gsm','Minutos','2008-08-11 15:53:26','AST','','pt_BR'),('astcc-noanswer.gsm','O número chamado não atende','2008-08-11 15:53:46','AST','','pt_BR'),('astcc-notenough.gsm','Sem créditos suficientes p/ efetuar a chamada','2008-08-11 15:54:16','AST','','pt_BR'),('astcc-nothing.gsm','Nada','2008-08-11 15:54:26','AST','','pt_BR'),('astcc-perminute.gsm','Centavos por minuto','2008-08-11 15:54:41','AST','','pt_BR'),('astcc-phonenum.gsm','Disque o número a ser chamado seguido de #','2008-08-11 15:55:09','AST','','pt_BR'),('astcc-pleasewait.gsm','Aguarde enquanto efetuamos sua chamada','2008-08-11 15:55:31','AST','','pt_BR'),('astcc-point.gsm','Ponto','2008-08-11 15:55:47','AST','','pt_BR'),('astcc-remaining.gsm','Está sobrando','2008-08-11 15:55:59','AST','','pt_BR'),('astcc-secounds.gsm','Segundos','2008-08-11 15:56:22','AST','','pt_BR'),('astcc-unavail.gsm','Número não disponível no momento','2008-08-11 15:57:12','AST','','pt_BR'),('astcc-welcome.gsm','Bem-vindo','2008-08-11 15:57:23','AST','','pt_BR'),('astcc-willapply.gsm','Será debitada','2008-08-11 15:57:39','AST','','pt_BR'),('astcc-willcost.gsm','Chamada vai custar','2008-08-11 15:57:55','AST','','pt_BR'),('astcc-youhave.gsm','Você tem','2008-08-11 15:58:08','AST','','pt_BR'),('at-tone-time-exactly.wav','Quando houvir o tom a hora exata será','2008-08-11 15:58:36','AST','','pt_BR'),('auth-incorrect.wav','Senha incorreta','2008-08-11 15:58:54','AST','','pt_BR'),('auth-thankyou.wav','Obrigado','2008-08-11 15:59:11','AST','','pt_BR'),('Bahia.wav','Bahia','2008-08-11 14:14:57','AST','','pt_BR'),('Belem.wav','Belém','2008-08-11 14:15:01','AST','','pt_BR'),('Belo-Horizonte.wav','Belo Horizonte','2008-08-11 14:15:22','AST','','pt_BR'),('Boa-Vista.wav','Boa Vista','2008-08-11 14:15:31','AST','','pt_BR'),('Brasilia.wav','Brasilia','2008-08-11 14:15:38','AST','','pt_BR'),('call-fwd-no-ans.wav','Redicionar ligação quando não atende','2008-08-11 15:59:43','AST','','pt_BR'),('call-fwd-on-busy.wav','Redicionar ligação quando ocupado','2008-08-11 15:59:51','AST','','pt_BR'),('call-fwd-unconditional.wav','Redicionar ligação sempre','2008-08-11 16:00:10','AST','','pt_BR'),('Campo-Grande.wav','Campo Grande','2008-08-11 14:15:46','AST','','pt_BR'),('Ceara.wav','Ceara','2008-08-11 14:15:50','AST','','pt_BR'),('CHANGES-asterisk-moh-opsound-wav','CHANGES-asterisk-moh-opsound-wav','2015-03-05 10:06:11','MOH','default','pt_BR'),('conf-adminmenu.wav','Conferência - Tecle 1 p/ lig/des microfone ou 2 para bloq/desbl a Sala de Conf','2008-08-12 08:30:34','AST','','pt_BR'),('conf-enteringno.wav','Conferência - Sala de Conferência número','2008-08-12 08:30:57','AST','','pt_BR'),('conf-errormenu.wav','Conferência - Opção inválida','2008-08-12 08:31:11','AST','','pt_BR'),('conf-getchannel.wav','Conferência - Digite o canal da Sala de conferência seguido de #','2008-08-12 08:31:47','AST','','pt_BR'),('conf-getconfno.wav','Conferência - Digite o número da Sala de Conferência e pressione #','2008-08-12 08:32:01','AST','','pt_BR'),('conf-getpin.wav','Conferência - Digite a senha da sala de conferência','2008-08-12 08:32:15','AST','','pt_BR'),('conf-hasjoin.wav','Conferência - Entrou na sala de conferência','2008-08-12 08:32:28','AST','','pt_BR'),('conf-hasleft.wav','Conferência - Saiu da Sala de Conferência','2008-08-12 08:32:42','AST','','pt_BR'),('conf-invalid.wav','Conferência - Sala de Conferência inválida','2008-08-12 08:32:55','AST','','pt_BR'),('conf-invalidpin.wav','Conferência - Senha da Sala de Conferência inválida','2008-08-12 08:33:06','AST','','pt_BR'),('conf-kicked.wav','Conferência - Você foi excluido desta Sala de Conferência','2008-08-12 08:33:16','AST','','pt_BR'),('conf-leaderhasleft.wav','Conferência - Líder saiu da sala de conferência','2008-08-12 08:33:29','AST','','pt_BR'),('conf-locked.wav','Conferência - Sala de Conferência Bloqueada','2008-08-12 08:33:46','AST','','pt_BR'),('conf-lockednow.wav','Conferência - Sala de Conferência Bloqueada','2008-08-12 08:33:58','AST','','pt_BR'),('conf-muted.wav','Conferência - Microfone desativado','2008-08-12 08:34:08','AST','','pt_BR'),('conf-noempty.wav','Conferência - Todos Canais da Sala de Conferência estão ocupados','2008-08-12 08:34:46','AST','','pt_BR'),('conf-onlyone.wav','Conferência - Existe apenas 1 participante na Sala','2008-08-12 08:24:47','AST','','pt_BR'),('conf-onlyperson.wav','Conferência - Você é a única pessoa nesta Sala de Conferência','2008-08-12 08:25:18','AST','','pt_BR'),('conf-otherinparty.wav','Conferência - Outros participantes na Sala de Conferência','2008-08-12 08:25:49','AST','','pt_BR'),('conf-placeINTOconf.wav','Conferência - Você entrará agora na Sala de Conferência','2008-08-12 08:26:23','AST','','pt_BR'),('conf-thereare.wav','Conferência - Existem atualmente','2008-08-12 08:26:49','AST','','pt_BR'),('conf-unlockednow.wav','Conferência - Sala de Conferência desbloqueada','2008-08-12 08:27:14','AST','','pt_BR'),('conf-unmuted.wav','Conferência - Microfone ativado','2008-08-12 08:27:36','AST','','pt_BR'),('conf-usermenu.wav','Conferência - Pressione 1 para Ligar ou Desligar o Microfone','2008-08-12 08:28:09','AST','','pt_BR'),('conf-userswilljoin.wav','Conferência - Algumas pessoas entrarão na Sala de Conferência','2008-08-12 08:28:46','AST','','pt_BR'),('conf-userwilljoin.wav','Conferência - Uma pessoa entrara na Sala de Conferência','2008-08-12 08:35:23','AST','','pt_BR'),('conf-waitforleader.wav','Conferência - Sala de Conferência iniciará quando líder chegar','2008-08-12 08:35:55','AST','','pt_BR'),('CREDITS-asterisk-moh-opsound-wav','CREDITS-asterisk-moh-opsound-wav','2015-03-05 10:06:11','MOH','default','pt_BR'),('Cuiaba.wav','Cuiaba','2008-08-11 14:15:57','AST','','pt_BR'),('Curitiba.wav','Curitiba','2008-08-11 14:16:01','AST','','pt_BR'),('de-activated.wav','Desativado','2008-08-11 17:02:25','AST','','pt_BR'),('Distrito-Federal.wav','Distrito Federal','2008-08-11 14:16:14','AST','','pt_BR'),('do-not-disturb.wav','Não perturbe','2008-08-12 08:36:51','AST','','pt_BR'),('ent-target-attendant.wav','Entre com o número do','2008-08-12 08:37:19','AST','','pt_BR'),('Espirito-Santo.wav','Espirito Santo','2008-08-11 14:16:25','AST','','pt_BR'),('ext-disabled.wav','Ramal não habilitado para receber chamadas','2008-08-12 08:37:38','AST','','pt_BR'),('Florianopolis.wav','Florianopolis','2008-08-11 14:17:03','AST','','pt_BR'),('Fortaleza.wav','Fortaleza','2008-08-11 14:17:10','AST','','pt_BR'),('fpm-calm-river.wav','Som de Musica em Espera - Calm River','2008-07-25 10:51:42','MOH','default','pt_BR'),('fpm-sunshine.wav','Som de Musica em Espera - Sunshine','2008-07-25 10:51:56','MOH','default','pt_BR'),('fpm-world-mix.wav','Som de Musica em Espera - World Mix','2008-07-25 10:52:13','MOH','default','pt_BR'),('Goiania.wav','Goiania','2008-08-11 14:17:15','AST','','pt_BR'),('Goias.wav','Goais','2008-08-11 14:17:19','AST','','pt_BR'),('hour.wav','Hora','2008-08-12 08:38:11','AST','','pt_BR'),('im-sorry.wav','Desculpe','2008-08-12 08:38:20','AST','','pt_BR'),('incorrect-password.wav','Senha incorreta','2008-08-12 08:39:00','AST','','pt_BR'),('info-about-last-call.wav','Informação sobre a última chamada','2008-08-12 08:38:43','AST','','pt_BR'),('invalid.wav','Número inválido, tente novamente','2008-08-12 08:39:25','AST','','pt_BR'),('is-in-use.wav','Está em uso','2008-08-12 08:39:42','AST','','pt_BR'),('is-set-to.wav','Está marcado como','2008-08-12 08:40:43','AST','','pt_BR'),('is.wav','É','2008-08-12 08:40:09','AST','','pt_BR'),('Joao-Pessoa.wav','Joao pessoa','2008-08-11 14:17:22','AST','','pt_BR'),('LICENSE-asterisk-moh-freeplay-wav','LICENSE-asterisk-moh-freeplay-wav','2015-03-05 10:06:11','MOH','default','pt_BR'),('LICENSE-asterisk-moh-opsound-wav','LICENSE-asterisk-moh-opsound-wav','2015-03-05 10:06:11','MOH','default','pt_BR'),('location.wav','Posição','2008-08-12 08:40:05','AST','','pt_BR'),('Macapa.wav','Macapa','2008-08-11 14:26:06','AST','','pt_BR'),('Maceio.wav','Maceio','2008-08-11 14:17:32','AST','','pt_BR'),('Manaus.wav','Manaus','2008-08-11 14:17:35','AST','','pt_BR'),('Maranhao.wav','Maranhão','2008-08-11 14:17:39','AST','','pt_BR'),('Mato-Grosso-do-Sul.wav','Mato grosso do Sul','2008-08-11 14:17:44','AST','','pt_BR'),('Mato-Grosso.wav','Mato Grosso','2008-08-11 14:17:51','AST','','pt_BR'),('Minas-Gerais.wav','Minas Gerais','2008-08-11 14:17:55','AST','','pt_BR'),('minute.wav','Minuto','2008-08-12 08:40:22','AST','','pt_BR'),('morning.wav','Manhã','2008-08-12 08:41:04','AST','','pt_BR'),('Natal.wav','Natal','2008-08-11 14:17:59','AST','','pt_BR'),('night.wav','Noite','2008-08-12 08:41:11','AST','','pt_BR'),('no-rights.wav','Você não tem direito de acesso à rota sainte','2008-08-12 08:41:33','AST','','pt_BR'),('number.wav','Número','2008-08-12 08:41:44','AST','','pt_BR'),('one-moment-please.wav','Um momento , por favor','2008-08-12 08:42:03','AST','','pt_BR'),('Palmas.wav','Palmas','2008-08-11 14:18:02','AST','','pt_BR'),('Para.wav','Para','2008-08-11 14:18:25','AST','','pt_BR'),('Paraiba.wav','Paraiba','2008-08-11 14:18:33','AST','','pt_BR'),('Parana.wav','Paraná','2008-08-11 14:18:47','AST','','pt_BR'),('pbx-invalid.wav','Ramal inválido, por favor tente novamente','2008-08-12 08:42:30','AST','','pt_BR'),('pbx-invalidpark.wav','Não existe chamada estacionada neste ramal','2008-08-12 08:43:11','AST','','pt_BR'),('pbx-transfer.wav','Transferência','2008-08-12 08:42:46','AST','','pt_BR'),('Pernambuco.wav','Pernambuco','2008-08-11 14:18:57','AST','','pt_BR'),('Piaui.wav','Piaui','2008-08-11 14:19:01','AST','','pt_BR'),('pls-try-call-later.wav','Tente mais tarde','2008-08-12 08:43:38','AST','','pt_BR'),('pm-invalid-option.wav','Você escolheu uma opção inválida','2008-08-12 08:43:58','AST','','pt_BR'),('Porto-Alegre.wav','Porto Alegre','2008-08-11 14:19:09','AST','','pt_BR'),('Porto-Velho.wav','Porto velho','2008-08-11 14:19:15','AST','','pt_BR'),('press-1.wav','Pressione 1','2008-08-12 08:44:16','AST','','pt_BR'),('press-2.wav','Pressione 2','2008-08-12 08:44:24','AST','','pt_BR'),('press-3.wav','Pressione 3','2008-08-12 08:44:30','AST','','pt_BR'),('press-star.wav','Pressione estrela','2008-08-12 08:44:44','AST','','pt_BR'),('queue-callswaiting.wav','Filas - Aguarde para falar com um atendente','2008-08-12 08:52:07','AST','','pt_BR'),('queue-holdtime.wav','Filas - O tempo estimado de espera é de','2008-08-12 08:52:16','AST','','pt_BR'),('queue-less-than.wav','Filas - Menos que','2008-08-12 08:45:03','AST','','pt_BR'),('queue-minutes.wav','Filas - Minutos','2008-08-12 08:45:23','AST','','pt_BR'),('queue-periodic-announce.wav','Filas - Atendentes ocupados, por favor aguarde ...','2008-08-12 08:52:26','AST','','pt_BR'),('queue-reporthold.wav','Filas - Tempo de espera','2008-08-12 08:45:42','AST','','pt_BR'),('queue-seconds.wav','Filas - Segundos','2008-08-12 08:45:50','AST','','pt_BR'),('queue-thankyou.wav','Filas - Aguarde ser atendido','2008-08-12 08:52:36','AST','','pt_BR'),('queue-thereare.wav','Filas - Sua chamada é a','2008-08-12 08:48:21','AST','','pt_BR'),('queue-youarenext.wav','Filas - Sua chamada é a primeira da fila','2008-08-12 08:48:44','AST','','pt_BR'),('Real.wav','Real','2008-08-11 14:19:21','AST','','pt_BR'),('Recife.wav','Recife','2008-08-11 14:19:26','AST','','pt_BR'),('Rio-Branco.wav','Rio Branco','2008-08-11 14:19:30','AST','','pt_BR'),('Rio-de-Janeiro.wav','Rio de Janeiro','2008-08-11 14:19:46','AST','','pt_BR'),('Rio-Grande-do-Norte.wav','Rio Grande do Norte','2008-08-11 14:19:36','AST','','pt_BR'),('Rio-Grande-do-Sul.wav','Rio Grande do Sul','2008-08-11 14:19:42','AST','','pt_BR'),('Rondonia.wav','Rondonia','2008-08-11 14:19:51','AST','','pt_BR'),('Roraima.wav','Roraima','2008-08-11 14:19:55','AST','','pt_BR'),('Salvador.wav','Salvador','2008-08-11 14:19:59','AST','','pt_BR'),('Santa-Catarina.wav','Santa Catarina','2008-08-11 14:20:03','AST','','pt_BR'),('Sao-Luis.wav','São Luiz','2008-08-11 14:20:10','AST','','pt_BR'),('Sao-Paulo.wav','São Paulo','2008-08-11 14:20:13','AST','','pt_BR'),('Sergipe.wav','Sergipe','2008-08-11 14:20:16','AST','','pt_BR'),('Teresina.wav','Teresina','2008-08-11 14:20:20','AST','','pt_BR'),('Tocantins.wav','Tocantins','2008-08-11 14:20:46','AST','','pt_BR'),('Vitoria.wav','Vitória','2008-08-11 14:20:53','AST','','pt_BR');

--
-- Default user admin
--
INSERT INTO profiles (name, created, updated) VALUES ('default',now(),now());
INSERT INTO users (name, password,email,profile_id, created, updated) VALUES ('admin','0192023a7bbd73250516f069df18b500','suporte@opens.com.br',1,now(),now());

--
-- Default pickup group
--
INSERT INTO `grupos` (`cod_grupo`, `nome`) VALUES
(1, 'GERAL');

--
-- Default queue group
--
INSERT INTO `group_queues` (name) VALUES ('Default');

--
-- Default rule 
--
INSERT INTO `regras_negocio` VALUES ('',0,'Internas - Ramal para Ramal','G:all','G:all','00:00:00-23:59:59','sun,mon,tue,wed,thu,fri,sat',0,1,0);
INSERT INTO `regras_negocio_actions` VALUES (1,0,'PBX_Rule_Action_CCustos'),(1,1,'PBX_Rule_Action_DiscarRamal');
INSERT INTO `regras_negocio_actions_config` VALUES (1,0,'ccustos','9'),(1,1,'allow_voicemail','false'),(1,1,'dial_flags','twk'),(1,1,'dial_timeout','60'),(1,1,'diff_ring','false'),(1,1,'dont_overflow','false'),(1,1,'resolv_agent','false');


