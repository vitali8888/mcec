<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Direction;

/**
 * DirectionSearch represents the model behind the search form of `app\models\Direction`.
 */
class DirectionSearch extends Direction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'x', 'z', 'time'], 'integer'],
            [['fromdir', 'todir'], 'number'],
            [['source', 'description'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Direction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'x' => $this->x,
            'z' => $this->z,
            'fromdir' => $this->fromdir,
            'todir' => $this->todir,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
