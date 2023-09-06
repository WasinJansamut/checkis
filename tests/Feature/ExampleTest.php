<?php

namespace Tests\Feature;

use App\Http\Controllers\CheckingController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function testCheckIF(){

        $value = null;

        $this->assertFalse($value == "02");


    }


    public function testGoodOneICD()
    {
        $icd10 = "V20";
        $icdStart = "V20";
        $icdEnd = "V20";
        $result = CheckingController::checkICD10InRange($icd10, $icdStart, $icdEnd);

        $this->assertTrue($result);
    }

    public function testBadOneICD()
    {
        $icd10 = "V19";
        $icdStart = "V20";
        $icdEnd = "V20";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse( $result);
    }



    public function testGoodICD()
    {
        $icd10 = "X50";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertTrue($result);
    }

    public function testGoodLessCharICD()
    {
        $icd10 = "X5";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertTrue($result);
    }

    public function testGoodMoreCharICD()
    {
        $icd10 = "X555555";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertTrue($result);
    }

    public function testGoodOneCharICD()
    {
        $icd10 = "W";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertTrue($result);
    }

    public function testWrongLowerICD()
    {
        $icd10 = "U60";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse($result);
    }

    public function testWrongLowerLessCharICD()
    {
        $icd10 = "U6";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse($result);
    }

    public function testWrongUpperICD()
    {
        $icd10 = "X60";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse($result);
    }


    public function testWrongUpperAndMoreCharICD()
    {
        $icd10 = "X600";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse($result);
    }

    public function testWrongUpperAndLessCharICD()
    {
        $icd10 = "X6";
        $icdStart = "V00";
        $icdEnd = "X59";
        $result = CheckingController::checkICD10InRange($icd10,$icdStart,$icdEnd);

        $this->assertFalse($result);
    }
}
