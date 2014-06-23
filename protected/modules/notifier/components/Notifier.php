<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.8.1
*	copyright			:	(c) 2014 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
*
* This file is part of CMS Open Real Estate
*
* Open Real Estate is free software. This work is licensed under a GNU GPL.
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
***********************************************************************************************/

class Notifier {
	private $_adminRules;
	private $_userRules;
	private $init = 0;
	private $lang;
    private $sendToAdmin = false;

    private $_params = array();

	public function init(){
        Yii::import('application.modules.notifier.models.NotifierModel');

		$this->lang['admin'] = Lang::getAdminMailLang();
		$this->lang['default'] = Lang::getDefaultLang();
		$this->lang['current'] = Yii::app()->language;

		Yii::app()->setLanguage($this->lang['admin']);

		$this->_adminRules = array(
			'onNewBooking' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end', 'apartment_id', 'ownerEmail'),
				'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewUser' => array(
				'fields' => array('email', 'username'),
				'url' => array(
					'/users/backend/main/admin',
				),
				'active' => param('module_notifier_adminNewUser', 1),
			),
			'onNewContactform' => array(
				'fields' => array('name', 'email', 'phone', 'body'),
				'subject' => tt('New message (contact form)', 'notifier'),
				'body' => tt('New message from ::name (::email ::phone). Message text: ::body', 'notifier')."\n",
				'active' => param('module_notifier_adminNewContactform', 1),
			),
			'onOfflinePayment' => array(
				'fields' => array('amount', 'currency_charcode'),
				'subject' => tt('New payment through a bank.', 'notifier'),
				'body' => tt('New payment through a bank in the amount of ::amount ::currency_charcode', 'notifier'),
				'active' => param('module_notifier_adminNewPayment', 1)
			),
			'onRequestProperty' => array(
				'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'ownerEmail', 'apartmentUrl'),
				'active' => param('module_request_property_send_admin', 1),
			),
			'onNewComment' => array(
				'fields' => array('rating', 'user_email', 'body', 'user_name'),
				'active' => param('module_notifier_userNewComment', 1),
			),
			'onNewApartment' => array(
				'fields' => array('id'),
				'active' => param('module_notifier_adminNewApartment', 1),
			),
			'onNewComplain' => array(
				'fields' => array('apartment_id', 'email', 'name', 'body'),
				'subject' => Yii::t('module_notifier', 'New complain added.'),
				'body' => Yii::t('module_notifier', 'New complain was added. From ::name (::email). Complain text: ::body')."\n".
					Yii::t('module_notifier', 'You can view it at').' ::host'.Yii::app()->controller->createUrl('/apartmentsComplain/backend/main/admin'),
				'active' => param('module_notifier_adminNewComplain', 1),
			),
			'onNewReview' => array(
				'fields' => array('name', 'body'),
				'active' => param('module_notifier_adminNewReview', 1),
			),
		);

		Yii::app()->setLanguage($this->lang['current']);
		$this->setLang();

		$this->_userRules = array(
            'onNewBooking' => array(
                'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end', 'apartment_id'),
                'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
                'active' => param('module_notifier_ownerNewBooking', 1),
            ),
			'onNewUser' => array(
				'fields' => array('email', 'password', 'activateLink'),
				'url' => array('/usercpanel/main/index'),
				'active' => param('module_notifier_userNewUser', 1),
			),
			'onRecoveryPassword' => array(
				'fields' => array('email', 'temprecoverpassword', 'recoverPasswordLink'),
				'url' => array('/usercpanel/main/index'),
				'active' => 1,
			),
            'onNewComment' => array(
                'fields' => array('rating', 'user_email', 'body', 'user_name'),
                'active' => param('module_notifier_adminNewComment', 1),
            ),
			'onRequestProperty' => array(
                'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'apartmentUrl', 'ownerEmail'),
                'active' => param('module_request_property_send_user', 1),
            ),
			'onNewAgent' => array(
                'fields' => array('username', 'email', 'phone'),
                'active' => param('module_notifier_new_agent', 1),
            ),
    	);

        $this->init = 1;

        $this->restoreLang();
	}

	public function setLang(){
		if(Yii::app()->user->getState('isAdmin')){
			Yii::app()->setLanguage($this->lang['default']);
		}
	}
	public function restoreLang(){
		if(Yii::app()->user->getState('isAdmin') && $this->lang['current']){
			Yii::app()->setLanguage($this->lang['current']);
		}
	}

    public static function getRules(){
        $notify = new Notifier();
        $notify->init();

        return array(
            'admin' => $notify->_adminRules,
            'user' => $notify->_userRules,
        );
    }

	public function raiseEvent($eventName, $model, $params = array()){
        Yii::import('application.modules.notifier.models.NotifierModel');

        if($this->init == 0){
            $this->init();
        }

        $notifyModel = NotifierModel::model()->findByAttributes(array('event' => $eventName));

        if(!$notifyModel){
            return false;
        }

        $this->_params = $params;

        $forceEmail = $this->getFromParam('forceEmail');

        $to = '';
        if ($forceEmail) {
            $to = $forceEmail;
        } else {
            $user = $this->getFromParam('user');
            if ($user){
                $to = $user->email;
            }
        }

        if(isset($this->_userRules[$eventName]) && $to){
            $rules = $this->_userRules[$eventName];

            $rules['subject'] = $notifyModel->getStrByLang('subject');
            $rules['body'] = $notifyModel->getStrByLang('body');

            if($rules['active']){
                $this->_processEvent($rules, $model, $to);
            }
        }

        if(isset($this->_adminRules[$eventName])){
            $rules = $this->_adminRules[$eventName];

            $rules['subject'] = $notifyModel->getStrByLang('subject_admin');
            $rules['body'] = $notifyModel->getStrByLang('body_admin');

            $this->sendToAdmin = true;

            if($rules['active']){
                $this->_processEvent($rules, $model, param('adminEmail'));
            }
        }
	}

    private function getFromParam($key){
        return isset($this->_params[$key]) ? $this->_params[$key] : NULL;
    }

	private function _processEvent($rule, $model, $to){
        $user = $this->getFromParam('user');

		if($this->sendToAdmin)
			$lang = 'admin';
		else
			$lang = Yii::app()->user->getState('isAdmin') ? 'default' : 'current';

		$body = '';
		if(isset($rule['body'])){
			$body = $rule['body'];
			$body = str_replace('{host}', Yii::app()->request->hostInfo, $body);
			$body = str_replace('{fullhost}', Yii::app()->getBaseUrl(true), $body);

			if($user && !isset($model->username) && !isset($model->ownerName)){
				$body = str_replace('{username}', $user->username, $body);
			}

			if(isset($rule['url']) && $model){
				$params = array();
				if(isset($rule['url'][1])){
					foreach($rule['url'][1] as $param){
						$params[$param] = $model->$param;
					}
					$params['lang'] = $lang;
				}
				$url = Yii::app()->controller->createUrl($rule['url'][0], $params);
				$body = str_replace('{url}', $url, $body);
			}

			if(isset($rule['fields']) && $model){
				foreach($rule['fields'] as $field){
                    $val = isset($model->$field) ? $model->$field : tc('No information');
                    $body = str_replace('{'.$field.'}', $val, $body);
				}
			}

			if(isset($rule['i18nFields']) && $model){
				foreach($rule['i18nFields'] as $field){
					$field_val = $model->$field;
					$body = str_replace('{'.$field.'}', (isset($field_val[$lang]) ? CHtml::encode($field_val[$lang]) : tc('No information')), $body);
				}
			}
			$body = str_replace("\n.", "\n..", $body);
		}

        if($body){
	        Yii::import('application.extensions.mailer.EMailer');
	        $mailer = new EMailer();

            if (param('mailUseSMTP', 0)) {
	            $mailer->IsSMTP();
				$mailer->SMTPAuth = true;

	            $mailer->Host = param('mailSMTPHost', 'localhost');
	            $mailer->Port = param('mailSMTPPort', 25);

	            $mailer->Username = param('mailSMTPLogin');  // SMTP login
	            $mailer->Password = param('mailSMTPPass'); // SMTP password
            }

	        $mailer->From = param('adminEmail');
	        $mailer->FromName = param('mail_fromName', User::getAdminName());

		    $mailer->AddAddress($to);

            if(isset($rule['subject']))
	            $mailer->Subject = $rule['subject'];

	        $mailer->Body = $body;
	        $mailer->CharSet = 'UTF-8';
	        $mailer->IsHTML(true);

            if (!$mailer->Send()){
                throw new CHttpException(503, tt('message_not_send', 'notifier') . ' ErrorInfo: ' . $mailer->ErrorInfo);
                //showMessage(tc('Error'), tt('message_not_send', 'notifier'));
            }
        }
	}

}