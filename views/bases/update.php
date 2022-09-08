<?php

use yii\helpers\Html;
use app\models\Players;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Bases */

$this->title = 'Update Bases: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bases-update">

    <h1><?= Html::encode($this->title) ?></h1>



<div class="bases-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'x')->textInput() ?>

    <?= $form->field($model, 'z')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <br>
    <span>players:</span>
    <br>
    <br>

    <?php
    foreach ($players as $value){
        echo $value->player->name;?>

        <a href="<?=Url::to(array('bases/deletepb', 'id' => $model->id, 'idlink' => $value->id))?>" title="Delete" aria-label="Delete" data-pjax="0" data-confirm="Are you sure you want to delete this item?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
        <br>
    <?php
    }

    ?>


    <br>
    <span>add new player:</span>
    <select name="Bases[newplayer]">
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
    <br>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>