<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *    version                :    1.8.1
 *    copyright            :    (c) 2014 Monoray
 *    website                :    http://www.monoray.ru/
 *    contact us            :    http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

class BeginRequest {

	const TIME_UPDATE = 86400;

	public static function updateStatusAd() {
		if (Yii::app()->request->getIsAjaxRequest() || !issetModule('paidservices')) {
			return false;
		}

		if (!oreInstall::isInstalled()) {
			return false;
		}

		$data = Yii::app()->statePersister->load();

		// Обновляем статусы 1 раз в сутки
		if (isset($data['next_check_status'])) {
			if ($data['next_check_status'] < time()) {
				$data['next_check_status'] = time() + self::TIME_UPDATE;
				Yii::app()->statePersister->save($data);

				self::checkStatusAd();
				self::clearApartmentsStats();

				// обновляем курсы валют
				Currency::model()->parseCbr();
			}
		} else {
			$data['next_check_status'] = time() + self::TIME_UPDATE;
			Yii::app()->statePersister->save($data);

			self::checkStatusAd();
			self::clearApartmentsStats();
		}
	}

	public static function checkStatusAd() {
		$activePaids = ApartmentPaid::model()->findAll('date_end <= NOW() AND status=' . ApartmentPaid::STATUS_ACTIVE);

		foreach ($activePaids as $paid) {
			$paid->status = ApartmentPaid::STATUS_NO_ACTIVE;

			if ($paid->paid_id == PaidServices::ID_SPECIAL_OFFER || $paid->paid_id == PaidServices::ID_UP_IN_SEARCH) {
				$apartment = Apartment::model()->findByPk($paid->apartment_id);

				if ($apartment) {
					$apartment->scenario = 'update_status';

					if ($paid->paid_id == PaidServices::ID_SPECIAL_OFFER) {
						$apartment->is_special_offer = 0;
						$apartment->update(array('is_special_offer'));
					}

					if ($paid->paid_id == PaidServices::ID_UP_IN_SEARCH) {
						$apartment->date_up_search = new CDbExpression('NULL');
						$apartment->update(array('date_up_search'));
					}
				}
			}

			if (!$paid->update(array('status'))) {
				//deb($paid->getErrors());
			}
		}

		$adEndActivity = Apartment::model()->with('user')->findAll('t.date_end_activity <= NOW() AND t.activity_always != 1 AND (t.active=:status OR t.owner_active=:status)', array(':status' => Apartment::STATUS_ACTIVE));
		foreach($adEndActivity as $ad){
			$ad->scenario = 'update_status';
			if(isset($ad->user) && $ad->user->isAdmin == 1){
				$ad->active = Apartment::STATUS_INACTIVE;
			} else {
				$ad->active = Apartment::STATUS_INACTIVE;
				$ad->owner_active = Apartment::STATUS_INACTIVE;
			}
			$ad->save(false);
		}
	}

	public static function clearApartmentsStats(){
		$sql = 'DELETE FROM {{apartment_statistics}} WHERE date_created < (NOW() - INTERVAL 2 DAY)';
		Yii::app()->db->createCommand($sql)->execute();
	}
}