<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Mozgas;
use app\models\Terv;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
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

    public function actionRecordkess()
    {
        $model = new Mozgas();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->id = 0;
            $model->osszeg=null;
            $model->kategoria_id=null; 
        } elseif (null != Yii::$app->request->get('penztarca_id')) {
            $model->penztarca_id = Yii::$app->request->get('penztarca_id');
        }
        return $this->render('recordkess',[
            'model' => $model,
        ]);
    }

    public function actionListkess()
    {
        $penztarca_id = Yii::$app->request->get('penztarca_id');

        if (null != Yii::$app->request->get('delete_id')) {
            $model = Mozgas::findOne(['id' => Yii::$app->request->get('delete_id'), 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
        }

        return $this->render('listkess',[
            'penztarca_id' => $penztarca_id,
        ]);
    }

    public function actionPlan()
    {
        if (null != Yii::$app->request->get('delete_id')) {
            $model = Terv::findOne(['id' => Yii::$app->request->get('delete_id'), 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
            return $this->redirect("index.php?r=site%2Fplan");
        }

        $model = new Terv();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->id = 0;
            $model->osszeg=null;
            $model->kategoria_id=null; 
        }

        return $this->render('plan',[
            'model' => $model,
        ]);
    }
}
