<?php

use yii\helpers\Html;
use app\models\Players;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Direction */

$this->title = 'Update Direction: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Directions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="direction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="direction-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'x')->textInput() ?>

        <?= $form->field($model, 'z')->textInput() ?>

        <?= $form->field($model, 'fromdir')->textInput() ?>

        <?= $form->field($model, 'todir')->textInput() ?>

        <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>


        <br>
        <span>players:</span>
        <br>
        <br>

        <?php
        foreach ($players as $value){
            echo $value->player->name.'  '.$value->chance;?>

            <a href="<?=Url::to(array('direction/deletepd', 'id' => $model->id, 'idlink' => $value->id))?>" title="Delete" aria-label="Delete" data-pjax="0" data-confirm="Are you sure you want to delete this item?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
            <br>
            <?php
        }

        ?>



        <br>
        <span>add new player:</span>
        <select name="Direction[newplayer]">
            <option></option>

            <?php
            foreach (Players::getNamesArray() as $value){?>
                <option>
                    <?php
                    echo $value;
                    ?>
                </option>
                <?php
            }
            ?>

        </select>

        <input type="number" name="Direction[chance]">
        <br>








        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
