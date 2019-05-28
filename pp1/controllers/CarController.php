<?php

namespace app\controllers;

use app\models\Receipt;
use app\models\ReceiptSearch;
use app\models\Registration;
use Yii;
use app\models\Car;
use app\models\CarSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use  yii\web\Session;
use yii\web\UploadedFile;

/**
 * CarController implements the CRUD actions for Car model.
 */
class CarController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Car models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new CarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Car model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Car model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Car();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Car model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->carImgUrl = UploadedFile::getInstance($model, 'carImgUrl');
            $img_name = $model->carName . '.' . $model->carImgUrl->extension;
            $img_path = 'img/' . $img_name;
            $model->carImgUrl->saveAs($img_path);
            $model->carImgUrl = $img_name;
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Car model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Car model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Car the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Car::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Booking car function
     * It has a pending time.
     */
    public function actionBooking()
    {
        $model = new Car();
        $email = Yii::$app->session->get('email');

        if (isset($_SESSION['email'])) {
            $checkId = Registration::find()->select(['carId'])->where('email=:email', [':email' => $email])->One();

            if ($checkId->attributes["carId"] != null) {
                $session = Yii::$app->session;
                $session->open();
                $session->set('return', $checkId->attributes["carId"]);
                return $this->render('returnCar');
            }
        }

        if (isset($_POST['booking2']) && isset($_SESSION['email'])) {
            $_SESSION['carID'] = $_POST['booking2'];
            $model->updateBookingStatus();
            return $this->redirect(['car/car-confirmed']);
        } elseif (isset($_POST['booking2'])) {
            Yii::$app->session->setFlash('error', "Please Login First!!");
            return $this->redirect('index.php?r=registration/signin');
        } else {
            return $this->render('booking');
        }
    }

    //if user clicks of confirm button, pending becomes to off, inUse becomes to confirmed
    public function actionConfirmStatus()
    {
        $model = new Car();
        $receipt = new Receipt();
        $user = new Registration();
        $email = Yii::$app->session->get('email');
        if (isset($_POST['payed']) && isset($_SESSION['carID']) && $_POST['payed'] == 'true')
            $model::updateAll(['pendingTime' => 'off', 'inUse' => 'confirmed'], ['id' => $_SESSION['carID']]);
        //update database carid field to the corresponding carId
        $user::updateAll(['carId' => $_SESSION['carID']], ['email' => $email]);
        $receipt->addRecord();
        unset($_SESSION['carID']);
        return $this->redirect(['car/booking']);
    }

    /**If pending time over and user doesn't click of confirm button
     * Pending becomes off, Inuse becomes available
     */
    public function actionTimePassed()
    {
        $model = new Car();

        $model::updateAll(['pendingTime' => 'off', 'inUse' => 'available'], ['id' => $_SESSION['carID']]);
    }

    public function actionTimePassed2()
    {
        $model = new Car();
        $model::updateAll(['pendingTime' => 'off', 'inUse' => 'available'], ['id' => 2]);
    }

    public function actionBookingStatus()
    {
        $model = new Car();
        $model->updateBookingStatus();
    }

    public function actionCarConfirmed()
    {
        return $this->render('car-confirmed');
    }

    public function actionReturnCar()
    {
        $user = new Registration();
        $model = new Car();
        $return = Yii::$app->session->get('return');
        $model::updateAll(['pendingTime' => 'off', 'inUse' => 'available'], ['id' => $return]);
        $user::updateAll(['carId' => ""], ['email' => Yii::$app->session->get('email')]);
        Yii::$app->session->remove("return");
        return $this->redirect(['car/booking']);
    }

    public function actionModifyCar()
    {

        $searchModel = new CarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('modify-car', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCancelCar()
    {
        $model = new Car();

        if(isset($_POST["cancel"])){
            $model::updateAll(['pendingTime' => 'off', 'inUse' => 'available'], ['id' => $_SESSION['carID']]);
            return $this->redirect(['car/booking']);
        }else{
            return $this->render('car-confirmed');
        }
    }
    public function actionGetCars()
    {
        $cars = Car::find()->orderBy('id')->all();
        return $this->asJson($cars);
    }


    public function actionRentHistory()
    {
        $searchModel = new ReceiptSearch();
        $account = Yii::$app->session->get('email');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $searchModel->email = $account);
        return $this->render('rent-history', [
            'dataProvider' => $dataProvider,
        ]);

    }
}
