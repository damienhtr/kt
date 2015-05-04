<?php

require_once(KT_LIB_DIR . '/dashboard/dashlet.inc.php');
require_once('ChantierUtil.php');

class ChantierDashlet extends KTBaseDashlet {
	
	function ChantierDashlet()
	{
		$this->sTitle='Suivi des chantiers';
	}
	
	function render() {
		$oTemplating = new KTTemplating;
		$oTemplate = $oTemplating->loadTemplate("kt3/chantier/dashboard");
		$chantiers = ChantierUtil::getUncompleteChantier();
		$aTemplateData = array(
			'chantiers' => $chantiers,
		);
		return $oTemplate->render($aTemplateData);
	}
}