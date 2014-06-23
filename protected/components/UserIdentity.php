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

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

	private $_id;
	//private $_isAdmin;

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$user = User::model()->find('LOWER(username)=?', array(strtolower($this->username)));
		if ($user === null){
			$user = User::model()->find('LOWER(email)=?', array(strtolower($this->username)));
			if ($user === null){
				$this->errorCode = self::ERROR_USERNAME_INVALID;
				return 0;
			}
		}

		if (!$user->validatePassword($this->password)){
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return 0;
		}
		elseif (!$user->active) {
			showMessage(Yii::t('common', 'Login'), Yii::t('common', 'Your account not active. The reasons: you not followed the link in the letter which has been sent at registration. Or administrator deactivate your account'), null, true);
			return 0;
		}
		else {
			$this->_id = $user->id;
			//$this->_isAdmin = $user->isAdmin;
			if($user->isAdmin){
				$this->setState('isAdmin', $user->isAdmin);
			}
			$this->username = $user->username;

			$this->setState('email', $user->email);
			$this->setState('username', $user->username);
			$this->setState('phone', $user->phone);

			$this->errorCode = self::ERROR_NONE;
		}
		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId() {
		return $this->_id;
	}

	/*public function isAdmin() {
		return $this->_isAdmin;
	}*/

}