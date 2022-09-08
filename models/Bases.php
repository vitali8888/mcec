<?php

namespace app\models;

use Yii;
use app\models\Players;

/**
 * This is the model class for table "bases".
 *
 * @property int $id
 * @property int $x
 * @property int $z
 * @property string|null $description
 * @property string $name
 *
 * @property Playerbases[] $playerbases
 */
class Bases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $newplayer;

    public static function tableName()
    {
        return 'bases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['x', 'z', 'name'], 'required'],
            [['x', 'z'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 45],
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
            'description' => 'Description',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Playerbases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerbases()
    {
        return $this->hasMany(Playerbases::className(), ['baseid' => 'id']);
    }

    public function getPlayersString(){
        $str = '';
        $pbs = Playerbases::find()->where(['baseid' => $this->id])->all();

        if ($pbs != null){
            foreach ($pbs as $pb){
                if ($pb != null){

                if ($pb instanceof Playerbases){
                    $str .= ' '.$pb->player->name;
                }
                }

            }
        }

        return $str;
    }

    public function getNewPlayerName(){
        return $this->newplayer;
    }
}
