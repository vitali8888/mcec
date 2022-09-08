<?php
/**
 * Created by PhpStorm.
 * User: vitali
 * Date: 20.01.21
 * Time: 21:26
 */

namespace app\models;


class square
{

    private $dirs;

    private $error;
    private $validAngle = 5;
    private $points;


    public function adddir($x, $z, $from, $to){
        $this->dirs[] = ['x' => $x, 'z' => $z, 'from' => $from, 'to' => $to];
    }

    public function getPoints(){
        $p = array();
        foreach ($this->points as $point){
            $p[] = ['x' => round($point['x']), 'z' => round($point['z'])];
        }
        return $p;
    }

    public function getdirs(){
        return $this->dirs;
    }

    public function shakedirs(){ //once need for test
        $arr[0] = $this->dirs[2];
        $arr[1]= $this->dirs[3];
        $arr[2]=$this->dirs[0];
        $arr[3]=$this->dirs[1];
        $this->dirs = $arr;
    }


    public function handle(){
        if (!$this->validate()){return false;}

        //get start square
        $this->makeRays();

        $rayf0 = $this->dirs[0]['rayf'];
        $rayt0 = $this->dirs[0]['rayt'];

        $rayf1 = $this->dirs[1]['rayf'];
        $rayt1 = $this->dirs[1]['rayt'];


        $pf0f1 = $this->crossRays($rayf0, $rayf1);
        $pf0t1 = $this->crossRays($rayf0, $rayt1);
        $pt0t1 = $this->crossRays($rayt0, $rayt1);
        $pt0f1 = $this->crossRays($rayt0, $rayf1);

        $counter = 0;
        //how many cross we have:

        foreach ([$pf0f1, $pf0t1, $pt0t1, $pt0f1] as $val){
            if (is_array($val)){
                $counter++;
            }
        }

        $this->points = null;

        if ($counter !== 4){

            //very rare
            // dir 0 point into dir1 and dir 1 point into dir0
            if ($this->indir($this->dirs[0], $this->dirs[1]) and $this->indir($this->dirs[1], $this->dirs[0])){
                if (is_array($pf0t1) and is_array($pt0f1)){
                    $this->points = [['x' => $this->dirs[0]['x'], 'z' => $this->dirs[0]['z']], $pf0t1, ['x' => $this->dirs[1]['x'], 'z' =>
                        $this->dirs[1]['z']], $pt0f1];
                }
            }

            //if start point direction 0 is into direction1
            if ($this->indir($this->dirs[0], $this->dirs[1]) and $this->points === null and $counter === 2){
                if (is_array($pf0t1) and is_array($pt0t1)){
                    $this->points = [$pf0t1, $pt0t1, ['x' => $this->dirs[0]['x'], 'z' => $this->dirs[0]['z']]];
                }

                if (is_array($pf0f1) and is_array($pt0f1)){
                    $this->points = [$pf0f1, $pt0f1, ['x' => $this->dirs[0]['x'], 'z' => $this->dirs[0]['z']]];
                }
            }

            //if start point direction 1 is into direction0
            if ($this->indir($this->dirs[1], $this->dirs[0]) and $this->points === null and $counter === 2){
                if (is_array($pf0f1) and is_array($pf0t1)){
                    $this->points = [$pf0f1, $pf0t1, ['x' => $this->dirs[1]['x'], 'z' => $this->dirs[1]['z']]];
                }

                if (is_array($pt0f1) and is_array($pt0t1)){
                    $this->points = [$pt0t1, $pt0f1, ['x' => $this->dirs[1]['x'], 'z' => $this->dirs[1]['z']]];
                }
            }

        }

        if ($counter === 4 and $this->points === null){
            $this->points = [$pt0t1, $pf0t1, $pf0f1, $pt0f1];
        }

        if ($this->points === null){
            $err = 'dir: '. $this->dir2str($this->dirs[0]).' and dir: '.$this->dir2str($this->dirs[1]).' doesnt have square';
            $err.=' crossairs: '.$counter;
            $this->addError($err);
            return false;
        }
        else{


            if (count($this->dirs) > 2){
                $this->cutSquare();
            }


            return true;


        }


    }

    private function cutSquare(){
        for ($i = 2; $i < count($this->dirs); $i++){
            $dir = $this->dirs[$i];

            $counter = 0;
            for ($j = 0; $j<$i; $j++){
                if ($this->indir($dir, $this->dirs[$j])){
                    $counter++;
                }
            }
            if ($counter === $j){  //means start point of current direction is into zone
                $crossF = $this->crossZoneRay($dir['rayf']);
                $crossT = $this->crossZoneRay($dir['rayt']);

                if ($crossF['counter'] !== 1){
                    $err = 'unexpected behavior: crossf, start point into cur dir, dir: ' . $this->dir2str($dir);
                    $this->addError($err);
                }

                if ($crossT['counter'] !== 1){
                    $err = 'unexpected behavior: crosst, start point into cur dir, dir: ' . $this->dir2str($dir);
                    $this->addError($err);
                }

                $this->addPoint($crossF['crosses'][0]['hm'], $crossF['crosses'][0]);
                $this->addPoint($crossT['crosses'][0]['hm'], $crossT['crosses'][0]);
                $this->addPoint($this->lfBreak($dir), $dir);
                $this->points = $this->rmDirOutPoints($dir);

            }
            else{

                if (count($this->points) === $this->calcInDirPoints($dir)){
                    continue; //means all points into this direction
                }

                $crossF = $this->crossZoneRay($dir['rayf']);
                $crossT = $this->crossZoneRay($dir['rayt']);

                $sum = $crossF['counter'] + $crossT['counter'];

                if ($this->calcInDirPoints($dir) === 0 and $sum === 0){
                    $this->addError('dir out of square: '.$this->dir2str($dir));
                    return false;
                }

                if ($this->calcInDirPoints($dir) > 0 and $sum === 0){
                    $this->addError('unexpected behavior, there are points in dir, but no crossairs: '.$this->dir2str($dir));
                    return false;
                }

                if ($crossF['counter'] !== 0){
                    if ($crossF['counter'] !== 2){
                        $this->addError('unexpected behavior, crossF has more than 0 crosses, but no 2, dir: '.$this->dir2str($dir));
                        return false;
                    }
                    $this->addPoint($crossF['crosses'][0]['hm'], $crossF['crosses'][0]);
                    $this->addPoint($crossF['crosses'][1]['hm'], $crossF['crosses'][1]);
                }


                if ($crossT['counter'] !== 0){
                    if ($crossT['counter'] !== 2){
                        $this->addError('unexpected behavior, crossT has more than 0 crosses, but no 2, dir: '.$this->dir2str($dir));
                        return false;
                    }
                    $this->addPoint($crossT['crosses'][0]['hm'], $crossT['crosses'][0]);
                    $this->addPoint($crossT['crosses'][1]['hm'], $crossT['crosses'][1]);
                }


                $this->points = $this->rmDirOutPoints($dir);


            }



            
        }
    }

    private function addPoint($hm, $point){
        $arr = array();
        for ($i = 0; $i<count($this->points); $i++){
            if ($i === $hm){
                $arr[$i] = $this->points[$i];
                $arr[$hm+1]['x'] = $point['x'];
                $arr[$hm+1]['z'] = $point['z'];

            }
            elseif ($i>$hm){
                $arr[$i+1] = $this->points[$i];
            }
            else{
                $arr[$i] = $this->points[$i];
            }
        }

        $this->points = $arr;
    }

    private function rmDirOutPoints($dir){

        $arr = array();

        foreach ($this->points as $point){
            if ($this->indir($point, $dir)){
                $arr[] = $point;
            }
        }

        return $arr;
    }

    private function calcInDirPoints($dir){
        $counter = 0;
        foreach ($this->points as $point){
            if ($this->indir($point, $dir)){
                $counter++;
            }
        }
        return $counter;
    }



    private function lfBreak($dir){

        for ($i = 0; $i<count($this->points); $i++){

            if (($this->indir($this->points[$i], $dir) and !$this->indir($this->points[$i+1], $dir)) or (!$this->indir($this->points[$i], $dir) and $this->indir($this->points[$i+1], $dir))){
                return $i;
            }
        }
    }


    private function crossZoneRay($ray){

        $counter = 0;
        $ans = array();

        for ($i = 0; $i < count($this->points); $i++){
            $hm = $i;
            $next = $i+1;

            if ($next === count($this->points)){
                $next = 0;

            }

            $crossing = $this->crossSegmentRay($this->points[$hm], $this->points[$next], $ray);
            if (is_array($crossing)){
                $counter++;
                $a['hm'] = $hm;
                $a['next'] = $next;
                $a['z'] = $crossing['z'];
                $a['x'] = $crossing['x'];
                $ans[] = $a;
            }
        }

        return ['crosses' => $ans, 'counter' => $counter];
    }

    private function crossSegmentRay($point1, $point2, $ray){

        $x1 = $point1['x'];
        $z1 = $point1['z'];

        $x2 = $point2['x'];
        $z2 = $point2['z'];

        $x3 = $ray['x'];
        $z3 = $ray['z'];

        $k = tan(deg2rad($ray['deg']));

        //need check if is paralel :(

         $z = ($z1*($x2 - $x1)/($z2 - $z1) - $k*$z3 - $x3 -$x1 )/(($x2 - $x1)/($z2 - $z1) + $k);
         $x = ($z + $z3) * (-$k) - $x3;
print_r($x);
echo '<br>';
print_r($z); exit();
        if (!$this->isRightSide($ray['deg'], ['x' => $x3, 'z' => $z3], ['x' => $x, 'z' => $z])){
            return false;
        }

        $minz = $z1;
        $maxz = $z2;

        if ($z2 < $z1){
            $minz = $z2;
            $maxz = $z1;
        }

        if ($z < $minz or $z > $maxz){
            return false;
        }

        return ['x' => $x, 'z' => $z];
    }

    private function crossRays($ray1, $ray2){

        if ($this->parallel($ray1['deg'], $ray2['deg'])){
            return false;
        }

        $tanA = tan(deg2rad($ray1['deg']));
        $tanB = tan(deg2rad($ray2['deg']));
        $x1 = $ray1['x'];
        $z1 = $ray1['z'];
        $x2 = $ray2['x'];
        $z2 = $ray2['z'];

        $z = ($x2 - $x1 - $tanA*$z1 + $tanB*$z2)/(-$tanA + $tanB);
        $x = - $tanA * $z + $tanA*$z1 + $x1;

        if (!$this->isRightSide($ray1['deg'], ['x' => $x1, 'z' => $z1], ['x' => $x, 'z' => $z])){
            return false;
        }

        if (!$this->isRightSide($ray2['deg'], ['x' => $x2, 'z' => $z2], ['x' => $x, 'z' => $z])){
            return false;
        }

        return ['x' => $x, 'z' => $z];
    }

    private function indir($point, $dir){

        if ($point['x'] === $dir['x'] and $point['z'] === $dir['z']){
            return true;
        }

        $x = $point['x'] - $dir['x'];
        $z = $point['z'] - $dir['z'];

        $deg = 361;

        if ($z === 0 and $x>0) {
            $deg = 90;
            }
        elseif ($z === 0 and $x<0){
            $deg = -90;
        }
        elseif ($z === 0 and $x === 0){
            return false;
        }

        if ($z !== 0){
            $deg = round(rad2deg(atan($x/$z)), 3);
            $deg = -$deg;

            if ($z > 0 and $x < 0) {
                $deg = $deg-180;
            }

            if ($z > 0 and $x > 0){
                $deg = $deg + 180;
            }
        }

        if ($deg >= $dir['from'] and $deg <= $dir['to']){
            return true;
        }

        if ($deg >= $dir['from'] +360  and $deg <= $dir['to'] + 360){
            return true;
        }

        return false;

    }

    private function parallel($deg, $deg1){
        $deg = floatval($deg);
        $deg1 = floatval($deg1);

        if ($deg === $deg1){return true;}

        if ($deg > 0){
            $deg = $deg - 180;
        }elseif ($deg < 0){
            $deg = $deg + 180;
        }

        if ($deg === $deg1){
            return true;
        }
        return false;
    }

    private function makeRays(){
        for ($i = 0; $i<count($this->dirs); $i++){
            $this->dirs[$i]['rayf'] = ['deg' => $this->dirs[$i]['from'], 'x' => $this->dirs[$i]['x'], 'z' => $this->dirs[$i]['z']];
            $this->dirs[$i]['rayt'] = ['deg' => $this->dirs[$i]['to'], 'x' => $this->dirs[$i]['x'], 'z' => $this->dirs[$i]['z']];
        }
    }


    private function isRightSide($deg, array $pointF, array $pointT){

        $x = $pointT['x'] - $pointF['x'];
        $z = $pointT['z'] - $pointF['z'];



        if ($z > 0){
            if ($deg > -90 and $deg < 90) {return false;}
        }

        if ($z < 0){
            if ($deg < -90 or $deg > 90){return false;}
        }

        if ($x > 0){
            if ($deg < 0 and $deg > -180) {return false;}
        }

        if ($x < 0){
            if ($deg > 0) {return false;}
        }

        return true;
    }

    private function validate(){

        if (!is_array($this->dirs)){
            $this->addError('choose at least 2 directions');
            return false;
        }

        if (count($this->dirs) < 2){
            $this->addError('need more than 1 direction');
            return false;
        }

        for ($i = 0; $i < count($this->dirs); $i++){
            if ($this->dirs[$i]['from'] === $this->dirs[$i]['to']){
                $err = 'direction must have 2 rays, sry (dir:'.$this->dir2str($this->dirs[$i]).')';
                $this->addError($err);
                return false;
            }

            if ($this->dirs[$i]['from'] > $this->dirs[$i]['to']){
                list($this->dirs[$i]['from'],$this->dirs[$i]['to'])=[$this->dirs[$i]['to'],$this->dirs[$i]['from']];

            }

            if (($this->dirs[$i]['from']-$this->validAngle < -180) and ($this->dirs[$i]['to'] + $this->validAngle > 180)){
                $this->dirs[$i]['to'] = $this->dirs[$i]['to'] - 360;
                list($this->dirs[$i]['from'],$this->dirs[$i]['to'])=[$this->dirs[$i]['to'],$this->dirs[$i]['from']];
            }

            if ($this->dirs[$i]['to'] - $this->dirs[$i]['from'] > $this->validAngle){
                $err = 'max angle between rays is: '.$this->validAngle.' , dir: ' . $this->dir2str($this->dirs[$i]);
                $this->addError($err);
                return false;
            }

            if ($this->dirs[$i]['from'] < -180 - $this->validAngle or $this->dirs[$i]['to'] > 180){
                $err = 'direction must be from -180 to 180 in dir: '. $this->dir2str($this->dirs[$i]);
                $this->addError($err);
                return false;
            }
        }

        return true;



    }

    public function hasError(){
        if ($this->error !== null){
            return true;
        }
        return false;
    }

    public function errorMessage(){
        return $this->error;
    }

    private function dir2str(array $dir){
        return $dir['x'].':'.$dir['z'].'|'.$dir['from'].':'.$dir['to'];
    }

    private function addError($err){
        $this->error = $err;
    }

    public function getminx(){

        if ($this->points === null){
            return (-10000000);
        }
        $minx = 9999999;
        foreach ($this->points as $point){
            if ($point['x'] < $minx){
                $minx = $point['x'];
            }

        }
        return $minx;
    }


    public function getminz(){
        if ($this->points === null){
            return (-10000000);
        }
        $minz = 9999999;
        foreach ($this->points as $point){
            if ($point['z'] < $minz){
                $minz = $point['z'];
            }

        }
        return $minz;
    }

    private function getmaxx(){
        if ($this->points === null){
            return (10000000);
        }
        $maxx = -9999999;
        foreach ($this->points as $point){
            if ($point['x'] > $maxx){
                $maxx = $point['x'];
            }

        }
        return $maxx;
    }

    private function getmaxz(){
        if ($this->points === null){
            return (10000000);
        }
        $maxz = -9999999;
        foreach ($this->points as $point){
            if ($point['z'] > $maxz){
                $maxz = $point['z'];
            }

        }
        return $maxz;
    }

    public function getdist(){
        if ($this->points === null){
            return (20000000);
        }

        $distx = $this->getmaxx() - $this->getminx();
        $distz = $this->getmaxz() - $this->getminz();

        if ($distx > $distz){
            return $distx;
        }
        else{
            return $distz;
        }

    }

}