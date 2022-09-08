<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Players;

/* @var $this yii\web\View */
/* @var $model app\models\Bases */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bases-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'x')->textInput() ?>

    <?= $form->field($model, 'z')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
