<?php

namespace app\models;

use Yii;
use app\models\Playerdir;

/**
 * This is the model class for table "players".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 *
 * @property Playerbases[] $playerbases
 * @property Playerdir[] $playerdirs
 */
class Players extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'players';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Playerbases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerbases()
    {
        return $this->hasMany(Playerbases::className(), ['playerid' => 'id']);
    }

    /**
     * Gets query for [[Playerdirs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerdirs()
    {
        return $this->hasMany(Playerdir::className(), ['playerid' => 'id']);
    }

    public static function getNamesArray(){
        $arr = self::find()->orderBy('name')->all();
        $ar = array();
        foreach ($arr as $value){
            $ar[] = $value->name;
        }
        return $ar;
    }



}
