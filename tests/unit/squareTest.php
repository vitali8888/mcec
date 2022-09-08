<?php

use \app\models\square;

class squareTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testGetMinXWithNoPoints()
    {
        $square = $this->make(square::class);

        $this->assertSame(-10000000, $square->getminx());

    }

    public function testGetMinXWithPoints()
    {
        $square = $this->make(square::class, [
            'points' => ['1' => ['x' => 500, 'z' => 300], '2' => ['x' => 450, 'z' => 444]],
        ]);

        $this->assertSame(450, $square->getminx());

    }

    /*
    public function testcodeceptonmockes(){
        $square = $this->make(square::class, [
            'delthis' => \Codeception\Stub\Expected::once()
        ]);

        $square->getminx();
        $this->assertSame(5, $square->delthistoo());

    }
    */

    
}