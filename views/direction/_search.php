<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DirectionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="direction-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'x') ?>

    <?= $form->field($model, 'z') ?>

    <?= $form->field($model, 'fromdir') ?>

    <?= $form->field($model, 'todir') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
