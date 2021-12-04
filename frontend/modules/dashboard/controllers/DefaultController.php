<?php

namespace frontend\modules\dashboard\controllers;

use yii\web\Controller;

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
        return $this->render('index');
    }
}
