<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "playerdir".
 *
 * @property int $id
 * @property int $dirid
 * @property int $playerid
 * @property int $chance
 *
 * @property Players $player
 * @property Direction $dir
 */
class Playerdir extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'playerdir';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dirid', 'playerid', 'chance'], 'required'],
            [['dirid', 'playerid', 'chance'], 'integer'],
            [['playerid'], 'exist', 'skipOnError' => true, 'targetClass' => Players::className(), 'targetAttribute' => ['playerid' => 'id']],
            [['dirid'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['dirid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dirid' => 'Dirid',
            'playerid' => 'Playerid',
            'chance' => 'Chance',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Players::className(), ['id' => 'playerid']);
    }

    /**
     * Gets query for [[Dir]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDir()
    {
        return $this->hasOne(Direction::className(), ['id' => 'dirid']);
    }
}
