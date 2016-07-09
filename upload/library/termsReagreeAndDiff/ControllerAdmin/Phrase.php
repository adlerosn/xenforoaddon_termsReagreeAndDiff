<?php

class termsReagreeAndDiff_ControllerAdmin_Phrase extends XFCP_termsReagreeAndDiff_ControllerAdmin_Phrase{
	/** @override
	 * Saves a phrase. This may either be an insert or an update.
	 *
	 * @return XenForo_ControllerResponse
	 */
	public function actionSave(){
		$returnValue = parent::actionSave();
		/*
		termsReagreeAndDiff_Model::uninstall();
		termsReagreeAndDiff_Model::install();
		termsReagreeAndDiff_Model::insertTos();
		/*/
		termsReagreeAndDiff_Model::insertTos();
		//*/
		return $returnValue;
	}
}
