<?php

namespace frontend\modules\dashboard\controllers;

use yii\web\Controller;
use Yii;

/**
 * Default controller for the `login` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'dashboard-app';
        Yii::$app->cache->flush(); //cache flush
        //Yii::$app->frontendCache->flush(); //frontend flush
        return $this->render('index');
    }
}
