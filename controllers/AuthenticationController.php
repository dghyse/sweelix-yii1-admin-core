<?php
/**
 * File AuthenticationController.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.base.controllers
 */

namespace sweelix\yii1\admin\base\controllers;
use sweelix\yii1\admin\base\web\Controller;
use sweelix\yii1\ext\entities\Author;

/**
 * Class AuthenticationController
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.base.controllers
 */
class AuthenticationController extends Controller {

	/**
	 * Default action.
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionIndex() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.base.controllers');
			$this->redirect(array('authentication/login'));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.base.controllers');
			throw $e;
		}

	}

	/**
	 * Perform login logic
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionLogin() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.base.controllers');
			$this->layout = 'login';
			$author = new Author('authenticate');
			if(isset($_POST[\CHtml::modelName($author)]) === true) {
				$author->attributes = $_POST[\CHtml::modelName($author)];
				if($author->validate() === true) {
					$webUser = \Yii::app()->user;
					if(($webUser->allowAutoLogin === true) && (\CPropertyValue::ensureBoolean($author->authorAutoLogin) === true)) {
						$webUser->login($author->identity, $this->getModule()->sessionLifeTime);
					} else {
						$webUser->login($author->identity);
					}
					$this->redirect(\Yii::app()->user->returnUrl);
				}
				$author->authorPassword = '';
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === true) {
				$this->renderPartial('_login', array('author'=>$author));
			} else {
				$this->render('login', array('author'=>$author));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.base.controllers');
			throw $e;
		}
	}

	/**
	 * Perform logout logic
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionLogout() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.base.controllers');
			\Yii::app()->user->logout();
			\Yii::app()->user->setReturnUrl(null);
			$this->redirect(array('default/'));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.base.controllers');
			throw $e;
		}
	}

	/**
	 * Define filtering rules
	 *
	 * @return array
	 */
	public function filters() {
		return array('accessControl');
	}

	/**
	 * Define access rules / rbac stuff
	 *
	 * @return array
	 */
	public function accessRules() {
		return array(
			array(
				'deny',
				'actions'=>array('login'),
				'users'=>array('@'),
			),
			array(
				'allow',
				'actions'=>array('login'),
				'users'=>array('?'),
			),
		);
	}
}