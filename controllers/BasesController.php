<?php

namespace app\controllers;

use app\models\Playerbases;
use Yii;
use yii\filters\AccessControl;
use app\models\Bases;
use app\models\BasesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Players;

/**
 * BaseController implements the CRUD actions for Bases model.
 */
class BasesController extends Controller
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
     * Lists all Bases models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BasesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bases model.
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
     * Creates a new Bases model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bases();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bases model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $players = $model->playerbases;


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $newplayername = $model->getNewPlayerName();
            $model->save();
            $NP = Players::find()->where(['name' => $newplayername])->one();
            if($NP != false){
                $nplink = new Playerbases;
                $nplink->baseid = $model->id;
                $nplink->playerid = $NP->id;
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
     * Deletes an existing Bases model.
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

    public function actionDeletepb($id, $idlink){
        $model = Playerbases::findOne($idlink);
        if ($model !== null){
            $model->delete();
        }
        return $this->redirect(array('update', 'id' => $id));
    }

    /**
     * Finds the Bases model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bases the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bases::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
