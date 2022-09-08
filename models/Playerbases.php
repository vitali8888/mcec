<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "playerbases".
 *
 * @property int $id
 * @property int $playerid
 * @property int $baseid
 *
 * @property Bases $base
 * @property Players $player
 */
class Playerbases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */


    public static function tableName()
    {
        return 'playerbases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['playerid', 'baseid'], 'required'],
            [['playerid', 'baseid'], 'integer'],
            [['baseid'], 'exist', 'skipOnError' => true, 'targetClass' => Bases::className(), 'targetAttribute' => ['baseid' => 'id']],
            [['playerid'], 'exist', 'skipOnError' => true, 'targetClass' => Players::className(), 'targetAttribute' => ['playerid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'playerid' => 'Playerid',
            'baseid' => 'Baseid',
        ];
    }

    /**
     * Gets query for [[Base]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBase()
    {
        return $this->hasOne(Bases::className(), ['id' => 'baseid']);
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
}
