<?php

class termsReagreeAndDiff_ControllerPublic_Register extends XFCP_termsReagreeAndDiff_ControllerPublic_Register {
	protected function _completeRegistration(array $user, array $extraParams = array()){
		$returnValue = parent::_completeRegistration($user,$extraParams);
		termsReagreeAndDiff_Model::agreeWithLatestTerms($user['user_id']);
		return $returnValue;
	}
}
