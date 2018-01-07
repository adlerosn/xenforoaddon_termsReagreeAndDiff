<?php
class termsReagreeAndDiff_ControllerPublic_Help extends XFCP_termsReagreeAndDiff_ControllerPublic_Help
{
	public function actionTermschanges(){
		$options = XenForo_Application::get('options');
		$visitor = XenForo_Visitor::getInstance();
		$uid = $visitor['user_id'];
		if(!$uid) return parent::actionTerms();
		$dismissable = termsReagreeAndDiff_Model::checkAgreementDismissable($uid);
		if($dismissable[0]) return parent::actionTerms();
		if (!$options->tosUrl['type'])
		{
			termsReagreeAndDiff_Model::agreeWithLatestTerms($uid);
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT,
				XenForo_Link::buildPublicLink('index')
			);
		}
		else if ($options->tosUrl['type'] == 'custom')
		{
			termsReagreeAndDiff_Model::agreeWithLatestTerms($uid);
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT,
				$options->tosUrl['custom']
			);
		}
		
		$diffObj = new XenForo_Diff();
		$OldTosId= $dismissable[1]['OldTosId'];
		$Old= termsReagreeAndDiff_Model::getTosById($OldTosId);
		$NewTosId= $dismissable[1]['NewTosId'];
		$New= termsReagreeAndDiff_Model::getTosById($NewTosId);
		/* New and Old
		 * ===========
		 * 
		 * --> tid
		 * --> uts
		 * --> ts
		 * --> tos
		 */
		$diff = $diffObj->findDifferences($Old['tos'],$New['tos']);
		$agree = $this->_input->filterSingle('agree', XenForo_Input::STRING);
		if($agree=='yes'){
			termsReagreeAndDiff_Model::agreeWithLatestTerms($uid);
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				$this->getDynamicRedirect(false, false)
			);
		}
		$redir = $this->_input->filterSingle('redirect', XenForo_Input::STRING);
		return $this->_getWrapper('terms',
			$this->responseView('XenForo_ViewPublic_Help_Terms', 'kiror_help_terms_changes',array(
				'prevFTs'=>$Old['ts'],
				'currFTs'=>$New['ts'],
				'prev'=>$Old['uts'],
				'curr'=>$New['uts'],
				'diffArr'=>$diff,
				'redir'=>$redir
				)
			)
		);
	}
}
