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
use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Registration;
use app\models\Settings;
use app\models\User;
use yii\base\Event;

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
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/about');
        }

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

    public function actionRegister()
    {
        $model = new Registration();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = new User();
                $user->username = $model->username;
                $user->email = $model->email;
                $user->phone = $model->phone;
                $user->torolt = 1;
                $user->savePassword($model->password);
                Yii::$app->mailer->compose()
                    ->setFrom('gabor@dikan.hu')
                    ->setTo($user->email)
                    ->setSubject('Kess fiók aktiváció')
                    ->setHtmlBody('Az alábbi linkre kattintva tudod aktiválni a fiókodat: <a href="https://kess.dix.hu/site/activation?user='.$user->username.'&token='.$user->accessToken.'">Akitválom</a>')
                    ->send();
                $this->redirect("/site/registered");
            }
        }

        return $this->render('registration', [
            'model' => $model,
        ]);
    }

    public function actionRegistered()
    {
        return $this->render('message', [
            'msg' => 'Hamarosan felvesszük veled a kapcsolatot a megadott elérhetőségek valamelyik a fiók aktiválásához.'
        ]);
    }

    public function actionActivation($user, $token)
    {
        $user = User::findOne(["username" => $user]);
        if ($user && $user->accessToken == $token) {
            if ($user->torolt == 0) {
                $msg = "Ez a fiók már aktiválva van";
            } else {
                $user->torolt = 0;
                $user->save();

                $penztarca = new Penztarca(
                    [
                        'nev' => 'Pénztárca',
                        'felhasznalo' => $user->id,
                    ]
                );
                $penztarca->save();

                Yii::$app->db->createCommand('insert into kategoriak 
                    (tipus,fokategoria,nev,technikai,felhasznalo) 
                    (select 
                        tipus,fokategoria,nev,technikai,'.$user->id.' from kategoriak 
                        where felhasznalo=1 
                            and torolt=0)')->execute();  

                $msg = "Sikeresen aktiváltad a fiókodat, most már bejelentkezhetsz";
            }
        } else {
            $msg = "Az aktiváló link nem érvényes";
        }
        return $this->render('message', [
            'msg' => $msg,
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

    public function actionThanks()
    {
        return $this->render('message', [
            'msg' => 'Köszönöm a kávét! ☕',
        ]);
    }

    public function actionRecordkess($penztarca_id = null, $tipus = 'Kiadás', $update_id = null)
    {
        $model = new Mozgas();

        if ($update_id) {
            $model = Mozgas::findOne(["id" => $update_id, "felhasznalo" => Yii::$app->user->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->torolt = 0;
            $model->save();
            $model->id = 0;
            $model->osszeg=null;
            $model->kategoria_id=null; 
            if ($update_id) {
                return $this->redirect("/site/listkess?penztarca_id=".$model->penztarca_id);
            }
        } 

        if ($penztarca_id) {
            $model->penztarca_id = $penztarca_id;
        }
        if ($tipus) {
            $model->tipus = $tipus;
        }
        return $this->render('recordkess',[
            'model' => $model,
            'penztarca_id' => $penztarca_id,
            'tipus' => $tipus,
        ]);
    }

    public function actionListkess($penztarca_id = null, $delete_id = null)
    {
        $penztarca_id = $penztarca_id;

        if ($delete_id) {
            $model = Mozgas::findOne(['id' => $delete_id, 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
        }

        return $this->render('listkess',[
            'penztarca_id' => $penztarca_id,
        ]);
    }

    public function actionPlan($delete_id = null, $update_id = null)
    {
        if ($delete_id) {
            $model = Terv::findOne(['id' => $delete_id, 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
            return $this->redirect("/site/plan");
        }

        $model = new Terv();

        if ($update_id) {
            $model = Terv::findOne(['id' => $update_id, 'felhasznalo' => Yii::$app->user->id]);
        }

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

    public function actionCategories($tipus = 'Kiadás', $delete_id = null, $update_id = null)
    {
        if ($delete_id) {
            $model = Kategoriak::findOne(['id' => $delete_id, 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
            return $this->redirect("/site/categories?tipus=".$tipus);
        }

        $model = new Kategoriak();

        if ($update_id) {
            $model = Kategoriak::findOne(['id' => $update_id, 'felhasznalo' => Yii::$app->user->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->torolt = 0;
            $model->save();
            return $this->redirect("/site/categories?tipus=".$model->tipus);
        }

        $model->tipus = $tipus;

        return $this->render('categories',[
            'model' => $model,
            'tipus' => $tipus,
        ]);
    }

    public function actionWallets($delete_id = null, $update_id = null)
    {
        if ($delete_id) {
            $model = Penztarca::findOne(['id' => $delete_id, 'felhasznalo' => Yii::$app->user->id]);
            $model->torolt = 1;
            $model->save(false);
            return $this->redirect("/site/wallets");
        }

        $model = new Penztarca();

        if ($update_id) {
            $model = Penztarca::findOne(['id' => $update_id, 'felhasznalo' => Yii::$app->user->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->torolt = 0;
            $model->save();
            return $this->redirect("/site/wallets");
        }

        return $this->render('wallets',[
            'model' => $model,
        ]);
    }

    public function actionSettings()
    {
        $setting = new Settings();
        $user = User::findOne(["id" => Yii::$app->user->id]);

        if ($setting->load(Yii::$app->request->post())) {
            if ($setting->email) {
                $user->email = $setting->email;
                $user->phone = $setting->phone; 
                $user->save();
                return $this->redirect("/site/settings");
            }
            if ($setting->oldpassword || $setting->newpassword) {
                if($setting->validate()) {
                    $user->savePassword($setting->newpassword);
                    return $this->redirect("/site/settings");
                }
            }

        }

        $setting->email = $user->email;
        $setting->phone = $user->phone;
        return $this->render('settings',[
            'model' => $setting,
        ]);
    }
}
