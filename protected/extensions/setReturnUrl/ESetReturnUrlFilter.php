<?php
/**
* SetReturnUrl Filter
*
* Keep current URL (if it's not an AJAX url) in session so that the browser may
* be redirected back.
* @version 1.0.2
* @author creocoder <creocoder@gmail.com>
*/

class ESetReturnUrlFilter extends CFilter {
	public $staticAllow = array('index', 'view', 'create', 'update', 'bookingform', 'complain', 'mainform', 'add');

	protected function preFilter($filterChain) {
		$app = Yii::app();
		$request = $app->getRequest();

		if(!$request->getIsAjaxRequest()) {
			/*$mca = Yii::app()->urlManager->parseUrl($request);
			if ($mca) {
				$mcaArr = explode('/', $mca);

				if (is_array($mcaArr) && count($mcaArr) == 3) {
					if (in_array($mcaArr[2], $this->staticAllow)) {*/
						$app->getUser()->setReturnUrl($request->getUrl());
					/*}
				}

			}*/
		}

		return true;
	}
}