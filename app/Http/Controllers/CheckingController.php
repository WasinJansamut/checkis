<?php

namespace App\Http\Controllers;


use App\Mail\SentMail;
use App\Models\CasesModel;
use App\Models\HospcodeModel;
use App\Models\IsModel;
use App\Models\JobsModel;
use App\Models\User;
use App\Exports\IsReportExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Monolog\Handler\IFTTTHandler;

class CheckingController extends Controller
{
    //    private $EMSList = ['ALS', 'BLS','FR'];
    private $apointList = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
    private $carList = ['04', '05', '06', '18', '19'];
    private $kidFrontName = ['ด.ช', 'ด.ญ.', 'เด็กชาย', 'เด็กหญิง'];
    private $maleFrontName = ['นาย', 'ด.ช.', 'Mr', 'พระ'];
    private $femaleFrontName = ['ms', 'ms.', 'mrs', 'mrs.', 'Ms', 'Ms.', 'Mrs', 'Mrs.', 'Miss', 'miss', 'นาง', 'น.ส.', 'ด.ญ.', 'นส.', 'ดญ.', 'หญิง', "แม่", "นางสาว"];
    private $policeSoldierFrontname = ['ดต.', 'พ.จ', 'ท.', 'ต.', 'อ.', 'ว่าที่', 'ร.ต', 'ร.ท', 'เรือ', 'ตำรวจ', "สิบ", "ร้อย", "พัน", 'พล'];
    private $MonkFrontname = ['พ.ภ', 'พระ', 'ชี', 'เณร'];


    //จำนวนของ rows ที่ไม่ตรงในแต่ละด้าน
    private $type_1 = [];
    private $type_2 = [];
    private $type_3 = [];
    private $type_4 = [];
    private $type_5 = [];
    private $type_6 = [];

    private $case_array = [];
    private $case_id_run = [];

    public function sentEmail($start_date, $end_date, $hosp, $start_time)
    { //sent email to user func

        $user = User::where('username', $hosp)->first();
        if ($user['username'] == "admin") {
            $hosp_name = "admin";
        } else {
            $hospital = HospcodeModel::where('hospcode', $hosp)->first();
            $hosp_name = $hospital['full_name'];
        }

        $email = $user->email;
        $name = $user->firstname;

        if ($email != "") {
            $details = [
                'start_date' => $start_date->format('d/m/Y'),
                'end_date' => $end_date->format('d/m/Y'),
                'hosp_name' => $hosp_name,
                'name' => $name,
                'start_time' => $start_time->format("d/m/Y H:i:s"),

            ];

            Mail::to($email)->send(new SentMail($details));
            return "success";
        } else {
            return "no email";
        }
    }

    //checking cron job
    public static function runJob($job)
    { //for automation check when created a single job

        set_time_limit(60 * 20);

        $checkController = new CheckingController();
        $checkController->checkProcess($job, $job->hosp);
    }


    public function checkingCronJob()
    { //for corn job checking
        try {
            while (true) {
                $job = JobsModel::where("status", "waiting")->first();
                if ($job) {
                    $hosp = $job->hosp;
                    $this->checkProcess($job, $hosp);
                } else {
                    dump("no more job ..");
                    break;
                }
            }
        } catch (\Exception $error) {
            dd($error);
        }
    }

    public function selectedCheck($id)
    { //for select id job to check

        set_time_limit(60 * 20);

        $job = JobsModel::where("id", $id)->first();
        $this->checkProcess($job, $job->hosp);

        return redirect()->route('retrospective_report');
    }


    public function checking()
    { //for case multiple cases running by while loop

        try {
            while (true) {

                if (Auth::user()->type == 0) {
                    $hosp = Auth::user()->username;
                    $result = JobsModel::where("hosp", $hosp)->where("status", "waiting")->first();
                } else { //if admin
                    $result = JobsModel::where("status", "waiting")->first();

                    if ($result) {
                        $hosp = $result->hosp;
                    }
                }

                if ($result) {
                    $this->checkProcess($result, $hosp);
                } else {
                    dump("no more job ..");
                    break;
                }
            }
        } catch (\Exception $e) {

            dump($e);
            dd("error");
        }
        return redirect()->route('retrospective_report');
    }

    public function checkingOnlyOne()
    { //for case one cases

        try {
            $progress = JobsModel::where("status", "in progress")->first();

            if ($progress) {
                return $progress;
            } else {
                $result = JobsModel::where("status", "waiting")->first();

                if ($result) {
                    $hosp = $result->hosp;
                    $this->checkProcess($result, $hosp);
                }

                return $result;
            }
        } catch (\Exception $e) {

            dump($e);
            dd("error");
        }

        return response()->json(['status' => 200]);
    }



    public function checkProcess($job, $hosp)
    {

        set_time_limit(60 * 20);


        try {
            $start_date = $job->start_date->format("Y-m-d");
            $end_date = $job->end_date->format("Y-m-d");

            $now = Carbon::now();
            $total = $job->count;
            $job->status = "in progress";
            $job->start_time = $now->format('Y-m-d H:i:s');
            $job->user_id = Auth::id();
            $job->save();
            //            dump("Checking on $hosp in $start_date to $end_date");

            $fileName = "report_$hosp" . "-" . $now->getTimestamp() . "-" . $start_date . "-" . $end_date . ".xlsx";

            $isData = IsModel::where('hosp', $hosp)->whereBetween('hdate', [$start_date, $end_date])->get();

            $this->checkCaseNew($isData);
            $this->insertTypesToDataBase($job, $total);
            (new IsReportExport($hosp, $job['start_date'], $job['end_date'], $job['id'], $this->case_array))->store("/public/report/$fileName");


            $this->resetValue();

            $job->status = "checked";
            $job->path = "storage/report/$fileName";


            sleep(2);

            $detail =  $job->toArray();
            $detail['start_time'] = $job->start_time->format('Y-m-d H:i:s');
            LogController::addlog("check", "jobs", $detail);

            $result = $this->sentEmail($job['start_date'], $job['end_date'], $hosp, $job['start_time']); //sent mail to user
            $job->user_id = Auth::id();
            $job->email_status = $result;
            $job->save();
        } catch (\Exception $error) {
            dd($error);
        }
    }

    public function checkCaseNew($datas)
    {
        $this->setupCaseCheck();
        $this->case_id_run = [];

        foreach ($datas as $row) {

            $this->checkErrorInRow($row);
        }
    }

    public function checkErrorInRow($row)
    {
        try {
            $row_id = $row['id'];

            //1. อุบัติเหตุจากการขนส่ง รหัส V01-X59 และการบาดเจ็บ Injby จะต้องไม่มีรหัส 2 คือ ทำร้ายตนเอง (X60-X84),
            //    Injby 3 คือ ผู้อื่นทำร้าย  (X85-Y09) และ
            //    Injby 4 คือ ปฏิบัติทางกฏหมาย/สงคราม/สถานการณ์ (Y35-Y36)
            $this->case_1($row, $row_id);


            //2. เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสทำร้ายตนเอง ซึ่งมีรหัสเป็น Injby = 2
            //เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสทำร้ายตนเอง(ICD=X60-X84)  และมีรหัสการบาดเจ็บ(Injby) เท่ากับ 2
            if ($row->age <= 5) {
                if ($row->injby == 2 || self::checkICD10InRange($row->icdcause, "X60", "X84")) {
                    $this->addCases(2, $row_id, $row);
                }
            }

            //3. พฤติกรรมเสี่ยงจากการใช้โทรศัพท์มือถือ จะต้องมีเป็นรหัส Risk 5 ทุกราย
            //ถ้ามีการบาดเจ็บเกิดโดย อุบัติเหตุขนส่ง  เท่ากับ 1 (Injby=1) หรือ สาเหตุการบาดเจ็บ เท่ากับ 1 (CAUSE = 1)
            //หรือ สาเหตุของอุบัติเหตุและการบาดเจ็บตาม ICD 10 จะต้องมีความเสี่ยง เท่ากับ V00 - V89 ()
            //(Risk5) ไม่เท่ากับค่าว่าง
            if (
                (self::checkICD10InRange($row->icdcause, "V00", "V89") || $row->injby == 1 ||  $row->cause == 1)
                && self::checkEmpty($row->risk5)
            ) {
                $this->addCases(3, $row_id, $row);
            }


            //4. ICDcause จะต้องมีรหัสทุกราย และยกเว้นอุบัติเหตุจากการขนส่งที่ไม่ต้องมีรหัส เพราะใช้ cause = 1 แทนแล้ว
            //สาเหตุของอุบัติเหตุและการบาดเจ็บตาม ICD 10 จะต้องระบุทุกราย ICDcause <>  " "  ยกเว้น สาเหตุการบาดเจ็บ ต้องไม่เท่ากับ 1 (CAUSE <> 1)
            if ((self::checkEmpty($row->icdcause) && ($row->cause) != 1)) {
                $this->addCases(4, $row_id, $row);
            }

            //5. มาจากที่เกิดเหตุ (Atohosp) จะต้องมีรหัสทุกราย และหากเดินทางมาโดยหน่วยบริการการแพทย์ฉุกเฉิน (EMS) ให้ระบุระดับ ALS, BLS, หรือ FR  ด้วยเสมอ
            //มาจากที่เกิดเหตุ (Atohosp) จะต้องมีรหัสทุกราย และหากเดินทางมาโดยหน่วยบริการการแพทย์ฉุกเฉิน (Atohosp=3) ให้ระบุระดับ(EMS) = ALS, BLS, หรือ FR  ด้วยเสมอ
            if (($row->atohosp == 3 && self::checkEmpty($row->ems)) ||
                ($row->atohosp == 3 && !in_array($row->ems, [1, 2, 3]))
            ) {
                $this->addCases(5, $row_id, $row);
            }

            //6.เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสพฤติกรรมเสี่ยงที่เป็นการดื่มแอลกอฮอล์
            //เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสพฤติกรรมเสี่ยงที่เป็นการดื่มแอลกอฮอล์(RISK1) ไม่เท่ากับ 1
            if (
                $row->age < 5 && $row->risk1 == 1
            ) {
                $this->addCases(6, $row_id, $row);
            }

            //7. จะต้องไม่มีรหัสบาดเจ็บในอาชีพจากการทำร้ายตนเอง Injby = 2
            //หากมีการบาดเจ็บจากการทำงานในอาชีพ (injoccu=1)

            if (
                $row->injoccu == 1 && $row->injby == 2
            ) {
                $this->addCases(7, $row_id, $row);
            }

            //8. DBA จะต้องมีรหัสการนำส่งทุกราย STAER == 1 and Atohosp == 0
            if (
                $row->staer == 1 && $row->atohosp == 0
            ) {
                $this->addCases(8, $row_id, $row);
            }

            //9. อุบัติเหตุตกน้ำ จมน้ำ ICDcause จะเป็นรหัส W65-W74 และจุดเกิดเหตุจะต้องไม่มีรหัสที่เป็นบริเวณถนน รหัส คือ Apoint = 5
            if (
                self::checkICD10InRange($row->icdcause, "W65", "W74")  &&
                $row->apoint == 5
            ) {
                $this->addCases(9, $row_id, $row);
            }

            //10. บาดเจ็บจากอาชีพ รหัส คือ injoccu จะต้องมีรหัสการบันทึกอาชีพ (occu) ทุกราย
            if ($row->injoccu == 1 && self::checkEmpty($row->occu)) {
                $this->addCases(10, $row_id, $row);
            }

            //11. ผู้บาดเจ็บ ที่มี br = 6, ais = 1 แล้ว และไม่สมควรเสียชีวิต หากเสียชีวิตควรมีเหตุผลแนบ ดังนี้
            //1. จำหน่ายผู้ป่วยออกจาก ER (staer = 1 : เสียชีวิตก่อนถึง รพ., 6 : ถึงแก่กรรม) และ
            //2. จำหน่ายผู้ป่วยออกจาก ward (staward = 5 : ถึงเเก่กรรม)
            if (in_array($row->staer, [1, 6]) || $row->staward == 5) {
                $isHighInjury = false;
                for ($i = 1; $i <= 6; $i++) {
                    if (!self::checkEmpty($row->{"br$i"})) {
                        if ($row->{"br$i"} != 6 && $row->{"ais$i"} != 1) {
                            $isHighInjury = true;
                        }
                    }
                }
                if ($isHighInjury == false) {
                    $this->addCases(11, $row_id, $row);
                }
            }

            //12. ข้อมูลเพศจะต้องมีรหัส คือ sex : 1 = ชาย และ 2 = หญิง ทุกราย
            if (self::checkEmpty($row->sex)) {
                $this->addCases(12, $row_id, $row);
            }

            //13. bp, rr, p ไม่ควรเกินกว่ากำหนด (bp1 ไม่เกิน 300, bp2 ไม่เกิน 300, pr ไม่เกิน 200, rr ไม่เกิน 60)
            // และไม่ควรมีค่าว่าง
            if (
                ($row->bp1 > 300  &&  $row->bp1 < 998) ||
                ($row->bp1 >= 1000)  ||
                ($row->bp2 > 300 &&  $row->bp2 < 998) ||
                ($row->bp2 >= 1000)  ||
                ($row->pr > 200) ||
                ($row->rr > 60 && $row->rr <= 98) ||  $row->rr >= 100
            ) {
                $this->addCases(13, $row_id, $row);
            }

            //14. จำหน่ายผู้ป่วยออกแล้ว (staward) และจะต้องมีวันที่จำหน่าย (rdate)
            //   != 6
            if ((!self::checkEmpty($row->staward) && $row->staward != 6) &&
                // if (($row->staward <= 1 || $row->staward <= 6) &&
                self::checkEmpty($row->rdate)
            ) {
                $this->addCases(14, $row_id, $row);
            }

            //15.อายุเป็น ปี (age) หรือเดือน (month) หรือวัน (day)
            // จะต้องมีรหัสทุกราย และอายุ จะต้องไม่เกิน 130 ปี และต้องมีรหัส AGE (อายุ) ทุกราย
            // จะต้องไม่มีค่าว่าง ยกเว้น เคส DBA DER

            if ($row->age > 130) {
                $this->addCases(15, $row_id, $row);
            } else {
                if (($row->month == 0 && $row->age == 0) ||  (self::checkEmpty($row->age) && self::checkEmpty($row->month))) {
                    if (self::checkEmpty($row->day)) {
                        $this->addCases(15, $row_id, $row);
                    }
                }
            }

            //16. ออกจาก ER จะต้องมีรหัสรับไว้รักษาเป็น staer = 7 แล้วจะต้องระบุว่าอยู่ ward ใด ward หนึ่ง และถ้าผู้ป่วย admit จะต้องแสดงสถานภาพการจำหน่ายออกจาก ward ทุกราย
            if (
                $row->staer == 7 &&
                (self::checkEmpty($row->staward)
                )
            ) {
                $this->addCases(16, $row_id, $row);
            }

            //17. สถานที่เกิดเหตุจะต้องมีรหัส aplace ทุกราย และให้ครบถึงระดับหมู่บ้าน ตำบล
            if (
                $row->aplace != 99 &&
                (self::checkEmpty($row->aplace) || (
                    ($row->aplace >= 10 && $row->aplace <= 96) && (self::checkEmpty($row->ampur) || self::checkEmpty($row->tumbon))
                ))

            ) {
                $this->addCases(17, $row_id, $row);
            }

            //18. พฤติกรรมเสี่ยงแอลกอฮอล์จะต้องมีรหัส risk 1 ทุกราย
            if (self::checkEmpty($row->risk1)) {
                $this->addCases(18, $row_id, $row);
            }

            //19. จะต้องมีรหัส hn/name/fname ทุกราย
            if (
                self::checkEmpty($row->hn) ||
                self::checkEmpty($row->name) ||
                self::checkEmpty($row->fname)
            ) {
                $this->addCases(19, $row_id, $row);
            }

            //20. อุบัติเหตุขนส่งจะเป็น cause=1 และพฤติกรรมเสี่ยงเข็มขัดนิรภัยจะต้องมีรหัส risk 3 ทุกราย ของรถยนต์ทุกชนิด รหัสคือ v40-79
            if (($row->cause == 1 && self::checkEmpty($row->risk3) &&
                (in_array($row->injt, [4, 5, 6, 7, 8, 9, 10, 18, 19, 191, 192, 193]) ||
                    self::checkICD10InRange($row->icdcause, "V40", "V79")
                )
            )) {
                $this->addCases(20, $row_id, $row);
            }

            //21. อุบัติเหตุขนส่งจะเป็น cause=1 พฤติกรรมเสี่ยงหมวกนิรภัยจะต้องมีรหัส risk 4 ทุกราย ของรถจักรยานยนต์ทุกชนิด รหัสคือ v20-29)
            if (($row->cause == 1 && self::checkEmpty($row->risk4))
                && $row->injt == 2
            ) {
                $this->addCases(21, $row_id, $row);
            }

            //22.  ตัวแปร SEX (เพศ) และ PRENAME (คำนำหน้า) จะต้องมีรหัสที่สอดคล้องกัน
            if (($row->sex == 2 && $this->checkWordInArray($row->prename, $this->maleFrontName)) ||
                ($row->sex == 1 && $this->checkWordInArray($row->prename, $this->femaleFrontName))
            ) {
                $this->addCases(22, $row_id, $row);
            }


            //23. เด็กอายุต่ำกว่าหรือเท่ากับ 5 ปี จะต้องมีรหัสผู้ขับขี่รถทุกประเภท ยกเว้น จักรยานและสามล้อ Injt 01
            if (
                $row->age <= 5 &&
                ($row->injp == 2 && $row->injt != 1)
            ) {
                $this->addCases(23, $row_id, $row);
            }

            //24. คำนำหน้า (PRENAME) และ อาชีพ (OCCU) (ทหาร / ตำรวจ) จะต้องมีรหัสที่สอดคล้องกัน
            // Cancel
            // if ($this->checkWordInArray($row->prename, $this->policeSoldierFrontname) == true) {
            //     if ($row->occu != 2) {
            //         $this->addCases(24, $row_id, $row);
            //     }
            // } else {
            //     if ($row->occu == 2) {
            //         $this->addCases(24, $row_id, $row);
            //     }
            // }

            //25. คำนำหน้า (PRENAME) และ อาชีพ (OCCU) (พระ)  จะต้องมีรหัสที่สอดคล้องกัน

            if ($this->checkWordInArray($row->prename, $this->MonkFrontname) == true) {
                if ($row->occu != 9) {
                    $this->addCases(25, $row_id, $row);
                }
            } else {
                if ($row->occu == 9) {
                    $this->addCases(25, $row_id, $row);
                }
            }

            //26 ตัวแปร คำนำหน้า(PRENAME) และ อาชีพ (OCCU) (เด็ก) ต้องคลองจองกัน
            if ($row->age < 15) {
                if ($row->age <= 3 and $row->occu != 17) {
                    $this->addCases(26, $row_id, $row);
                } else if ($row->age > 3 and $row->occu == 00) {
                    $this->addCases(26, $row_id, $row);
                } else if (!$this->checkWordInArray($row->prename, $this->kidFrontName)) {
                    $this->addCases(26, $row_id, $row);
                }
            }

            //27. ตัวแปร INJOCCU ถ้าตอบ 1 จะเป็นเกิดจากอาชีพ และจะต้องมีรหัสอาชีพ (OCCU) ที่ไม่มีค่าว่าง
            if (
                $row->injoccu == 1 && $row->age < 65   &&
                (self::checkEmpty($row->occu) || $row->occu == 0)
            ) {
                $this->addCases(27, $row_id, $row);
            }

            //28. วันที่เกิดเหตุ (ADATE), เวลาเกิดเหตุ (ATIME), วันที่มารพ. (HDATE), เวลาที่มารพ. (HTIME) จะต้องมีรหัสครบถ้วนทุกราย
            if (
                self::checkEmpty($row->adate) ||
                self::checkEmpty($row->atime) ||
                self::checkEmpty($row->hdate) ||
                self::checkEmpty($row->htime)
            ) {
                $this->addCases(28, $row_id, $row);
            }

            //29. จุดเกิดเหตุจะต้องมีรหัส apoint ทุกราย
            if (self::checkEmpty($row->apoint)) {
                $this->addCases(29, $row_id, $row);
            }

            //30. ตัวแปร APOINT จะต้องเป็นไปตามคู่มือ ถ้ามีรหัสนอกเหนือจากนั้น จะแจ้งรพ.เจ้าของข้อมูลทราบ
            if (
                ($row->apoint < 1  && $row->apoint == 10)  || ($row->apoint > 17)
            ) {
                $this->addCases(30, $row_id, $row);
            }

            //31. บาดเจ็บจากอุบัติเหตุรหัสเป็น Injby = 1 แต่สาเหตุการบาดเจ็บจะต้องไม่มีรหัสเป็นเจตนา icdcause = X60-Y09
            if (
                $row->injby == 1 && self::checkICD10InRange($row->icdcause, "X60", "Y09")
            ) {
                $this->addCases(31, $row_id, $row);
            }

            //32. บาดเจ็บจากการทำร้ายตนเองรหัสเป็น Injby = 2  และสาเหตุการบาดเจ็บจะต้องไม่มีรหัสทำร้ายตนเอง (ICD cause <> X60-X84)
            if (!self::checkEmpty($row->caues)) {
                if (
                    $row->injby == 2  &&   $row->cause == 2 && self::checkICD10InRange($row->icdcause, "X60", "X84")
                ) {
                    $this->addCases(32, $row_id, $row);
                }
            }


            //33. บาดเจ็บจากการทำร้ายตนเองรหัสเป็น(Injby =3) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัสถูกทำร้ายด้วยวิธีต่าง ๆ (ICDcause = X85-Y09)
            if (!self::checkEmpty($row->caues)) {
                if (
                    $row->injby == 3 && self::checkICD10InRange($row->icdcause, "X85", "Y09")
                ) {
                    $this->addCases(33, $row_id, $row);
                }
            }


            //34. บาดเจ็บจากภัยทางสงคราม (Injby =4) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัส ICDcause = Y35 – Y36
            if (!self::checkEmpty($row->caues)) {
                if (
                    $row->injby == 4 && ($row->cause == 2 || self::checkICD10InRange($row->icdcause, "Y35", "Y36"))
                ) {
                    $this->addCases(34, $row_id, $row);
                }
            }


            //35. บาดเจ็บจากไม่ทราบ (Injby =  N) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัส ICDcause = Y10 – Y34
            if (
                strtoupper($row->injby) == "N" && ($row->cause != 2 || !self::checkICD10InRange($row->icdcause, "Y10", "Y34"))
            ) {
                $this->addCases(35, $row_id, $row);
            }

            //36. บาดเจ็บในอาชีพจะต้องไม่มีรหัส Injby = 2 คือ การทำร้ายตนเอง
            if (
                $row->injoccu == 1 && $row->injby == 2
            ) {
                $this->addCases(36, $row_id, $row);
            }

            //37. CAUSE 1 และ 2 ICDCAUSE  จะต้องไม่มีรหัสว่าง
            if (
                $row->injby != 1 &&
                in_array($row->cause, [1, 2]) && self::checkEmpty($row->icdcause)
            ) {
                $this->addCases(37, $row_id, $row);
            }

            //38. ขับขี่รถจักรยานยนต์ จะต้องมีข้อมูลพฤติกรรมเสี่ยงเรื่องหมวกนิรภัย RISK4
            if (
                $row->injp != 2 && ($row->injt == 2 && self::checkEmpty($row->risk4))
            ) {
                $this->addCases(38, $row_id, $row);
            }

            //39. คนเดินเท้าจะต้องมีรหัสบาดเจ็บจากพาหนะอื่นด้วย รหัสคือ INJFROM
            if (
                $row->injp == 1 && self::checkEmpty($row->injfrom)
            ) {
                $this->addCases(39, $row_id, $row);
            }

            //40. ค่า COMA ตัวแปร E, V และ M จะต้องไม่มีรหัสว่าง
            if ((!self::checkEmpty($row->coma) && ($row->coma < 3 || $row->coma > 15))  ||
                (
                    ($row->e < 1 || $row->e > 4) ||
                    ($row->v < 1 || $row->v > 5) ||
                    ($row->m < 1 || $row->m > 6)
                )
            ) {
                $this->addCases(40, $row_id, $row);
            }

            //41. ตรวจสอบลักษณะการบาดเจ็บจะต้อมีรหัส TINJ ทุกราย
            if (self::checkEmpty($row->tinj)) {
                $this->addCases(41, $row_id, $row);
            }

            $this->type_1 = array_unique($this->type_1);
            $this->type_2 = array_unique($this->type_2);
            $this->type_3 = array_unique($this->type_3);
            $this->type_4 = array_unique($this->type_4);
            $this->type_5 = array_unique($this->type_5);
            $this->type_6 = array_unique($this->type_6);
        } catch (\Exception $error) {
            dd($error);
        }
    }




    public function case_1($row, $row_id)
    {
        //1. อุบัติเหตุจากการขนส่ง รหัส V01-X59 และการบาดเจ็บ Injby จะต้องไม่มีรหัส 2 คือ
        //      ทำร้ายตนเอง (X60-X84),
        //      3 คือ ผู้อื่นทำร้าย  (X85-Y09) และ
        //      4 คือ ปฏิบัติทางกฏหมาย/สงคราม/สถานการณ์ (Y35-Y36)
        //

        if (self::checkICD10InRange($row->icdcause, "V01", "V89")) {
            if ((int)$row->injby != 1) {
                $this->addCases(1, $row_id, $row);
            }
        }
        if (self::checkICD10InRange($row->icdcause, "X00", "X59")) {
            if ((int)$row->injby != 1) {
                $this->addCases(1, $row_id, $row);
            }
        }
        if (self::checkICD10InRange($row->icdcause, "X60", "X84")) {
            if ((int)$row->injby != 2) {
                $this->addCases(1, $row_id, $row);
            }
        }
        if (self::checkICD10InRange($row->icdcause, "X85", "X99")) {
            if ((int)$row->injby != 3) {
                $this->addCases(1, $row_id, $row);
            }
        }
        if (self::checkICD10InRange($row->icdcause, "Y00", "Y09")) {
            if ((int)$row->injby != 3) {
                $this->addCases(1, $row_id, $row);
            }
        }
        if (self::checkICD10InRange($row->icdcause, "Y35", "Y36")) {
            if ((int)$row->injby != 4) {
                $this->addCases(1, $row_id, $row);
            }
        }
        //  Add 3/10/65 By Anong
        if (self::checkICD10InRange($row->icdcause, "Y33", "Y34")) {
            if ((int)$row->injby != 'N') {
                $this->addCases(1, $row_id, $row);
            }
        }
    }
    //                                          W56     V11     -   X59
    public static function checkICD10InRange($icd10, $icd10_start, $icd10_end)
    {


        $icd10_start = str_split(strtoupper($icd10_start));
        $icd10_end = str_split(strtoupper($icd10_end));
        //  V W X
        $alphas = range($icd10_start[0], $icd10_end[0]);
        $max_loop = count($alphas); // 3

        $char_1_start = $icd10_start[1];
        $char_2_start = $icd10_start[2];

        $char_1_end = $icd10_end[1];
        $char_2_end = $icd10_end[2];

        if (self::checkEmpty($icd10)) {
            return false;
        }

        $lengh = strlen($icd10);
        $icd10 = str_split(strtoupper($icd10));



        $main = "";
        $char_1 = 0;
        $char_2 = 0;

        if ($lengh >= 1) {
            $main = $icd10[0];    // W
        }
        if ($lengh >= 2) {
            $char_1 = $icd10[1];  // 5
        }
        if ($lengh >= 3) {
            $char_2 = $icd10[2]; // 6
        }

        $index = 1;
        foreach ($alphas as $alpha) {
            if ($alpha == $main) {

                //check min start
                if ($index == 1) {
                    //   1           1
                    if ($char_1 < $char_1_start) {
                        return false;
                    }

                    //   0            1
                    if ($char_2 < $char_2_start) {
                        return false;
                    }

                    return true;
                } else if ($index == $max_loop) {

                    //   1           1
                    if ($char_1 > $char_1_end) {
                        return false;
                    }

                    //   0            1
                    if ($char_2 > $char_2_end) {
                        return false;
                    }

                    return true;
                } else {
                    // The match of main char is between start and end
                    // Don't have to check lower and upper icd number

                    return true;
                }
            }
            $index++;
        }
        return false;
    }

    public static function checkEmpty($value)
    {

        if ($value == null) {
            return true;
        }
        if (is_null($value)) {
            return true;
        }

        if ($value == "") {
            return true;
        }

        if ($value == " ") {
            return true;
        }

        return false;
    }

    public function setupCaseCheck()
    {
        $cases = CasesModel::orderBy('number')->get();


        foreach ($cases as $case) {
            $highlight_columns = explode(",", $case->highlight_columns);

            $this->case_array["case_" . $case->number] = [
                "case_number" => $case->number,
                "case_name" => $case->name,
                "highlight_columns" => $highlight_columns,
                "error_type" => "type_" . $case->errorType,
                "is_ids" => [],
                "is_datas" => []
            ];
        }
    }


    function addCases($case, $row_id, $row)
    {

        $value_key =  $case . "_" . $row_id;
        $key_case = "case_" . $case;


        if (!array_key_exists($value_key, $this->case_id_run)) {
            $this->case_id_run[$value_key] = 1;
            array_push($this->case_array[$key_case]["is_ids"], $row_id);
            array_push($this->case_array[$key_case]["is_datas"], $row);
            array_push($this->{$this->case_array[$key_case]["error_type"]}, $row_id);
        }
    }



    //func เช็คคำใน array
    function checkWordInArray($word, $arrays = array())
    {
        foreach ($arrays as $array) {
            $result = strpos($word, $array);

            if ($result !== false) {
                //true ถ้ามีใน array
                return true;
            }
        }

        //ถ้าไม่เจอตัวไหนเลย return false
        return false;
    }

    function get_percentage($total, $number) //จำนวนทั้งหมดมา กับ รับส่วนที่ผิดมา
    {
        if ($total > 0) {
            return 100 - (round(($number * 100) / $total, 2)); //return ส่วนที่ถูกกลับไป
        } else {
            return 0;
        }
    }

    function insertTypesToDataBase($result, $total)
    {

        $result->type_1 = $total - sizeof($this->type_1); //จำนวนที่ถุกต้องเก็บลง db
        $result->type_2 = $total - sizeof($this->type_2);
        $result->type_3 = $total - sizeof($this->type_3);
        $result->type_4 = $total - sizeof($this->type_4);
        $result->type_5 = $total - sizeof($this->type_5);
        $result->type_6 = $total - sizeof($this->type_6);
        $result->type_1P = $this->get_percentage($total, sizeof($this->type_1)); //ถูกกี่ % ส่งค่าที่ผิด กับ ค่ารวม ไป จะได้ค่าที่ถูก % คืนมา
        $result->type_2P = $this->get_percentage($total, sizeof($this->type_2));
        $result->type_3P = $this->get_percentage($total, sizeof($this->type_3));
        $result->type_4P = $this->get_percentage($total, sizeof($this->type_4));
        $result->type_5P = $this->get_percentage($total, sizeof($this->type_5));
        $result->type_6P = $this->get_percentage($total, sizeof($this->type_6));


        $result->save();
    }

    function resetValue()
    {
        $this->type_1 = []; //รีค่า
        $this->type_2 = [];
        $this->type_3 = [];
        $this->type_4 = [];
        $this->type_5 = [];
        $this->type_6 = [];

        $this->case_array = [];
    }
}
