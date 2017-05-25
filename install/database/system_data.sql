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
INSERT INTO `expr_alias` VALUES (2,'Celular Local');
INSERT INTO `expr_alias` VALUES (3,'Fixo LDN');
INSERT INTO `expr_alias` VALUES (4,'Celular LDN');

--
-- Default values expression alias
--
INSERT INTO `expr_alias_expression` VALUES (1,'[2-5]XXXXXXX'),(2,'[6-9]XXXXXXX'),(2,'9[6-9]XXXXXXX'),(3,'0|XX[2-5]XXXXXXX'),(3,'0XX|XX[2-5]XXXXXXX'),(4,'0|XX[6-9]XXXXXXX'),(4,'0XX|XX[6-9]XXXXXXX'),(4,'0|XX9[4-9]XXXXXXX'),(4,'0XX|XX9[4-9]XXXXXXX');

--
-- Default group extension
--
INSERT INTO `core_groups` (`name`) VALUES ('Default');

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
INSERT INTO `sounds` VALUES ('a-m.wav','a-m','2015-08-21 16:12:57','AST','','pt_BR'),('access-password.wav','access-password','2015-08-21 16:12:57','AST','','pt_BR'),('Acre.wav','Acre','2015-08-21 16:12:57','AST','','pt_BR'),('activated.wav','activated','2015-08-21 16:12:57','AST','','pt_BR'),('afternoon.wav','afternoon','2015-08-21 16:12:57','AST','','pt_BR'),('agent-alreadyon.wav','agent-alreadyon','2015-08-21 16:12:57','AST','','pt_BR'),('agent-incorrect.wav','agent-incorrect','2015-08-21 16:12:57','AST','','pt_BR'),('agent-loggedoff.wav','agent-loggedoff','2015-08-21 16:12:57','AST','','pt_BR'),('agent-loginok.wav','agent-loginok','2015-08-21 16:12:57','AST','','pt_BR'),('agent-newlocation.wav','agent-newlocation','2015-08-21 16:12:57','AST','','pt_BR'),('agent-pass.wav','agent-pass','2015-08-21 16:12:57','AST','','pt_BR'),('agent-user.wav','agent-user','2015-08-21 16:12:57','AST','','pt_BR'),('Alagoas.wav','Alagoas','2015-08-21 16:12:57','AST','','pt_BR'),('all-circuits-busy-now.wav','all-circuits-busy-now','2015-08-21 16:12:57','AST','','pt_BR'),('Amapa.wav','Amapa','2015-08-21 16:12:57','AST','','pt_BR'),('Amazonas.wav','Amazonas','2015-08-21 16:12:57','AST','','pt_BR'),('an-error-has-occured.wav','an-error-has-occured','2015-08-21 16:12:58','AST','','pt_BR'),('and.wav','and','2015-08-21 16:12:57','AST','','pt_BR'),('Aracaju.wav','Aracaju','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-accountnum.wav','astcc-accountnum','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-and.wav','astcc-and','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-badaccount.wav','astcc-badaccount','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-badphone.wav','astcc-badphone','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-cents.wav','astcc-cents','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-connectcharge.wav','astcc-connectcharge','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-dollar.wav','astcc-dollar','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-dollars.wav','astcc-dollars','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-down.wav','astcc-down','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-forfirst.wav','astcc-forfirst','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-isbusy.wav','astcc-isbusy','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-minute.wav','astcc-minute','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-minutes.wav','astcc-minutes','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-noanswer.wav','astcc-noanswer','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-notenough.wav','astcc-notenough','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-nothing.wav','astcc-nothing','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-perminute.wav','astcc-perminute','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-phonenum.wav','astcc-phonenum','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-pleasewait.wav','astcc-pleasewait','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-point.wav','astcc-point','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-remaining.wav','astcc-remaining','2015-08-21 16:12:58','AST','','pt_BR'),('astcc-seconds.wav','astcc-seconds','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-secounds.wav','astcc-secounds','2015-08-21 16:12:58','AST','','pt_BR'),('astcc-silence.wav','astcc-silence','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-tone.wav','astcc-tone','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-unavail.wav','astcc-unavail','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-welcome.wav','astcc-welcome','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-willapply.wav','astcc-willapply','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-willcost.wav','astcc-willcost','2015-08-21 16:12:57','AST','','pt_BR'),('astcc-youhave.wav','astcc-youhave','2015-08-21 16:12:57','AST','','pt_BR'),('at-tone-time-exactly.wav','at-tone-time-exactly','2015-08-21 16:12:57','AST','','pt_BR'),('auth-thankyou.wav','auth-thankyou','2015-08-21 16:12:57','AST','','pt_BR'),('Bahia.wav','Bahia','2015-08-21 16:12:57','AST','','pt_BR'),('beep.wav','beep','2015-08-21 16:12:57','AST','','pt_BR'),('Belem.wav','Belem','2015-08-21 16:12:57','AST','','pt_BR'),('Belo-Horizonte.wav','Belo-Horizonte','2015-08-21 16:12:57','AST','','pt_BR'),('Boa-Vista.wav','Boa-Vista','2015-08-21 16:12:57','AST','','pt_BR'),('Brasilia.wav','Brasilia','2015-08-21 16:12:57','AST','','pt_BR'),('call-fwd-no-ans.wav','call-fwd-no-ans','2015-08-21 16:12:57','AST','','pt_BR'),('call-fwd-on-busy.wav','call-fwd-on-busy','2015-08-21 16:12:57','AST','','pt_BR'),('call-fwd-unconditional.wav','call-fwd-unconditional','2015-08-21 16:12:57','AST','','pt_BR'),('Campo-Grande.wav','Campo-Grande','2015-08-21 16:12:57','AST','','pt_BR'),('Ceara.wav','Ceara','2015-08-21 16:12:57','AST','','pt_BR'),('conf-enteringno.wav','conf-enteringno','2015-08-21 16:12:57','AST','','pt_BR'),('conf-errormenu.wav','conf-errormenu','2015-08-21 16:12:57','AST','','pt_BR'),('conf-getchannel.wav','conf-getchannel','2015-08-21 16:12:57','AST','','pt_BR'),('conf-getconfno.wav','conf-getconfno','2015-08-21 16:12:57','AST','','pt_BR'),('conf-getpin.wav','conf-getpin','2015-08-21 16:12:57','AST','','pt_BR'),('conf-hasjoin.wav','conf-hasjoin','2015-08-21 16:12:57','AST','','pt_BR'),('conf-hasleft.wav','conf-hasleft','2015-08-21 16:12:57','AST','','pt_BR'),('conf-invalid.wav','conf-invalid','2015-08-21 16:12:57','AST','','pt_BR'),('conf-invalidpin.wav','conf-invalidpin','2015-08-21 16:12:57','AST','','pt_BR'),('conf-kicked.wav','conf-kicked','2015-08-21 16:12:57','AST','','pt_BR'),('conf-leaderhasleft.wav','conf-leaderhasleft','2015-08-21 16:12:57','AST','','pt_BR'),('conf-locked.wav','conf-locked','2015-08-21 16:12:57','AST','','pt_BR'),('conf-lockednow.wav','conf-lockednow','2015-08-21 16:12:57','AST','','pt_BR'),('conf-muted.wav','conf-muted','2015-08-21 16:12:57','AST','','pt_BR'),('conf-noempty.wav','conf-noempty','2015-08-21 16:12:57','AST','','pt_BR'),('conf-onlyone.wav','conf-onlyone','2015-08-21 16:12:57','AST','','pt_BR'),('conf-onlyperson.wav','conf-onlyperson','2015-08-21 16:12:57','AST','','pt_BR'),('conf-otherinparty.wav','conf-otherinparty','2015-08-21 16:12:57','AST','','pt_BR'),('conf-placeintoconf.wav','conf-placeintoconf','2015-08-21 16:12:57','AST','','pt_BR'),('conf-thereare.wav','conf-thereare','2015-08-21 16:12:57','AST','','pt_BR'),('conf-unlockednow.wav','conf-unlockednow','2015-08-21 16:12:57','AST','','pt_BR'),('conf-unmuted.wav','conf-unmuted','2015-08-21 16:12:57','AST','','pt_BR'),('conf-usermenu.wav','conf-usermenu','2015-08-21 16:12:57','AST','','pt_BR'),('conf-userswilljoin.wav','conf-userswilljoin','2015-08-21 16:12:57','AST','','pt_BR'),('conf-userwilljoin.wav','conf-userwilljoin','2015-08-21 16:12:58','AST','','pt_BR'),('conf-waitforleader.wav','conf-waitforleader','2015-08-21 16:12:57','AST','','pt_BR'),('Cuiaba.wav','Cuiaba','2015-08-21 16:12:57','AST','','pt_BR'),('Curitiba.wav','Curitiba','2015-08-21 16:12:57','AST','','pt_BR'),('de-activated.wav','de-activated','2015-08-21 16:12:57','AST','','pt_BR'),('demo-abouttotry.wav','demo-abouttotry','2015-08-21 16:12:57','AST','','pt_BR'),('demo-congrats.wav','demo-congrats','2015-08-21 16:12:57','AST','','pt_BR'),('demo-echodone.wav','demo-echodone','2015-08-21 16:12:57','AST','','pt_BR'),('demo-echotest.wav','demo-echotest','2015-08-21 16:12:57','AST','','pt_BR'),('demo-enterkeywords.wav','demo-enterkeywords','2015-08-21 16:12:57','AST','','pt_BR'),('demo-instruct.wav','demo-instruct','2015-08-21 16:12:57','AST','','pt_BR'),('demo-moreinfo.wav','demo-moreinfo','2015-08-21 16:12:57','AST','','pt_BR'),('demo-nogo.wav','demo-nogo','2015-08-21 16:12:57','AST','','pt_BR'),('demo-nomatch.wav','demo-nomatch','2015-08-21 16:12:57','AST','','pt_BR'),('demo-thanks.wav','demo-thanks','2015-08-21 16:12:57','AST','','pt_BR'),('dir-instr.wav','dir-instr','2015-08-21 16:12:57','AST','','pt_BR'),('dir-intro-fn.wav','dir-intro-fn','2015-08-21 16:12:57','AST','','pt_BR'),('dir-intro-oper.wav','dir-intro-oper','2015-08-21 16:12:57','AST','','pt_BR'),('dir-intro.wav','dir-intro','2015-08-21 16:12:57','AST','','pt_BR'),('dir-nomatch.wav','dir-nomatch','2015-08-21 16:12:57','AST','','pt_BR'),('dir-nomore.wav','dir-nomore','2015-08-21 16:12:57','AST','','pt_BR'),('Distrito-Federal.wav','Distrito-Federal','2015-08-21 16:12:57','AST','','pt_BR'),('do-not-disturb.wav','do-not-disturb','2015-08-21 16:12:57','AST','','pt_BR'),('ent-target-attendant.wav','ent-target-attendant','2015-08-21 16:12:57','AST','','pt_BR'),('Espirito-Santo.wav','Espirito-Santo','2015-08-21 16:12:57','AST','','pt_BR'),('Exemplo.wav','Exemplo','2015-08-21 16:12:57','AST','','pt_BR'),('ext-disabled.wav','ext-disabled','2015-08-21 16:12:57','AST','','pt_BR'),('Florianopolis.wav','Florianopolis','2015-08-21 16:12:57','AST','','pt_BR'),('Fortaleza.wav','Fortaleza','2015-08-21 16:12:57','AST','','pt_BR'),('Goiania.wav','Goiania','2015-08-21 16:12:57','AST','','pt_BR'),('Goias.wav','Goias','2015-08-21 16:12:57','AST','','pt_BR'),('hello-world.wav','hello-world','2015-08-21 16:12:57','AST','','pt_BR'),('hour.wav','hour','2015-08-21 16:12:57','AST','','pt_BR'),('hours.wav','hours','2015-08-21 16:12:57','AST','','pt_BR'),('im-sorry.wav','im-sorry','2015-08-21 16:12:57','AST','','pt_BR'),('incorrect-password.wav','incorrect-password','2015-08-21 16:12:57','AST','','pt_BR'),('info-about-last-call.wav','info-about-last-call','2015-08-21 16:12:57','AST','','pt_BR'),('invalid.wav','invalid','2015-08-21 16:12:57','AST','','pt_BR'),('is-in-use.wav','is-in-use','2015-08-21 16:12:57','AST','','pt_BR'),('is-set-to.wav','is-set-to','2015-08-21 16:12:57','AST','','pt_BR'),('is.wav','is','2015-08-21 16:12:57','AST','','pt_BR'),('Joao-Pessoa.wav','Joao-Pessoa','2015-08-21 16:12:57','AST','','pt_BR'),('location.wav','location','2015-08-21 16:12:57','AST','','pt_BR'),('Macapa.wav','Macapa','2015-08-21 16:12:57','AST','','pt_BR'),('Maceio.wav','Maceio','2015-08-21 16:12:57','AST','','pt_BR'),('macroform-robot_dity.wav','macroform-robot_dity.wav','2015-08-31 13:53:33','MOH','default','pt_BR'),('macroform-the_simplicity.wav','macroform-the_simplicity.wav','2015-08-31 13:53:33','MOH','default','pt_BR'),('Manaus.wav','Manaus','2015-08-21 16:12:57','AST','','pt_BR'),('manolo_camp-morning_coffee.wav','manolo_camp-morning_coffee.wav','2015-08-31 13:53:33','MOH','default','pt_BR'),('Maranhao.wav','Maranhao','2015-08-21 16:12:57','AST','','pt_BR'),('Mato-Grosso-do-Sul.wav','Mato-Grosso-do-Sul','2015-08-21 16:12:57','AST','','pt_BR'),('Mato-Grosso.wav','Mato-Grosso','2015-08-21 16:12:57','AST','','pt_BR'),('Minas-Gerais.wav','Minas-Gerais','2015-08-21 16:12:57','AST','','pt_BR'),('minute.wav','minute','2015-08-21 16:12:57','AST','','pt_BR'),('minutes.wav','minutes','2015-08-21 16:12:57','AST','','pt_BR'),('morning.wav','morning','2015-08-21 16:12:57','AST','','pt_BR'),('Natal.wav','Natal','2015-08-21 16:12:57','AST','','pt_BR'),('night.wav','night','2015-08-21 16:12:57','AST','','pt_BR'),('no-rights.wav','no-rights','2015-08-21 16:12:57','AST','','pt_BR'),('number.wav','number','2015-08-21 16:12:57','AST','','pt_BR'),('one-moment-please.wav','one-moment-please','2015-08-21 16:12:57','AST','','pt_BR'),('Palmas.wav','Palmas','2015-08-21 16:12:57','AST','','pt_BR'),('Para.wav','Para','2015-08-21 16:12:57','AST','','pt_BR'),('Paraiba.wav','Paraiba','2015-08-21 16:12:57','AST','','pt_BR'),('Parana.wav','Parana','2015-08-21 16:12:57','AST','','pt_BR'),('pbx-invalid.wav','pbx-invalid','2015-08-21 16:12:57','AST','','pt_BR'),('pbx-invalidpark.wav','pbx-invalidpark','2015-08-21 16:12:57','AST','','pt_BR'),('pbx-transfer.wav','pbx-transfer','2015-08-21 16:12:57','AST','','pt_BR'),('Pernambuco.wav','Pernambuco','2015-08-21 16:12:58','AST','','pt_BR'),('Piaui.wav','Piaui','2015-08-21 16:12:57','AST','','pt_BR'),('pls-try-call-later.wav','pls-try-call-later','2015-08-21 16:12:57','AST','','pt_BR'),('pm-invalid-option.wav','pm-invalid-option','2015-08-21 16:12:57','AST','','pt_BR'),('Porto-Alegre.wav','Porto-Alegre','2015-08-21 16:12:57','AST','','pt_BR'),('Porto-Velho.wav','Porto-Velho','2015-08-21 16:12:57','AST','','pt_BR'),('pound.wav','pound','2015-08-21 16:12:57','AST','','pt_BR'),('press-1.wav','press-1','2015-08-21 16:12:57','AST','','pt_BR'),('press-2.wav','press-2','2015-08-21 16:12:58','AST','','pt_BR'),('press-3.wav','press-3','2015-08-21 16:12:57','AST','','pt_BR'),('press-star.wav','press-star','2015-08-21 16:12:57','AST','','pt_BR'),('privacy-incorrect.wav','privacy-incorrect','2015-08-21 16:12:57','AST','','pt_BR'),('privacy-prompt.wav','privacy-prompt','2015-08-21 16:12:57','AST','','pt_BR'),('privacy-thankyou.wav','privacy-thankyou','2015-08-21 16:12:57','AST','','pt_BR'),('privacy-unident.wav','privacy-unident','2015-08-21 16:12:57','AST','','pt_BR'),('queue-callswaiting.wav','queue-callswaiting','2015-08-21 16:12:57','AST','','pt_BR'),('queue-holdtime.wav','queue-holdtime','2015-08-21 16:12:57','AST','','pt_BR'),('queue-less-than.wav','queue-less-than','2015-08-21 16:12:57','AST','','pt_BR'),('queue-minutes.wav','queue-minutes','2015-08-21 16:12:57','AST','','pt_BR'),('queue-periodic-announce.wav','queue-periodic-announce','2015-08-21 16:12:57','AST','','pt_BR'),('queue-reporthold.wav','queue-reporthold','2015-08-21 16:12:57','AST','','pt_BR'),('queue-seconds.wav','queue-seconds','2015-08-21 16:12:57','AST','','pt_BR'),('queue-thankyou.wav','queue-thankyou','2015-08-21 16:12:57','AST','','pt_BR'),('queue-thereare.wav','queue-thereare','2015-08-21 16:12:57','AST','','pt_BR'),('queue-youarenext.wav','queue-youarenext','2015-08-21 16:12:57','AST','','pt_BR'),('Real.wav','Real','2015-08-21 16:12:57','AST','','pt_BR'),('Recife.wav','Recife','2015-08-21 16:12:57','AST','','pt_BR'),('reno_project-system.wav','reno_project-system.wav','2015-08-31 13:53:33','MOH','default','pt_BR'),('Rio-Branco.wav','Rio-Branco','2015-08-21 16:12:58','AST','','pt_BR'),('Rio-de-Janeiro.wav','Rio-de-Janeiro','2015-08-21 16:12:57','AST','','pt_BR'),('Rio-Grande-do-Norte.wav','Rio-Grande-do-Norte','2015-08-21 16:12:57','AST','','pt_BR'),('Rio-Grande-do-Sul.wav','Rio-Grande-do-Sul','2015-08-21 16:12:57','AST','','pt_BR'),('Rondonia.wav','Rondonia','2015-08-21 16:12:57','AST','','pt_BR'),('Roraima.wav','Roraima','2015-08-21 16:12:57','AST','','pt_BR'),('saida.wav','saida','2015-08-21 16:12:57','AST','','pt_BR'),('Salvador.wav','Salvador','2015-08-21 16:12:57','AST','','pt_BR'),('Santa-Catarina.wav','Santa-Catarina','2015-08-21 16:12:57','AST','','pt_BR'),('Sao-Luis.wav','Sao-Luis','2015-08-21 16:12:57','AST','','pt_BR'),('Sao-Paulo.wav','Sao-Paulo','2015-08-21 16:12:57','AST','','pt_BR'),('screen-callee-options.wav','screen-callee-options','2015-08-21 16:12:57','AST','','pt_BR'),('seconds.wav','seconds','2015-08-21 16:12:57','AST','','pt_BR'),('sec_opens.wav','sec_opens','2015-08-21 16:12:57','AST','','pt_BR'),('sec_opens2.wav','sec_opens2','2015-08-21 16:12:57','AST','','pt_BR'),('Sergipe.wav','Sergipe','2015-08-21 16:12:57','AST','','pt_BR'),('speed-dial-empty.wav','speed-dial-empty','2015-08-21 16:12:57','AST','','pt_BR'),('speed-dial.wav','speed-dial','2015-08-21 16:12:57','AST','','pt_BR'),('speed-enterlocation.wav','speed-enterlocation','2015-08-21 16:12:57','AST','','pt_BR'),('speed-enternumber.wav','speed-enternumber','2015-08-21 16:12:57','AST','','pt_BR'),('ss-noservice.wav','ss-noservice','2015-08-21 16:12:57','AST','','pt_BR'),('star.wav','star','2015-08-21 16:12:57','AST','','pt_BR'),('telephone-number.wav','telephone-number','2015-08-21 16:12:57','AST','','pt_BR'),('Teresina.wav','Teresina','2015-08-21 16:12:57','AST','','pt_BR'),('then-press-pound.wav','then-press-pound','2015-08-21 16:12:57','AST','','pt_BR'),('to-call-this-number.wav','to-call-this-number','2015-08-21 16:12:58','AST','','pt_BR'),('to-change.wav','to-change','2015-08-21 16:12:57','AST','','pt_BR'),('to-enter-a-diff.wav','to-enter-a-diff','2015-08-21 16:12:57','AST','','pt_BR'),('to-listen-to-it.wav','to-listen-to-it','2015-08-21 16:12:58','AST','','pt_BR'),('to-rerecord-it.wav','to-rerecord-it','2015-08-21 16:12:57','AST','','pt_BR'),('Tocantins.wav','Tocantins','2015-08-21 16:12:57','AST','','pt_BR'),('tt-allbusy.wav','tt-allbusy','2015-08-21 16:12:57','AST','','pt_BR'),('tt-monkeys.wav','tt-monkeys','2015-08-21 16:12:57','AST','','pt_BR'),('tt-monkeysintro.wav','tt-monkeysintro','2015-08-21 16:12:57','AST','','pt_BR'),('tt-somethingwrong.wav','tt-somethingwrong','2015-08-21 16:12:57','AST','','pt_BR'),('tt-weasels.wav','tt-weasels','2015-08-21 16:12:57','AST','','pt_BR'),('Vitoria.wav','Vitoria','2015-08-21 16:12:57','AST','','pt_BR'),('vm-advopts.wav','vm-advopts','2015-08-21 16:12:57','AST','','pt_BR'),('vm-and.wav','vm-and','2015-08-21 16:12:57','AST','','pt_BR'),('vm-calldiffnum.wav','vm-calldiffnum','2015-08-21 16:12:57','AST','','pt_BR'),('vm-changeto.wav','vm-changeto','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Cust1.wav','vm-Cust1','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Cust2.wav','vm-Cust2','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Cust3.wav','vm-Cust3','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Cust4.wav','vm-Cust4','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Cust5.wav','vm-Cust5','2015-08-21 16:12:57','AST','','pt_BR'),('vm-delete.wav','vm-delete','2015-08-21 16:12:57','AST','','pt_BR'),('vm-deleted.wav','vm-deleted','2015-08-21 16:12:57','AST','','pt_BR'),('vm-dialout.wav','vm-dialout','2015-08-21 16:12:57','AST','','pt_BR'),('vm-enter-num-to-call.wav','vm-enter-num-to-call','2015-08-21 16:12:57','AST','','pt_BR'),('vm-extension.wav','vm-extension','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Family.wav','vm-Family','2015-08-21 16:12:57','AST','','pt_BR'),('vm-first.wav','vm-first','2015-08-21 16:12:57','AST','','pt_BR'),('vm-for.wav','vm-for','2015-08-21 16:12:57','AST','','pt_BR'),('vm-forward.wav','vm-forward','2015-08-21 16:12:57','AST','','pt_BR'),('vm-forwardoptions.wav','vm-forwardoptions','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Friends.wav','vm-Friends','2015-08-21 16:12:57','AST','','pt_BR'),('vm-from-extension.wav','vm-from-extension','2015-08-21 16:12:57','AST','','pt_BR'),('vm-from-phonenumber.wav','vm-from-phonenumber','2015-08-21 16:12:57','AST','','pt_BR'),('vm-from.wav','vm-from','2015-08-21 16:12:57','AST','','pt_BR'),('vm-goodbye.wav','vm-goodbye','2015-08-21 16:12:57','AST','','pt_BR'),('vm-helpexit.wav','vm-helpexit','2015-08-21 16:12:57','AST','','pt_BR'),('vm-INBOX.wav','vm-INBOX','2015-08-21 16:12:57','AST','','pt_BR'),('vm-INBOXs.wav','vm-INBOXs','2015-08-21 16:12:57','AST','','pt_BR'),('vm-incorrect-mailbox.wav','vm-incorrect-mailbox','2015-08-21 16:12:57','AST','','pt_BR'),('vm-incorrect.wav','vm-incorrect','2015-08-21 16:12:57','AST','','pt_BR'),('vm-instructions.wav','vm-instructions','2015-08-21 16:12:57','AST','','pt_BR'),('vm-intro.wav','vm-intro','2015-08-21 16:12:57','AST','','pt_BR'),('vm-isonphone.wav','vm-isonphone','2015-08-21 16:12:57','AST','','pt_BR'),('vm-isunavail.wav','vm-isunavail','2015-08-21 16:12:57','AST','','pt_BR'),('vm-last.wav','vm-last','2015-08-21 16:12:57','AST','','pt_BR'),('vm-leavemsg.wav','vm-leavemsg','2015-08-21 16:12:57','AST','','pt_BR'),('vm-login.wav','vm-login','2015-08-21 16:12:57','AST','','pt_BR'),('vm-mailboxfull.wav','vm-mailboxfull','2015-08-21 16:12:57','AST','','pt_BR'),('vm-message.wav','vm-message','2015-08-21 16:12:57','AST','','pt_BR'),('vm-messages.wav','vm-messages','2015-08-21 16:12:57','AST','','pt_BR'),('vm-mismatch.wav','vm-mismatch','2015-08-21 16:12:57','AST','','pt_BR'),('vm-msginstruct.wav','vm-msginstruct','2015-08-21 16:12:57','AST','','pt_BR'),('vm-msgsaved.wav','vm-msgsaved','2015-08-21 16:12:57','AST','','pt_BR'),('vm-newpassword.wav','vm-newpassword','2015-08-21 16:12:57','AST','','pt_BR'),('vm-newuser.wav','vm-newuser','2015-08-21 16:12:57','AST','','pt_BR'),('vm-next.wav','vm-next','2015-08-21 16:12:57','AST','','pt_BR'),('vm-no.wav','vm-no','2015-08-21 16:12:57','AST','','pt_BR'),('vm-nobodyavail.wav','vm-nobodyavail','2015-08-21 16:12:57','AST','','pt_BR'),('vm-nobox.wav','vm-nobox','2015-08-21 16:12:57','AST','','pt_BR'),('vm-nomessages.wav','vm-nomessages','2015-08-21 16:12:57','AST','','pt_BR'),('vm-nomore.wav','vm-nomore','2015-08-21 16:12:57','AST','','pt_BR'),('vm-nonumber.wav','vm-nonumber','2015-08-21 16:12:57','AST','','pt_BR'),('vm-num-i-have.wav','vm-num-i-have','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Old.wav','vm-Old','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Olds.wav','vm-Olds','2015-08-21 16:12:57','AST','','pt_BR'),('vm-onefor.wav','vm-onefor','2015-08-21 16:12:57','AST','','pt_BR'),('vm-options.wav','vm-options','2015-08-21 16:12:57','AST','','pt_BR'),('vm-opts.wav','vm-opts','2015-08-21 16:12:57','AST','','pt_BR'),('vm-passchanged.wav','vm-passchanged','2015-08-21 16:12:57','AST','','pt_BR'),('vm-password.wav','vm-password','2015-08-21 16:12:57','AST','','pt_BR'),('vm-press.wav','vm-press','2015-08-21 16:12:57','AST','','pt_BR'),('vm-prev.wav','vm-prev','2015-08-21 16:12:57','AST','','pt_BR'),('vm-reachoper.wav','vm-reachoper','2015-08-21 16:12:57','AST','','pt_BR'),('vm-rec-busy.wav','vm-rec-busy','2015-08-21 16:12:57','AST','','pt_BR'),('vm-rec-name.wav','vm-rec-name','2015-08-21 16:12:57','AST','','pt_BR'),('vm-rec-temp.wav','vm-rec-temp','2015-08-21 16:12:57','AST','','pt_BR'),('vm-rec-unv.wav','vm-rec-unv','2015-08-21 16:12:57','AST','','pt_BR'),('vm-receive.wav','vm-receive','2015-08-21 16:12:57','AST','','pt_BR'),('vm-received.wav','vm-received','2015-08-21 16:12:58','AST','','pt_BR'),('vm-receiveo.wav','vm-receiveo','2015-08-21 16:12:57','AST','','pt_BR'),('vm-reenterpassword.wav','vm-reenterpassword','2015-08-21 16:12:57','AST','','pt_BR'),('vm-repeat.wav','vm-repeat','2015-08-21 16:12:57','AST','','pt_BR'),('vm-review.wav','vm-review','2015-08-21 16:12:57','AST','','pt_BR'),('vm-saved.wav','vm-saved','2015-08-21 16:12:58','AST','','pt_BR'),('vm-savedto.wav','vm-savedto','2015-08-21 16:12:57','AST','','pt_BR'),('vm-savefolder.wav','vm-savefolder','2015-08-21 16:12:57','AST','','pt_BR'),('vm-savemessage.wav','vm-savemessage','2015-08-21 16:12:57','AST','','pt_BR'),('vm-saveoper.wav','vm-saveoper','2015-08-21 16:12:57','AST','','pt_BR'),('vm-sorry.wav','vm-sorry','2015-08-21 16:12:57','AST','','pt_BR'),('vm-star-cancel.wav','vm-star-cancel','2015-08-21 16:12:58','AST','','pt_BR'),('vm-starmain.wav','vm-starmain','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tempgreeting.wav','vm-tempgreeting','2015-08-21 16:12:58','AST','','pt_BR'),('vm-tempgreeting2.wav','vm-tempgreeting2','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tempremoved.wav','vm-tempremoved','2015-08-21 16:12:57','AST','','pt_BR'),('vm-then-pound.wav','vm-then-pound','2015-08-21 16:12:57','AST','','pt_BR'),('vm-theperson.wav','vm-theperson','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tocallback.wav','vm-tocallback','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tocallnum.wav','vm-tocallnum','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tocancel.wav','vm-tocancel','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tocancelmsg.wav','vm-tocancelmsg','2015-08-21 16:12:57','AST','','pt_BR'),('vm-toenternumber.wav','vm-toenternumber','2015-08-21 16:12:57','AST','','pt_BR'),('vm-toforward.wav','vm-toforward','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tohearenv.wav','vm-tohearenv','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tomakecall.wav','vm-tomakecall','2015-08-21 16:12:57','AST','','pt_BR'),('vm-tooshort.wav','vm-tooshort','2015-08-21 16:12:57','AST','','pt_BR'),('vm-toreply.wav','vm-toreply','2015-08-21 16:12:58','AST','','pt_BR'),('vm-torerecord.wav','vm-torerecord','2015-08-21 16:12:57','AST','','pt_BR'),('vm-undelete.wav','vm-undelete','2015-08-21 16:12:57','AST','','pt_BR'),('vm-undeleted.wav','vm-undeleted','2015-08-21 16:12:57','AST','','pt_BR'),('vm-unknown-caller.wav','vm-unknown-caller','2015-08-21 16:12:57','AST','','pt_BR'),('vm-whichbox.wav','vm-whichbox','2015-08-21 16:12:57','AST','','pt_BR'),('vm-Work.wav','vm-Work','2015-08-21 16:12:57','AST','','pt_BR'),('vm-youhave.wav','vm-youhave','2015-08-21 16:12:57','AST','','pt_BR'),('vm-youhaveno.wav','vm-youhaveno','2015-08-21 16:12:57','AST','','pt_BR');
--
-- Default user admin
--
INSERT INTO profiles (name, created, updated) VALUES ('default',now(),now());
INSERT INTO users (name, password,email,profile_id, created, updated) VALUES ('admin','0192023a7bbd73250516f069df18b500','suporte@opens.com.br',1,now(),now());

--
-- Default pickup group
--
INSERT INTO `grupos` (`cod_grupo`, `nome`) VALUES (1, 'GERAL');

--
-- Default rule
--
INSERT INTO `regras_negocio` VALUES ('',0,'Internas - Ramal para Ramal','G:all','G:all','00:00:00-23:59:59','sun,mon,tue,wed,thu,fri,sat',0,1,0,0,'others');
INSERT INTO `regras_negocio_actions` VALUES (1,0,'PBX_Rule_Action_CCustos'),(1,1,'PBX_Rule_Action_DiscarRamal');
INSERT INTO `regras_negocio_actions_config` VALUES (1,0,'ccustos','9'),(1,1,'allow_voicemail','false'),(1,1,'dial_flags','twk'),(1,1,'dial_timeout','60'),(1,1,'diff_ring','false'),(1,1,'dont_overflow','false'),(1,1,'resolv_agent','false');

INSERT INTO `core_config` (`config_module`, `config_name`, `config_value`) VALUES ('default', 'host_notification', 'http://api.opens.com.br:3003/notifications'), ("default","userfield","TS_AAMMDD_HHii_SR_DS"),("default","userfield_ud",""), ('default','host_inspect','http://api.opens.com.br:8080');
