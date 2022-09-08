<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>

<div class="site-index">

    <?php $form = ActiveForm::begin(); ?>

    <div class="calc">
        <?= $form->field($model, 'x')->textInput() ?>
        <?= $form->field($model, 'z')->textInput() ?>

        <input type="submit" name="main[buttonname]" value="calcdirs"><br>
        <input type="submit" name="main[buttonname]" value="localmap"><br>
        <input type="submit" name="main[buttonname]" value="genmap"><br>
        <br>
        <?= $form->field($model, 'xstart')->textInput() ?>
        <?= $form->field($model, 'zstart')->textInput() ?>
        <?= $form->field($model, 'mapwidth')->textInput() ?>
        <br>

        <?php

        foreach ($model->dirs as $direction){

            echo $direction['name'].' : '.$direction['dir'].'<br>';

        }

        echo $model->sqerror;







        ?>

    </div>


    <div class="bases">

        <?php

        foreach ($model->bases as $base){
            echo ' .   '.$base->name; ?>

            <a href="<?=Url::to(array('bases/update', 'id' => $base->id))?>" title="edit" aria-label="Delete" ><span class="glyphicon glyphicon-cog"></span></a>
            <a href="" title="<?=$base->playersString?>" aria-label="Delete" ><span class="glyphicon glyphicon-user"></span></a>
            <?php

            echo '<br>';
            echo $base->x.'  :  '.$base->z;
            echo $form->field($model, "selbases[$base->id]")->checkbox();
            echo '____________________________<br>';
        }

        ?>

    </div>

    <div class="directions">


    <?php

    foreach ($model->directions as $dir){
        echo $dir->x.' : '.$dir->z.' | '.$dir->fromdir.' : '.$dir->todir; ?>

        <a href="<?=Url::to(array('direction/update', 'id' => $dir->id))?>" title="edit" aria-label="Delete" ><span class="glyphicon glyphicon-cog"></span></a>
        <a href="" title="<?=$dir->playersString?>" aria-label="Delete" ><span class="glyphicon glyphicon-user"></span></a>
        <?php

        echo '<br>';
        echo $form->field($model, "seldirs[$dir->id]")->checkbox();
        echo '____________________________<br>';
    }

    ?>
    </div>




    <?php ActiveForm::end(); ?>

    <div class="map">
        <img src="<?=$model->mapurl?>">
    </div>
</div>
