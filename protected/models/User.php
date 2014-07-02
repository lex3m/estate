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

class User extends ParentModel {

    const TYPE_PRIVATE_PERSON = 1;
    const TYPE_AGENCY = 2;
    const TYPE_AGENT = 3;
    const TYPE_ADMIN = 42;

    const AVA_PREFIX = 'ava_';

    const AGENT_STATUS_AWAIT_VERIFY = 0;
    const AGENT_STATUS_CONFIRMED = 1;

	private static $_saltAddon = 'openre';
	public $password_repeat;
	public $old_password;
	public $verifyCode;
	public $activateLink;
	public $recoverPasswordLink;

    public static function getAgentStatusList(){
        return array(
            self::AGENT_STATUS_AWAIT_VERIFY => tt('Waiting for acknowledge', 'users'),
            self::AGENT_STATUS_CONFIRMED => tt('Confirmed', 'users'),
        );
    }

    public function getAgentStatusName(){
        $list = self::getAgentStatusList();
        return isset($list[$this->agent_status]) ? $list[$this->agent_status] : '';
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{users}}';
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

    public function relations() {
        $relation = array();
        if(issetModule('payment')){
            $relation['payments'] = array(self::HAS_MANY, 'Payments', 'user_id', 'order'=>'payments.date_created DESC');
        }
        $relation['countAdRel'] = array(self::STAT, 'Apartment', 'owner_id', 'condition' => 'active = 1 AND owner_active = 1');
        return $relation;
    }

    public function getCountAd(){
        if($this->type != User::TYPE_AGENCY){
            return (int) $this->countAdRel;
        }
        $sql = "SELECT id FROM {{users}} WHERE agency_user_id = :user_id AND agent_status=:status";
        $agentsId = Yii::app()->db->createCommand($sql)->queryColumn(array(':user_id' => $this->id, ':status' => User::AGENT_STATUS_CONFIRMED));
        if(!$agentsId){
            return (int) $this->countAdRel;
        }
        $sql = "SELECT count(id) FROM {{apartment}} WHERE active = 1 AND owner_active = 1 AND (owner_id = :user_id OR owner_id IN (".implode(',', $agentsId)."))";
        return (int) Yii::app()->db->createCommand($sql)->queryScalar(array(':user_id' => $this->id));
    }

	public function rules() {
		return array(
			array('username, password, salt, email, temprecoverpassword, agency_name, ava', 'length', 'max' => 128),
			array('phone', 'length', 'max' => 15),
			array('email, phone, username', 'required', 'on' => 'usercpanel'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'usercpanel'),

			array('password, password_repeat', 'required', 'on' => 'changePass, changeAdminPass'),
			array('password', 'compare', 'on' => 'changePass, backend, changeAdminPass',
				'message' => tt('Passwords are not equivalent! Try again.', 'usercpanel')),
			array('password_repeat', 'safe'),
			array('password', 'length', 'min' => 6, 'on' => 'changePass, backend, changeAdminPass',
				'tooShort' => tt('Password too short! Minimum allowed length is 6 chars.', 'usercpanel')
			),

			array('username, email, password, password_repeat, phone', 'required', 'on' => 'backend'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'backend'),

			array('email, phone, username', 'required', 'on' => 'update'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'update'),
			array('email', 'email'),
			array('email', 'unique'),

			array('old_password', 'required', 'on' => 'changeAdminPass'),
			array('balance, type, agency_user_id', 'numerical', 'integerOnly' => true),

            array('agency_name', 'checkCompanyName'),

			array('username, email, verifyCode, phone', 'required', 'on' => 'register'),
			array('verifyCode', 'captcha', 'on' => 'register'),
			array('active, activatekey, agency_user_id, agent_status', 'safe'),
		);
	}

    public function checkCompanyName(){
        if($this->type == self::TYPE_AGENCY){
            if(!$this->agency_name){
                $this->addError('agency_name', tc('Enter agency name'));
            }

            $addWhere = '';
            if($this->id){
                $addWhere = ' AND id != ' . (int) $this->id;
            }

            $exist = Yii::app()->db->createCommand("SELECT id FROM {{users}} WHERE agency_name=:name" . $addWhere)->queryScalar(array(':name' => $this->agency_name));
            if($exist){
                $this->addError('agency_name', tc('The Agency with the same name already registered'));
            }
        }
    }

	public function i18nFields(){
        return array(
            'additional_info' => 'text not null',
        );
    }

	public function currencyFields(){
		return array('balance');
	}

	public function attributeLabels() {
		$return = array(
			'id' => 'Id',
			'username' => tt('Your name', 'usercpanel'),
			'password' => 'Password',
			'password_repeat' => tt('Repeat password','usercpanel'),
			'old_password' => tt('Current administrator password', 'adminpass'),
			'email' => tt('E-mail', 'users'),
			'phone' => Yii::t('common', 'Your phone number'),
			'Login (email)' => Yii::t('common', 'Login (email)'),
			'verifyCode' => Yii::t('common', 'Verify Code'),
			'additional_info' => tt('Additional info', 'usercpanel'),
			'balance' => tc('balance'),
			'type' => tc('Type'),
			'agency_name' => tc('Agency name'),
			'agency_user_id' => tc('Agency name'),
		);
		if($this->scenario == 'changePass' || $this->scenario == 'changeAdminPass'){
			$return['password'] = tt('Enter new password', 'usercpanel');
		}
		if($this->scenario == 'usercpanel'){
			$return['email'] = tt('Your e-mail', 'usercpanel');
		}
		if($this->scenario == 'backend' || $this->scenario == 'update'){
			$return['email'] = tt('E-mail', 'users');
			$return['username'] = tt('User name', 'users');
			$return['password'] = tt('Password', 'users');
			$return['phone'] = Yii::t('common', 'Phone number');
		}

		return $return;
	}

    public function scopes(){
        return array(
            'active' => array('condition' => $this->getTableAlias().'.active = 1'),
            'myAgents' => array('condition' => $this->getTableAlias().'.agency_user_id = ' . Yii::app()->user->id),
        );
    }

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password) {
		return self::hashPassword($password, $this->salt) === $this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @param string salt
	 * @return string hash
	 */
	public static function hashPassword($password, $salt) {
		return md5($salt . $password . $salt . self::$_saltAddon);
	}

	/**
	 * Generates a salt that can be used to generate a password hash.
	 * @return string the salt
	 */
	public static function generateSalt() {
		return uniqid('', true);
	}

	public function setPassword($password = null){
		$this->salt = self::generateSalt();
		if($password == null){
			$password = $this->password;
		}
		$this->password = md5($this->salt . $password . $this->salt . self::$_saltAddon);
	}

	public function setTempRecoverPassword($password = null){
		$this->salt = self::generateSalt();
		if($password == null){
			$password = $this->temprecoverpassword;
		}
		$this->temprecoverpassword = md5($this->salt . $password . $this->salt . self::$_saltAddon);
	}

	public function randomString($length = 10){
		$chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
		shuffle($chars);
		return implode('', array_slice($chars, 0, $length));
	}

	public function randomStringNonNumeric($length = 10){
		$characters = 'abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	public function search(){
		$criteria=new CDbCriteria;

		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare($this->getTableAlias().'.type',$this->type);

		if ($this->active != 'all')
		    $criteria->compare('active', $this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterDelete(){

		$sql = 'DELETE FROM {{users_social}} WHERE user_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{comments}} WHERE owner_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'UPDATE {{apartment}} SET owner_id=1, owner_active=:active, active=:inactive WHERE owner_id=:userId';
		Yii::app()->db->createCommand($sql)->execute(array(
			':active' => Apartment::STATUS_ACTIVE,
			':inactive' => Apartment::STATUS_INACTIVE,
			':userId' => $this->id,
		));

		if (issetModule('comparisonList')) {
			$sql = 'DELETE FROM {{comparison_list}} WHERE user_id="'.$this->id.'"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		self::destroyUserSession($this->id);

		return parent::afterDelete();
	}

	public function beforeSave() {
//		foreach (Lang::getActiveLangs() as $key => $item) {
//			$additionalInfo = 'additional_info_'.$item;
//			if (isset($this->$additionalInfo) && !empty($this->$additionalInfo)) {
//				$this->$additionalInfo = nl2br($this->$additionalInfo);
//			}
//		}
        //fix for not null default value temprecoverpassword
        $this->temprecoverpassword = '';
        $this->type = ( $this->type && in_array($this->type, self::getTypeList('key')) ) ? $this->type : User::TYPE_PRIVATE_PERSON;

		return parent::beforeSave();
	}

	public function getAdditionalInfo(){
        return $this->getStrByLang('additional_info');
    }

	public static function getRandomEmail(){
		$email = self::getRandomWord(8)."@null.io";
		return $email;
	}

	public static function getIdByUid($uid = false, $service = false) {
		$id = false;
		if ($uid) {
			$serviceCond = '';
			if ($service) { $serviceCond = ' AND service = "'.$service.'" '; }
			$id = Yii::app()->db->createCommand()
						->select('user_id')
						->from('{{users_social}}')
						->where('uid = "'.$uid.'" '.$serviceCond.'')
						->queryScalar();
		}
		return $id;
	}

	public static function setSocialUid($user_id, $uid, $service = '') {
		if ($user_id && $uid) {
			Yii::app()->db->createCommand()
					->insert('{{users_social}}', array(
						'user_id' => $user_id,
						'uid' => $uid,
						'service' => $service
					));
			return true;
		}
		return false;
	}

	public static function getRandomWord($size = 0){
		$word = md5(microtime(true));
		if (!$size)
			return $word;
		$subword = substr($word, $size*-1);
		return $subword;
	}

	static function getAdminName(){
		$sql = 'SELECT username FROM {{users}} WHERE isAdmin=1 LIMIT 1';
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}

	public static function getModeListShow(){

		$modeInState = Yii::app()->user->getState('mode_list_show');
		$settingsMode = param('mode_list_show', 'block');

		if ($modeInState == 'map' && !param('useGoogleMap', 0) && !param('useYandexMap', 0) && !param('useOSMMap', 0)) {
			$modeInState = 'block';
			Yii::app()->user->setState('mode_list_show', $modeInState);
			ConfigurationModel::updateValue('mode_list_show', $modeInState);
			Configuration::clearCache();
		}

		$modeInState = $modeInState ? $modeInState : $settingsMode;

		$modeInGet = Yii::app()->request->getParam('ls', $modeInState);

		if($modeInGet != $modeInState){
			Yii::app()->user->setState('mode_list_show', $modeInGet);

			$modeInState = $modeInGet;
		}

		return $modeInState;
	}

	public function addToBalance($amount) {
		if($amount > 0){
			$this->balance = $this->balance + $amount;

			return $this->update('balance');
		}

		return false;
	}

	public function deductBalance($amount) {
		if($amount > 0 && $this->balance >= $amount){
			$this->balance = $this->balance - $amount;

			return $this->update('balance');
		}

		return false;
	}

	public static function updateUserSession() {
		if (!Yii::app()->user->isGuest) {
			$id = Yii::app()->user->id;
			$sessionId = Yii::app()->session->sessionId;

			if ($id && $sessionId) {
				Yii::app()->db->createCommand()->update('{{users_sessions}}',array(
					'user_id'=>$id
				),'id=:sessionId',array(':sessionId'=>$sessionId));

				/*# comparison list
				if (issetModule('comparisonList')) {
					Yii::app()->db->createCommand()->update('{{comparison_list}}',array(
						'user_id'=>$id
					),'session_id=:sessionId',array(':sessionId'=>$sessionId));
				}*/
			}
		}
	}

	public static function destroyUserSession($userId = null) {
		if (Yii::app()->user->getState("isAdmin")) {
			if ($userId) {
				Yii::app()->db->createCommand()->delete('{{users_sessions}}','user_id=:userId',array(':userId'=>$userId));
			}
		}
	}


	public static function createUser($attributes, $isSocAuth = false) {
        $model = new User;
        $model->attributes = $attributes;

        $password = $model->randomString();
        $model->setPassword($password);

        if ($model->save()) {
            return array(
                'id' => $model->id,
                'email' => $model->email,
                'username' => $model->username,
                'password' => $password,
                'active' => $model->active,
                'activatekey' => $model->activatekey,
                'activateLink' => Yii::app()->createAbsoluteUrl('/site/activation?key=' . $model->activatekey),
                'userModel' => $model,
            );
        } else {
			if ($isSocAuth) {
				$errors = $model->getErrors();
				if ($errors) {
					foreach($errors as $error) {
						if ($error && is_array($error)) {
							foreach($error as $item) {
								echo '<div class="alert alert-block alert-error fade in">'.$item.'</div>';
							}
						}
					}
				}
				exit;
			}
			else
            return false;
        }
    }

    public static function generateActivateKey() {
        return md5(uniqid());
    }

    public static function getTypeList($variant = 'list'){
        $list = array(
            self::TYPE_PRIVATE_PERSON => tc('Private person'),
            self::TYPE_AGENCY => tc('Company'),
            self::TYPE_AGENT => tc('Agent'),
        );

        switch($variant){
            case 'key':
                return array_keys($list);
            break;

            case 'full':
                $list[self::TYPE_ADMIN] = tc('administrator');
                break;

            case 'withAll':
                $list['all'] = tc('All');
                break;

            default:
                return $list;
        }

        return $list;
    }

    public function getTypeName(){
        $list = self::getTypeList('full');
        return isset($list[$this->type]) ? $list[$this->type] : tc('not defined');
    }

    public function getUrl(){
        return '#';
    }

    public function renderAva($linkToProfile = true, $sizeClass = ''){
        echo '<div class="user-ava" id="user-ava-'.$this->id.'">';

        echo '<div class="user-ava-crop">';

        if($linkToProfile){
            echo '<a href="'.$this->getUrl().'">';
        }else{
            echo '<a href="'.$this->getAvaSrc().'" rel="prettyPhoto">';
        }

        $avaUrl = $this->ava ? $this->getAvaSrcThumb() : Yii::app()->baseUrl . '/images/ava-default.jpg';

        echo CHtml::image($avaUrl, $this->username, array('class' => 'message_ava '.$sizeClass));

        echo '</a>';
        echo '</div>';

        //echo '<div class="user-ava-username">'.$this->type == User::TYPE_COMPANY ? $this->agency_name : $this->username.'</div>';

        echo '</div>';
    }

    public function getAvaSrc() {
        $url = HUser::getUploadUrl($this, HUser::UPLOAD_AVA);

        return $url . '/' . $this->ava;
    }

    public function getAvaSrcThumb() {
        $url = HUser::getUploadUrl($this, HUser::UPLOAD_AVA);

        return $url . '/' . self::AVA_PREFIX . $this->ava;
    }

    public function getNameForType(){
        return $this->type == User::TYPE_AGENCY ? $this->agency_name : $this->username;
    }

    public function getLinkToAllListings(){
        return CHtml::link(tt('all_member_listings', 'apartments') . ' ('.$this->countAd.')', Yii::app()->createUrl('/apartments/main/alllistings', array('id' => $this->id)));
    }
}