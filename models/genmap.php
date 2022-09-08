<?php
/**
 * Created by PhpStorm.
 * User: vitali
 * Date: 16.01.21
 * Time: 14:59
 */

namespace app\models;

use yii\helpers\Url;
use app\models\square;


class genmap
{

    private $bases;
    private $directions;
    private $mapurl;

    private $xstart = -10000000;
    private $zstart = -10000000;
    private $dist = 20000000;
    private $scale = 20000;

    private $gridsteps = [100, 1000, 10000, 100000, 1000000, 10000000];

    private $resolution = 980;

    private $square;


    public function setpoints($x, $z, $name){
        $this->bases[] = ['x' => $x, 'z' => $z, 'name' => $name];
    }

    public function setdirections($x, $z, $from, $to){
        $this->directions[]=['x' => $x, 'z' => $z, 'from' => $from, 'to' => $to];
    }

    public function setdirs($dirs){
        $this->directions=$dirs;
    }

    public function setxstart($xstart){
        $this->xstart = (int) $xstart;
    }

    public function setzstart($zstart){
        $this->zstart = (int) $zstart;
    }

    public function setdist($dist){
        $this->dist = (int) $dist;
    }

    public function addsquare($ps){
        $this->square = $ps;
    }


    private function generate(){
        $map = imagecreatetruecolor($this->resolution, $this->resolution);

        $white = imagecolorallocate($map, 255, 255, 255);
        $lightgray = imagecolorallocate($map, 230, 230, 230);
        $darkgray = imagecolorallocate($map, 100, 100, 100);
        $red = imagecolorallocate($map, 255, 0, 0);
        $black = imagecolorallocate($map, 0, 0, 0);


        imagefill($map, 0, 0, $white);





        if ($this->square instanceof square){

            //need get map size
            $this->xstart = $this->square->getminx()-$this->square->getdist()/5;
            $this->zstart = $this->square->getminz()-$this->square->getdist()/5;
            $this->dist = $this->square->getdist()*1.4;

            $this->scale = $this->dist/($this->resolution-1);
            $this->drawgrid($map, $lightgray, $darkgray);

            $this->bases = $this->square->getPoints();
            $this->drawConnections($map, $red);
            $this->drawBases($map, $red, $black);
            //$this->drawdirbyid($map, 0);
            //$this->drawdirbyid($map, 1);
            //$this->drawdirbyid($map, 2);
            //$this->drawdirbyid($map, 3);
        }
        else {

            $this->scale = $this->dist/($this->resolution-1);
            $this->drawgrid($map, $lightgray, $darkgray);
            $this->drawdir($map);
            $this->drawBases($map, $red, $black);
        }


        $save = imagejpeg($map, 'map1.jpg', 90);
        $this->mapurl = 'map1.jpg';

    }

    /*
    public function localmap(){

        $map = imagecreatetruecolor($this->resolution, $this->resolution);
        $white = imagecolorallocate($map, 255, 255, 255);
        imagefill($map, 0, 0, $white);



        $save = imagejpeg($map, 'map2.jpg', 90);
        $this->mapurl = 'map2.jpg';
        return $this->mapurl;
    }
    */

    private function drawConnections($map, $color){
        for ($i = 0; $i<count($this->bases); $i++){
            $hm = $i;
            $next = $i+1;

            if ($next === count($this->bases)){
                $next = 0;
            }

            $x = $this->x2img($this->bases[$hm]['x']);
            $z = $this->z2img($this->bases[$hm]['z']);

            $x1 = $this->x2img($this->bases[$next]['x']);
            $z1 = $this->z2img($this->bases[$next]['z']);


            imageline($map, $x, $z, $x1, $z1, $color);
        }
    }

    private function drawdirbyid($map, $id){
        if ($this->directions == null){return true;}
        $dir = $this->directions[$id];
        $x = $this->x2img($dir['x']);
        $z = $this->z2img($dir['z']);



        $deg = $dir['to'];
        $deg2=$dir['from'];


        $tan = -tan(deg2rad($deg));

        if ($deg < -90 or $deg > 90){
            $z1 = $this->resolution+10;
        }
        else{
            $z1 = -$this->resolution-10;
        }

        $x1 = $tan*$z1 - $tan*$z + $x;


        $color = imagecolorallocate($map, mt_rand(50, 150), mt_rand(50, 150), mt_rand(50, 150));

        imageline($map, $x, $z, $x1, $z1, $color);

        $tan = -tan(deg2rad($deg2));

        if ($deg < -90 or $deg > 90){
            $z1 = $this->resolution+10;
        }
        else{
            $z1 = -$this->resolution-10;
        }

        $x1 = $tan*$z1 - $tan*$z + $x;
        imageline($map, $x, $z, $x1, $z1, $color);

    }

    private function drawdir($map){

        if ($this->directions == null){$this->directions=array();}
        foreach ($this->directions as $dir){
            $x = $this->x2img($dir['x']);
            $z = $this->z2img($dir['z']);



            //$deg = ($dir['from']+$dir['to'])/2;

            $deg = $dir['to'];
            $deg2=$dir['from'];

            //$deg=-179.75;
            ///$x=490;
            //$z=490;


            $tan = -tan(deg2rad($deg));

            if ($deg < -90 or $deg > 90){
                $z1 = $this->resolution+10;
            }
            else{
                $z1 = -$this->resolution-10;
            }

            $x1 = $tan*$z1 - $tan*$z + $x;


            $color = imagecolorallocate($map, mt_rand(50, 150), mt_rand(50, 150), mt_rand(50, 150));

            imageline($map, $x, $z, $x1, $z1, $color);

            $tan = -tan(deg2rad($deg2));

            if ($deg < -90 or $deg > 90){
                $z1 = $this->resolution+10;
            }
            else{
                $z1 = -$this->resolution-10;
            }

            $x1 = $tan*$z1 - $tan*$z + $x;
            imageline($map, $x, $z, $x1, $z1, $color);
        }

    }

    private function drawBases($map, $color, $color2){

        if ($this->bases == null){$this->bases=array();}
        foreach ($this->bases as $base){


            if (!$this->inmap($base['x'], $base['z'])){
                continue;
            }

            $x = $this->x2img($base['x']);
            $z = $this->z2img($base['z']);

            if (!isset($base['name'])){
                $base['name'] = $base['x'].':'.$base['z'];
            }


            //drawing point
            imagesetpixel($map, $x, $z, $color);
            imagesetpixel($map,  $x -1, $z, $color);
            imagesetpixel($map,  $x +1, $z, $color);
            imagesetpixel($map, $x,  $z-1, $color);
            imagesetpixel($map, $x,  $z+1, $color);
            imagesetpixel($map,$x-1,  $z-1, $color);
            imagesetpixel($map,  $x+1,  $z+1, $color);
            imagesetpixel($map,  $x+1,  $z-1, $color);
            imagesetpixel($map,  $x-1,  $z+1, $color);
            imagesetpixel($map,  $x+2, $z, $color);
            imagesetpixel($map,  $x-2, $z, $color);
            imagesetpixel($map, $x,  $z+2, $color);
            imagesetpixel($map, $x,  $z-2, $color);

            //draw text

            imagettftext ( $map , 10, 0, $this->x2img($base['x']) + 4 , $this->z2img($base['z'])-1, $color2, Url::to('@webroot/B52.ttf'), $base['name']);


        }
    }

    private function gridstep(){
        //return 1000;
        foreach ($this->gridsteps as $step){
            if ($this->dist/$step > 20){
                continue;
            }
            else{
                return $step;

            }
        }
    }

    private function x2img($x){
        return round( (($x-$this->xstart)/$this->scale)+0.5, 0);
    }

    private function z2img($z){
        return round( (($z-$this->zstart)/$this->scale)+0.5, 0);
    }

    private function inmap($x, $z){
        if ($x < $this->xstart or $x > ($this->xstart + $this->dist)){
            return false;
        }
        if ($z < $this->zstart or $z > ($this->zstart + $this->dist)){
            return false;
        }
        return true;
    }


    private function drawgrid($map, $color, $textcolor){

        $step = $this->gridstep();
        //vertical lines
        for ($i = $this->xstart; $i < $this->xstart + $this->dist; $i = $i + $step){
            $x = round($i/$step, 0, PHP_ROUND_HALF_DOWN)*$step;
            $z = $this->zstart;
            $z2 = $this->zstart+$this->dist;

            imageline($map, $this->x2img($x), $this->z2img($z), $this->x2img($x), $this->z2img($z2), $color);
            imagettftext ( $map , 8, -90, $this->x2img($x) + 4 , 18, $textcolor, Url::to('@webroot/B52.ttf'), $this->gridtext($x));
        }

        //horizontal lines

        for ($i = $this->zstart; $i < $this->zstart + $this->dist; $i = $i + $step){
            $z = round($i/$step, 0, PHP_ROUND_HALF_DOWN)*$step;
            $x = $this->xstart;
            $x2 = $this->xstart+$this->dist;
            imageline($map, $this->x2img($x), $this->z2img($z), $this->x2img($x2), $this->z2img($z), $color);
            imagettftext ( $map , 8, 0, 5 , $this->z2img($z)+8, $textcolor, Url::to('@webroot/B52.ttf'), $this->gridtext($z));
        }

    }


    private function gridtext($c){
        if (abs($c/1000000)>=1){
            return $c/1000000 .'kk';
        }
        if (abs($c/1000)>=1){
            return $c/1000 .'k';
        }
        return $c;
    }

    public function getmapurl(){

        $this->generate();
        return $this->mapurl;
    }


}