<?php

namespace app\controllers;

use app\models\Abb;
use app\models\ContactForm;
use app\models\ToUpdate;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search' => ['post'],
                    'add-new' => ['post'],
                    'get-item' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSearch()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $q = \Yii::$app->request->post('q', '');

        if (empty($q)) return [];

        $page = \Yii::$app->request->post('page', 0);
        $page = preg_replace('/[^\d]+/si', '', $page);

        $strict = \Yii::$app->request->post('strict', '');
        $strict = ($strict == 'true') ? true : false;

        $abbronly = \Yii::$app->request->post('abbronly', '');
        $abbronly = ($abbronly == 'true') ? true : false;

        $abb = new Abb();

        $result = $abb->doSearch($q, ['strict' => $strict, 'abbronly' => $abbronly], 20, $page);
        return $result;
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAddNew()
    {
        if (!\Yii::$app->request->isPost) {
            return $this->goHome();
        }

        $model = new Abb();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Your abbreviature was added. After moderation it will be shown.'));
            return $this->goHome();
        } else {
            return $this->goHome();
        }
    }

    public function actionGetItem()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = \Yii::$app->request->post('id', '');

        if (empty($id)) return [];

        $item = Abb::findOne($id);

        return $item;
    }

    public function actionUpdate()
    {
        $id = \Yii::$app->request->post('id', '');
        $abb = new ToUpdate();
        $data = \Yii::$app->request->post('Abb');
        $abb->abb_id = $id;
        $abb->abbr = $data['abbr'];
        $abb->decode = $data['decode'];
        $abb->description = $data['description'];
        if ($abb->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Your update was added. After moderation it will be shown.'));
            return $this->goHome();
        }

        \Yii::$app->session->setFlash('warning', \Yii::t('app', 'Something wrong. Pleasy contact with administrator'));
        return $this->goHome();
    }
}
