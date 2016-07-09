<?php

class termsReagreeAndDiff_NoticesPrepareCallback {
	public static function noticesPrepare(array &$noticeList, array &$noticeTokens, XenForo_Template_Abstract $template, array $containerData){
		$visitor = XenForo_Visitor::getInstance();
		$uid = $visitor['user_id'];
		$noticeModel = XenForo_Model::create('XenForo_Model_Notice');
		if($uid){
			$seeingTerms = false;
			try{$seeingTerms=termsReagreeAndDiff_Model::seeingTerms($template->getParam('requestPaths')['requestUri']);}catch(Exception $e){;};
			if(!$seeingTerms){
				if(!(termsReagreeAndDiff_Model::checkAgreementDismissable($uid)[0])){
					$noticeId = 'terms_of_service_and_rules_updated';
					$noticeList[$noticeId] = $noticeModel->getDefaultNotice();
					$noticeList[$noticeId]['notice_id'] = $noticeId;
					$noticeList[$noticeId]['title'] = new XenForo_Phrase('terms_of_service_and_rules_updated');
					$noticeList[$noticeId]['message'] = $template->create('kiror_notice_terms_of_service_and_rules_updated', $template->getParams());
					$noticeList[$noticeId]['wrap'] = true;
					$noticeList[$noticeId]['dismissible'] = false;
					$noticeList[$noticeId]['css_class'] = '';
					$noticeList[$noticeId]['auto_dismiss'] = false;
				}
			}
		}
	}
}
