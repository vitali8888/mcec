<?php
/**
 * Created by PhpStorm.
 * User: vitali
 * Date: 15.01.21
 * Time: 16:07
 */

namespace app\models;


class director
{
    private $x;
    private $z;
    private $topoints;
    private $result;

    public function setcoords($x, $z){
        $this->x = $x;
        $this->z = $z;
    }

    public function setpoint($x, $z, $name){
        $this->topoints[] = ['x' => $x, 'z' => $z, 'name' => $name];
    }

    private function getdir(){

    }

    public function getResult(){

        foreach($this->topoints as $value){

            $x = - $this->x + $value['x'];
            $z = - $this->z + $value['z'];

            if ($z === 0) {$z = 0.000001;} //kek


            $at = round(rad2deg(atan($x/$z)), 3);
            $at = -$at;

            if ($z > 0 and $x < 0) {
                $at = $at-180;
            }

            if ($z > 0 and $x > 0){
                $at = $at + 180;
            }


            $this->result[] = ['name' => $value['name'], 'dir' => $at];

        }

        return $this->result;
    }

}