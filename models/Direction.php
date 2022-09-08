<?php

namespace app\models;

use Yii;
use app\models\Players;
use app\models\Playerdir;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property int $x
 * @property int $z
 * @property float $fromdir
 * @property float $todir
 * @property string|null $source
 * @property string|null $description
 * @property int $time
 *
 * @property Playerdir[] $playerdirs
 */
class Direction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $newplayer;
    public $chance;

    public static function tableName()
    {
        return 'direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['x', 'z', 'fromdir', 'todir'], 'required'],
            [['x', 'z', 'time', 'chance'], 'integer'],
            [['fromdir', 'todir'], 'number'],
            [['description'], 'string'],
            [['source'], 'string', 'max' => 45],
            [['newplayer'], 'exist', 'skipOnError' => true, 'targetClass' => Players::className(), 'targetAttribute' => 'name'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'x' => 'X',
            'z' => 'Z',
            'fromdir' => 'Fromdir',
            'todir' => 'Todir',
            'source' => 'Source',
            'description' => 'Description',
            'time' => 'Time',
        ];
    }

    /**
     * Gets query for [[Playerdirs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerdirs()
    {
        return $this->hasMany(Playerdir::className(), ['dirid' => 'id']);
    }

    public function getNewPlayerName(){
        return $this->newplayer;
    }


    public function getChance(){
        return $this->chance;

    }

    public function getPlayersString(){
        $str = '';
        $pbs = Playerdir::find()->where(['dirid' => $this->id])->all();
        //print_r($pbs); echo 'sss'; exit();
        if ($pbs != null){
            foreach ($pbs as $pb){// print_r(gettype($pb)); echo 'sss'; exit();
                if ($pb != null){

                    if ($pb instanceof Playerdir){
                        $str .= ' '.$pb->player->name;
                    }
                }

            }
        }

        return $str;
    }
}
