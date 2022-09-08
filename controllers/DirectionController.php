<?php

namespace app\controllers;

use Yii;
use app\models\Direction;
use app\models\DirectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Playerdir;
use app\models\Players;

/**
 * DirectionController implements the CRUD actions for Direction model.
 */
class DirectionController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Direction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DirectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Direction model.
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
     * Creates a new Direction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Direction();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->time = time();
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Direction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $players = $model->playerdirs;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {


            $newplayername = $model->getNewPlayerName();
            $newplayerchance = $model->getChance();
            $model->save();


            $NP = Players::find()->where(['name' => $newplayername])->one();
            if($NP != false){
                $nplink = new Playerdir;
                $nplink->dirid = $model->id;
                $nplink->playerid = $NP->id;
                $nplink->chance = $newplayerchance;
                $nplink->save();
                return $this->render('update', [
                    'model' => $model,
                    'players' => $players,
                ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'players' => $players,
        ]);
    }

    /**
     * Deletes an existing Direction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $pds = Playerdir::find()->where(['dirid' => $id])->all();
        //print_r($pds); exit();
        if ($pds != null){
            foreach ($pds as $pb){
                //echo $pb->id; exit();
                $pb->delete();
            }
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionDeletepd($id, $idlink){
        $model = Playerdir::findOne($idlink);
        if ($model !== null){
            $model->delete();
        }
        return $this->redirect(array('update', 'id' => $id));
    }

    /**
     * Finds the Direction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Direction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Direction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
