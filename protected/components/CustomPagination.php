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

class CustomPagination extends CPagination {
	public function createPageUrl($controller,$page)         {
			$params=$this->params===null ? $_GET : $this->params;
	//      if($page>0) // page 0 is the default
					$params[$this->pageVar]=$page+1;
	//      else
	//              unset($params[$this->pageVar]);
			return $controller->createUrl($this->route,$params);
	}
}