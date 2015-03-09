/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'snep-icons\'">' + entity + '</span>' + html;
	}
	var icons = {
		'sn-dashboard': '&#xe622;',
		'sn-status': '&#xe602;',
		'sn-status-ip': '&#xe603;',
		'sn-links': '&#xe604;',
		'sn-erros-links': '&#xe605;',
		'sn-logs': '&#xe606;',
		'sn-relatorios': '&#xe607;',
		'sn-chamadas': '&#xe608;',
		'sn-ranking': '&#xe609;',
		'sn-servicos': '&#xe60a;',
		'sn-busca': '&#xe60b;',
		'sn-cadastros': '&#xe60c;',
		'sn-usuarios': '&#xe60d;',
		'sn-perfis': '&#xe60e;',
		'sn-ramal': '&#xe60f;',
		'sn-grupo-ramais': '&#xe610;',
		'sn-etiqueta': '&#xe611;',
		'sn-troncos': '&#xe612;',
		'sn-contatos': '&#xe613;',
		'sn-grupo-contatos': '&#xe614;',
		'sn-filas': '&#xe615;',
		'sn-grupo-filas': '&#xe616;',
		'sn-conferencia': '&#xe617;',
		'sn-regras': '&#xe618;',
		'sn-rotas': '&#xe619;',
		'sn-conf-padrao': '&#xe61a;',
		'sn-alias': '&#xe61b;',
		'sn-configuracao': '&#xe61c;',
		'sn-parametros': '&#xe61d;',
		'sn-arquivo-som': '&#xe61e;',
		'sn-som-espera': '&#xe61f;',
		'sn-status-sistema': '&#xe620;',
		'sn-modulos': '&#xe621;',
		'sn-grupo-captura': '&#xe600;',
		'sn-simulador-regra': '&#xe601;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/sn-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
