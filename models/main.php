<?php
/**
 * Created by PhpStorm.
 * User: vitali
 * Date: 15.01.21
 * Time: 4:01
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Direction;
use app\models\Bases;
use app\models\director;
use app\models\genmap;
use app\models\square;

class main extends Model
{
    public $x;
    public $z;
    public $seldirs;
    public $selbases;
    public $buttonname;
    public $dirs;
    public $mapurl;
    public $sqerror;

    public $xstart = -10000000;
    public $zstart = -10000000;
    public $mapwidth = 20000000;

    public function rules()
    {
        return [
            [['x', 'z', 'xstart', 'zstart', 'mapwidth'], 'integer'],
            ['selbases', 'each', 'rule' => ['boolean']],
            ['seldirs', 'each', 'rule' => ['boolean']],
            [['buttonname'], 'string'],
        ];
    }


    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $session = Yii::$app->session;
        $this->selbases = $session['selbases'];
        $this->seldirs = $session['seldirs'];
        $this->zstart = isset($session['zstart']) ? $session['zstart'] : -10000000  ;
        $this->xstart = isset($session['xstart']) ? $session['xstart'] : -10000000  ;
        $this->mapwidth = isset($session['mapwidth']) ? $session['mapwidth'] : 20000000;
        $this->dirs = array();

    }

    public function getDirections(){
        $dirs = Direction::find()->all();
        return $dirs;
    }

    public function getBases(){
        return Bases::find()->all();
    }


    public function handle(){
        $session = Yii::$app->session;
        $session['selbases'] = $this->selbases;
        $session['seldirs'] = $this->seldirs;
        $session['zstart'] = $this->zstart;
        $session['xstart'] = $this->xstart;
        $session['mapwidth'] = $this->mapwidth;

        $this->dirs = array();

        if ($this->buttonname === 'calcdirs'){
            $director = new director;
            $director->setcoords($this->x, $this->z);

            foreach ($this->selbases as $id => $value){
                if ($value == true){
                    $base = Bases::findOne($id);
                    $director->setpoint($base->x, $base->z, $base->name);
                }
            }

            $this->dirs = $director->getResult();

        }
        elseif($this->buttonname === 'genmap'){
            $map = new genmap;
            $map->setxstart($this->xstart);
            $map->setzstart($this->zstart);
            $map->setdist($this->mapwidth);


            foreach ($this->selbases as $id => $value){
                if ($value == true){
                    $base = Bases::findOne($id);
                    $map->setpoints($base->x, $base->z, $base->name);
                }
            }

            foreach ($this->seldirs as $id => $value){
                if ($value == true){
                    $dir = Direction::findOne($id);
                    $map->setdirections($dir->x, $dir->z, $dir->fromdir, $dir->todir);
                }
            }


            $this->mapurl = $map->getmapurl();

        }
        elseif($this->buttonname === 'localmap'){

            $square = new square;


            foreach ($this->seldirs as $id => $value){
                if ($value == true){
                    $dir = Direction::findOne($id);
                    $square->adddir($dir->x, $dir->z, $dir->fromdir, $dir->todir);
                }
            }
            //$square->shakedirs();

            if ($square->handle()){
                $map = new genmap;


                $map->setdirs($square->getdirs());


                $map->addsquare($square);
                $this->mapurl = $map->getmapurl();
                $this->sqerror = $square->errorMessage();
            }
            else{
                $this->sqerror = $square->errorMessage();
            }

        }

        return true;
    }

}