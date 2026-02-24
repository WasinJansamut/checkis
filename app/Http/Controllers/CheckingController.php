<?php

namespace App\Http\Controllers;


use App\Mail\SentMail;
use App\Models\CasesModel;
use App\Models\LibHospcode;
use App\Models\IsModel;
use App\Models\JobsModel;
use App\Models\User;
use App\Exports\IsReportExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Monolog\Handler\IFTTTHandler;

class CheckingController extends Controller
{
    // private $EMSList = ['ALS', 'BLS','FR'];
    private $apointList = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
    private $carList = ['04', '05', '06', '18', '19'];
    private $kidFrontName = ['ดช', 'ดช.', 'ด.ช', 'ด.ช.', 'ดญ', 'ดญ.', 'ด.ญ', 'ด.ญ.', 'เด็กชาย', 'เด็กหญิง'];
    private $maleFrontName = ['นาย', 'ด.ช.', 'ด.ช', 'ดช', 'เด็กชาย', 'mr', 'mr.', 'm.r.', 'mister', 'พระ'];
    private $femaleFrontName = ['นาง', 'นางสาว', 'น.ส.', 'นส.', 'ด.ญ.', 'ด.ญ', 'ดญ.', 'ดญ', 'เด็กหญิง', 'หญิง', 'แม่', 'ms', 'ms.', 'mrs', 'mrs.', 'miss', 'miss.', 'm.s.', 'm.r.s.', 'm.i.s.s.', 'madam'];
    private $policeSoldierFrontname = ['ดต.', 'พ.จ', 'ท.', 'ต.', 'อ.', 'ว่าที่', 'ร.ต', 'ร.ท', 'เรือ', 'ตำรวจ', "สิบ", "ร้อย", "พัน", 'พล'];
    private $MonkFrontname = ['พ.ภ', 'พระ', 'ชี', 'เณร'];

    // จำนวนของ rows ที่ไม่ตรงในแต่ละด้าน
    private $type_1 = [];
    private $type_2 = [];

    private $case_array = [];
    private $case_id_run = [];

    public function sentEmail($start_date, $end_date, $hosp, $start_time)
    {
        // sent email to user func
        if (empty(user_info())) {
            return "ไม่พบผู้ใช้งาน";
        }

        if (user_info('hosp_code') == "admin") {
            $hosp_name = "admin";
        } else {
            $hospital = LibHospcode::where('off_id', $hosp)->first();
            $hosp_name = $hospital['full_name'];
        }

        $email = user_info('email');
        $name = user_info('name');

        if (!empty($email)) {
            $details = [
                'start_date' => $start_date->format('d/m/Y'),
                'end_date' => $end_date->format('d/m/Y'),
                'hosp_name' => $hosp_name,
                'name' => $name,
                'start_time' => $start_time->format("d/m/Y H:i:s"),

            ];

            Mail::to($email)->send(new SentMail($details));
            return "สำเร็จ";
        } else {
            return "ไม่พบอีเมล";
        }
    }

    // checking cron job
    public static function runJob($job)
    {
        // for automation check when created a single job

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
    {
        // for select id job to check

        set_time_limit(60 * 20);

        $job = JobsModel::where("id", $id)->first();
        $this->checkProcess($job, $job->hosp);

        return redirect()->route('retrospective_report');
    }

    public function checking()
    {
        // for case multiple cases running by while loop

        try {
            while (true) {

                if (user_info('user_level_code') == 'HOSP') {
                    $hosp = user_info('hosp_code');
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

    public function checking_asm1()
    {
        // for case multiple cases running by while loop
        try {
            while (true) {
                //if admin
                $result = JobsModel::where("status", "waiting")->first();

                if ($result) {
                    $hosp = $result->hosp;
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
        echo 'success';
        // return redirect()->route('retrospective_report');
    }

    public function checkingOnlyOne()
    {
        // for case one cases
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
        //dd($job, $hosp);
        set_time_limit(60 * 20);

        try {
            $start_date = $job->start_date->format("Y-m-d");
            $end_date = $job->end_date->format("Y-m-d");

            $now = Carbon::now();
            $total = $job->count;
            $job->status = "in progress";
            $job->start_time = $now->format('Y-m-d H:i:s');
            $job->user_id = user_info('uid');
            $job->save();
            //dump("Checking on $hosp in $start_date to $end_date");

            $fileName = "report_$hosp" . "-" . $now->getTimestamp() . "-" . $start_date . "-" . $end_date . ".xlsx";

            $isData = IsModel::where('hosp', $hosp)->whereBetween('hdate', [$start_date, $end_date])->get();
            //  dump("จำนวนข้อมูล: ", count($isData));

            $this->checkCaseNew($isData);
            $this->insertTypesToDataBase($job, $total);

            try {
                foreach ($this->case_array as $key => &$case) {
                    $case["case_name"] ??= "ไม่ทราบชื่อเคส";
                    $case["case_number"] ??= intval(str_replace("case_", "", $key));
                    $case["highlight_columns"] ??= [];
                }
                (new IsReportExport($hosp, $job->start_date, $job->end_date, $job->id, $this->case_array))->store("/public/report/$fileName");
            } catch (\Exception $e) {
                dd("Export Error:", $e->getMessage());
            }

            $this->resetValue();

            $job->status = "checked";
            $job->path = "storage/report/$fileName";


            sleep(2);

            $detail =  $job->toArray();
            // $detail['start_time'] = $job->start_time->format('Y-m-d H:i:s');
            $detail['start_time'] = $job['start_time']->format('Y-m-d H:i:s');

            LogController::addlog("check", "jobs", $detail);

            $result = $this->sentEmail($job['start_date'], $job['end_date'], $hosp, $job['start_time']); //sent mail to user
            $job->user_id = user_info('uid');
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

            // 1. ความสมบูรณ์ครบ 21 ตัวแปร
            $totalCheckFail = false;
            $isMotorcycle = in_array($row->injt, ['02', '021', '022', '023']);
            $seatbeltVehicles = ['04', '041', '05', '06', '07', '08', '09', '10', '18', '181', '182', '19', '191', '192'];

            if (
                self::checkEmpty($row->adate) ||
                self::checkEmpty($row->atime) ||
                self::checkEmpty($row->hdate) ||
                self::checkEmpty($row->htime) ||
                self::checkEmpty($row->staer) ||
                self::checkEmpty($row->apoint) ||
                self::checkEmpty($row->tinj) ||
                self::checkEmpty($row->risk1) ||
                self::checkEmpty($row->risk2) ||
                self::checkEmpty($row->e) ||
                self::checkEmpty($row->v) ||
                self::checkEmpty($row->m) ||
                (self::checkEmpty($row->age) && self::checkEmpty($row->month) && self::checkEmpty($row->day)) ||
                self::checkEmpty($row->bp1) ||
                self::checkEmpty($row->rr) ||
                self::checkEmpty($row->pr) ||
                self::checkEmpty($row->br1) ||
                self::checkEmpty($row->ais1) ||
                self::checkEmpty($row->cause) ||
                self::checkEmpty($row->ps) ||
                ($isMotorcycle && self::checkEmpty($row->risk4)) ||
                (
                    in_array($row->injt, $seatbeltVehicles) &&
                    self::checkEmpty($row->risk3)
                )
            ) {
                // Collect which fields are empty before adding the case
                $emptyFields = [];
                // Age group check: if all age, month, day empty, add 'age_group'
                if (self::checkEmpty($row->age) && self::checkEmpty($row->month) && self::checkEmpty($row->day)) {
                    $emptyFields[] = 'age_group';
                }
                $fieldsToCheck = [
                    'adate',
                    'atime',
                    'hdate',
                    'htime',
                    'staer',
                    'apoint',
                    'tinj',
                    'risk1',
                    'risk2',
                    'e',
                    'v',
                    'm',
                    // 'age', // removed age from here
                    'bp1',
                    'rr',
                    'pr',
                    'br1',
                    'ais1',
                    'cause',
                    'ps'
                ];
                foreach ($fieldsToCheck as $field) {
                    if (self::checkEmpty($row->{$field})) {
                        $emptyFields[] = $field;
                    }
                }
                if ($isMotorcycle) {
                    if (self::checkEmpty($row->risk4)) {
                        $emptyFields[] = 'risk4';
                    }
                }
                // เฉพาะกรณี injt เป็นพาหนะที่ควรมีเข็มขัดนิรภัย
                if (in_array($row->injt, $seatbeltVehicles) && self::checkEmpty($row->risk3)) {
                    $emptyFields[] = 'risk3';
                }
                // เพิ่มไว้ใน case_array["case_1"]["empty_fields"] สำหรับ export
                $key_case = "case_1";
                if (!isset($this->case_array[$key_case]["empty_fields"])) {
                    $this->case_array[$key_case]["empty_fields"] = [];
                }
                $this->case_array[$key_case]["empty_fields"][$row_id] = $emptyFields;
                $this->addCases(1, $row_id, $row);
            }

            // 2. ความสอดคล้องระหว่างเพศและคำนำหน้า
            if (
                ($row->sex == 1 && !$this->checkWordInArray($row->prename, $this->maleFrontName)) ||
                ($row->sex == 2 && !$this->checkWordInArray($row->prename, $this->femaleFrontName))
            ) {
                $this->addCases(2, $row_id, $row);
            }

            // 3. ความสอดคล้องระหว่างอายุและคำนำหน้า
            $prename = trim(str_replace(['.', ' '], '', strtolower($row->prename)));

            $childGroup = ['ดช', 'ดญ', 'เด็กชาย', 'เด็กหญิง', 'เด็ก'];
            $adultGroup = ['นาย', 'นาง', 'นางสาว', 'นส', 'mr', 'mrs', 'miss', 'รตอ', 'ว่าที่รตหญิง'];

            if ($row->age < 15 && !in_array($prename, $childGroup)) {
                $this->addCases(3, $row_id, $row); // เด็กแต่ไม่ใช้คำนำหน้าแบบเด็ก
            }
            /// ปิดไว้เพื่อให้ครอบคลุม
            // elseif ($row->age >= 15 && !in_array($prename, $adultGroup)) {
            //     $this->addCases(3, $row_id, $row); // ผู้ใหญ่แต่ไม่ใช้คำนำหน้าแบบผู้ใหญ่
            // }

            // 4. ความสอดคล้องระหว่างอายุ ประเภทผู้บาดเจ็บและพาหนะ
            // เฉพาะคนขับขี่ (injp = 2) ที่อายุน้อยกว่า 5 ปี ควรขับได้เฉพาะจักรยานหรือสามล้อ (injt = '01', '03')
            if ($row->age < 5 && $row->injp == '2') {
                if (!in_array($row->injt, ['01', '011', '03'])) {
                    $this->addCases(4, $row_id, $row);
                }
            }

            // 5. ความสอดคล้องระหว่างอายุ ประเภทผู้บาดเจ็บและพาหนะ
            // อายุ 5-10 ปี ขับได้เฉพาะจักรยาน, สามล้อ, จักรยานยนต์ (injt = '01', '011', '03', '02', '021', '022', '023')
            // มากกว่า 10 ปี ขับรถอื่นๆได้
            if ($row->injp == '2') {
                if ($row->age >= 5 && $row->age <= 10) {
                    if (!in_array($row->injt, ['01', '011', '03', '02', '021', '022', '023'])) {
                        $this->addCases(5, $row_id, $row);
                    }
                }
            }

            // 6. ความสอดคล้องระหว่างอายุ ประเภทผู้บาดเจ็บและแอลกอฮอล์
            // เด็กอายุต่ำกว่า 5 ปี ไม่ควรมีพฤติกรรมดื่มแล้วขับ (injp = 2 และ risk1 = 1)
            if ($row->age < 5 && $row->injp == '2' && $row->risk1 == '1') {
                $this->addCases(6, $row_id, $row);
            }

            // 7. ความสอดคล้องระหว่างอายุ ผู้ขับขี่และโทรศัพท์
            // ผู้ขับขี่อายุน้อยกว่า 5 ปี หรือมากกว่า 100 ปี ไม่ควรใช้โทรศัพท์ (risk5 = 1)
            if ($row->injp == '2' && $row->risk5 == '1') {
                if ($row->age < 5 || $row->age > 100) {
                    $this->addCases(7, $row_id, $row);
                }
            }

            // 8. ความสอดคล้องระหว่างอายุและ car seat
            // อายุมากกว่า 6 ปี ไม่ควรใช้ car seat (risk3 = 2)
            if ($row->age > 6 && $row->risk3 == '2') {
                $this->addCases(8, $row_id, $row);
            }

            // 9. ความสอดคล้องระหว่างอายุและอาชีพ
            // < 3 ปี ต้องเป็น "เด็กในปกครอง"
            if ($row->age < 3) {
                if (trim($row->occu) !== '17') { // 17 = "เด็กในปกครอง"
                    $this->addCases(9, $row_id, $row);
                }
            }

            // อายุ 3-14 ปี ต้องห้ามกรอก "ไม่มีอาชีพ"
            if ($row->age >= 3 && $row->age <= 14) {
                if (trim($row->occu) === '00') {
                    $this->addCases(9, $row_id, $row);
                }
            }

            // 10. อายุ <= 5 ปี ไม่ควรทำร้ายตนเอง (injby = 2)
            if ($row->age <= 5 && $row->injby == '2') {
                $this->addCases(10, $row_id, $row);
            }

            // 11. อายุไม่ควรเกิน 130 ปี
            if ($row->age > 130) {
                $this->addCases(11, $row_id, $row);
            }

            // 12. ถ้า apoint ขึ้นต้นด้วย 5 (เช่น 5, 503) then icdcause ต้องขึ้นต้นด้วย V/W/X/Y
            $apointPrefix = substr(trim((string) $row->apoint), 0, 1);
            $icdPrefix = strtoupper(substr(trim((string) $row->icdcause), 0, 1));
            if ($apointPrefix === '5' && !in_array($icdPrefix, ['V', 'W', 'X', 'Y'])) {
                $this->addCases(12, $row_id, $row);
            }

            // 13. จุดเกิดเหตุในบ้าน (apoint = 0) ควรเป็นการทำร้ายตนเองหรือผู้อื่นทำร้าย (injby = 2 หรือ 3)
            if ($row->apoint == '0' && !in_array($row->injby, ['2', '3'])) {
                $this->addCases(13, $row_id, $row);
            }

            // 14. จมน้ำ (icdcause = W65-W74) จุดเกิดเหตุควรเป็นน้ำ (apoint = 5)
            if (self::checkICD10InRange($row->icdcause, "W65", "W74") && $row->apoint != '5') {
                $this->addCases(14, $row_id, $row);
            }

            // 15. การบาดเจ็บจากการทำร้ายตนเอง/ผู้อื่น/บังเอิญ (injby = 2,3,4) ไม่ควรเป็น cause 1 (อุบัติเหตุ)
            if (in_array($row->injby, ['2', '3', '4']) && $row->cause == '1') {
                $this->addCases(15, $row_id, $row);
            }



            // 16. ถ้ามี injby มา ควรมี icdcause
            if (!self::checkEmpty($row->injby) && self::checkEmpty($row->icdcause)) {
                $this->addCases(16, $row_id, $row);
            }

            // 17. ถ้ามี injfrom มา ควรมี icdcause
            if (!self::checkEmpty($row->injfrom) && self::checkEmpty($row->icdcause)) {
                $this->addCases(17, $row_id, $row);
            }

            // 18. หมายเลขโทรศัพท์ควรมี 9 หรือ 10 หลัก
            if (!self::checkEmpty($row->tel)) {
                $digits = preg_replace('/\D/', '', $row->tel);
                if (!in_array(strlen($digits), [9, 10])) {
                    $this->addCases(18, $row_id, $row);
                }
            }

            // 19. ควรระบุอาชีพ
            if (self::checkEmpty($row->occu)) {
                $this->addCases(19, $row_id, $row);
            }

            // 20. cause 2 ต้องมี icdcause / cause 1 ต้องมี injt หรือ injp
            if ($row->cause == '2' && self::checkEmpty($row->icdcause)) {
                $this->addCases(20, $row_id, $row);
            } elseif ($row->cause == '1' && (self::checkEmpty($row->injt) || self::checkEmpty($row->injp))) {
                $this->addCases(20, $row_id, $row);
            }

            // 21. บาดเจ็บจากการทำงาน (injoccu = 1) ต้องมีอาชีพ
            if ($row->injoccu == '1' && self::checkEmpty($row->occu)) {
                $this->addCases(21, $row_id, $row);
            }

            // 22. นักท่องเที่ยว (home = 4) ไม่ควรบาดเจ็บจากการทำงาน (injoccu = 1)
            if ($row->home == '4' && $row->injoccu == '1') {
                $this->addCases(22, $row_id, $row);
            }

            // 23. vehicle2 ต้องไม่ล้มเอง (injform ≠ 3)
            if (!self::checkEmpty($row->vehicle2) && $row->injform == '3') {
                $this->addCases(23, $row_id, $row);
            }

            // 24. pmi = 3 ต้องมีรหัสรพ.ที่ส่งต่อ (htohosp)
            if ($row->pmi == '3' && self::checkEmpty($row->htohosp)) {
                $this->addCases(24, $row_id, $row);
            }

            // 25. ทุกรายควรมี atohosp และถ้า atohosp = 3 ต้องมี EMS ในกลุ่ม 1-4
            if (self::checkEmpty($row->atohosp)) {
                $this->addCases(25, $row_id, $row);
            } elseif ($row->atohosp == '3' && !in_array($row->ems, ['1', '2', '3', '4'])) {
                $this->addCases(25, $row_id, $row);
            }

            // 26. airway = 2 ต้องกรอก airway_t
            if ($row->airway == '2' && self::checkEmpty($row->airway_t)) {
                $this->addCases(26, $row_id, $row);
            }

            // 27. blood = 2 ต้องกรอก blood_t
            if ($row->blood == '2' && self::checkEmpty($row->blood_t)) {
                $this->addCases(27, $row_id, $row);
            }

            // 28. splintc = 2 ต้องกรอก splntc_t
            if ($row->splintc == '2' && self::checkEmpty($row->splntc_t)) {
                $this->addCases(28, $row_id, $row);
            }

            // 29. splint = 2 ต้องกรอก splint_t
            if ($row->splint == '2' && self::checkEmpty($row->splint_t)) {
                $this->addCases(29, $row_id, $row);
            }

            // 30. iv = 2 ต้องกรอก iv_t
            if ($row->iv == '2' && self::checkEmpty($row->iv_t)) {
                $this->addCases(30, $row_id, $row);
            }

            // 31. ความสอดคล้องระหว่างเข็มขัดนิรภัยและพาหนะ
            $seatbeltVehicles = ['04', '041', '05', '06', '07', '08', '09', '10', '18', '181', '182', '19', '191', '192'];
            if (!self::checkEmpty($row->injt) && in_array($row->injt, $seatbeltVehicles)) {
                if (!self::checkEmpty($row->risk3) && $row->risk3 == '1') {
                    // ถ้าใช้เข็มขัดแต่พาหนะไม่อยู่ในกลุ่มรถยนต์ ไม่ต้องสนใจ
                    // เงื่อนไขนี้ใช้ได้เฉพาะในกลุ่มที่กำหนด
                    return;
                }
            }

            // 32. ความสอดคล้องระหว่างหมวกนิรภัยและพาหนะ
            $helmetVehicles = ['02', '021', '022', '023'];
            if (!self::checkEmpty($row->injt) && in_array($row->injt, $helmetVehicles)) {
                if (!self::checkEmpty($row->risk4) && $row->risk4 == '1') {
                    // ถ้าใส่หมวกนิรภัย แต่ไม่ใช่จักรยานยนต์ ไม่ต้องสนใจ
                    return;
                }
            }

            // 33. ความสอดคล้องระหว่างประเภทผู้บาดเจ็บ เข็มขัดนิรภัย และหมวกนิรภัย
            if ($row->injp == '1') {
                if (!self::checkEmpty($row->risk3) || !self::checkEmpty($row->risk4)) {
                    $this->addCases(33, $row_id, $row);
                }
            }

            // 34. ความสอดคล้องระหว่างประวัติสลบตั้งแต่เกิดเหตุและเวลาการสลบ
            if ($row->hxcc == '1') {
                if (!self::checkEmpty($row->hxcc_hr) || !self::checkEmpty($row->hxcc_min)) {
                    $this->addCases(34, $row_id, $row);
                }
            }

            // 35. Diag1 - Diag6 ต้องมีอย่างน้อย 1 ค่า
            if (
                self::checkEmpty($row->diag1) &&
                self::checkEmpty($row->diag2) &&
                self::checkEmpty($row->diag3) &&
                self::checkEmpty($row->diag4) &&
                self::checkEmpty($row->diag5) &&
                self::checkEmpty($row->diag6)
            ) {
                $this->addCases(35, $row_id, $row);
            }

            // 36. br1 - br6 ต้องมีอย่างน้อย 1 ค่า
            if (
                self::checkEmpty($row->br1) &&
                self::checkEmpty($row->br2) &&
                self::checkEmpty($row->br3) &&
                self::checkEmpty($row->br4) &&
                self::checkEmpty($row->br5) &&
                self::checkEmpty($row->br6)
            ) {
                $this->addCases(36, $row_id, $row);
            }

            // 37. ais1 - ais6 ต้องมีอย่างน้อย 1 ค่า
            if (
                self::checkEmpty($row->ais1) &&
                self::checkEmpty($row->ais2) &&
                self::checkEmpty($row->ais3) &&
                self::checkEmpty($row->ais4) &&
                self::checkEmpty($row->ais5) &&
                self::checkEmpty($row->ais6)
            ) {
                $this->addCases(37, $row_id, $row);
            }

            // 38. ค่า PS = 0.9 ไม่ควรตาย (ตรวจ cause_t และสถานะตาย)
            $ps = floatval($row->cause_t);
            if ($ps == 0.9) {
                if (
                    in_array($row->staer, ['1', '6']) ||
                    $row->staward == '5' ||
                    $row->pmi == '1' ||
                    in_array($row->refer_result, ['04', '05']) ||
                    $row->late_effect == 'DEAD'
                ) {
                    $this->addCases(38, $row_id, $row);
                }
            }

            // 39. ค่า ISS ต้องไม่น้อยกว่า 1
            $iss_inputs = [
                $row->br1,
                $row->br2,
                $row->br3,
                $row->br4,
                $row->br5,
                $row->br6,
                $row->ais1,
                $row->ais2,
                $row->ais3,
                $row->ais4,
                $row->ais5,
                $row->ais6,
            ];
            $has_unknown_iss_input = false;
            foreach ($iss_inputs as $value) {
                if ($value === 9 || $value === '9') {
                    $has_unknown_iss_input = true;
                    break;
                }
            }

            if (!$has_unknown_iss_input && !self::checkEmpty($row->iss)) {
                $iss = intval($row->iss);
                if ($iss < 1) {
                    $this->addCases(39, $row_id, $row);
                }
            }

            // 40. ความสมบูรณ์ของสถานะการบาดเจ็บ/เสียชีวิต
            $staer = intval($row->staer);
            $staward = intval($row->staward);
            $pmi = intval($row->pmi);
            $refer_result = trim($row->refer_result ?? '');
            $late_effect = trim($row->late_effect ?? '');

            $valid = false;

            if (in_array($staer, [1, 6, 2, 3, 4, 5, 7])) {
                $valid = true;
            }
            if (in_array($staward, [1, 2, 3, 4, 5, 6])) {
                $valid = true;
            }
            if (in_array($refer_result, ['02', '03', '04', '05', '06'])) {
                $valid = true;
            }
            if ($pmi === 1) {
                $valid = true;
            }
            if ($late_effect === 'DEAD') {
                $valid = true;
            }

            if (!$valid) {
                $this->addCases(40, $row_id, $row);
            }

            // //2. เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสทำร้ายตนเอง ซึ่งมีรหัสเป็น Injby = 2
            // //เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสทำร้ายตนเอง(ICD=X60-X84)  และมีรหัสการบาดเจ็บ(Injby) เท่ากับ 2
            // if ($row->age <= 5) {
            //     if ($row->injby == 2 || self::checkICD10InRange($row->icdcause, "X60", "X84")) {
            //         $this->addCases(2, $row_id, $row);
            //     }
            // }

            // //3. พฤติกรรมเสี่ยงจากการใช้โทรศัพท์มือถือ จะต้องมีเป็นรหัส Risk 5 ทุกราย
            // //ถ้ามีการบาดเจ็บเกิดโดย อุบัติเหตุขนส่ง  เท่ากับ 1 (Injby=1) หรือ สาเหตุการบาดเจ็บ เท่ากับ 1 (CAUSE = 1)
            // //หรือ สาเหตุของอุบัติเหตุและการบาดเจ็บตาม ICD 10 จะต้องมีความเสี่ยง เท่ากับ V00 - V89 ()
            // //(Risk5) ไม่เท่ากับค่าว่าง
            // if (
            //     (self::checkICD10InRange($row->icdcause, "V00", "V89") || $row->injby == 1 ||  $row->cause == 1)
            //     && self::checkEmpty($row->risk5)
            // ) {
            //     $this->addCases(3, $row_id, $row);
            // }



            // //4. ICDcause จะต้องมีรหัสทุกราย และยกเว้นอุบัติเหตุจากการขนส่งที่ไม่ต้องมีรหัส เพราะใช้ cause = 1 แทนแล้ว
            // //สาเหตุของอุบัติเหตุและการบาดเจ็บตาม ICD 10 จะต้องระบุทุกราย ICDcause <>  " "  ยกเว้น สาเหตุการบาดเจ็บ ต้องไม่เท่ากับ 1 (CAUSE <> 1)
            // if ((self::checkEmpty($row->icdcause) && ($row->cause) != 1)) {
            //     $this->addCases(4, $row_id, $row);
            // }

            // //5. มาจากที่เกิดเหตุ (Atohosp) จะต้องมีรหัสทุกราย และหากเดินทางมาโดยหน่วยบริการการแพทย์ฉุกเฉิน (EMS) ให้ระบุระดับ ALS, BLS, หรือ FR  ด้วยเสมอ
            // //มาจากที่เกิดเหตุ (Atohosp) จะต้องมีรหัสทุกราย และหากเดินทางมาโดยหน่วยบริการการแพทย์ฉุกเฉิน (Atohosp=3) ให้ระบุระดับ(EMS) = ALS, BLS, หรือ FR  ด้วยเสมอ
            // if (($row->atohosp == 3 && self::checkEmpty($row->ems)) ||
            //     ($row->atohosp == 3 && !in_array($row->ems, [1, 2, 3]))
            // ) {
            //     $this->addCases(5, $row_id, $row);
            // }

            // //6.เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสพฤติกรรมเสี่ยงที่เป็นการดื่มแอลกอฮอล์
            // //เด็กอายุน้อยกว่าหรือเท่ากับ 5 ปี จะต้องไม่มีรหัสพฤติกรรมเสี่ยงที่เป็นการดื่มแอลกอฮอล์(RISK1) ไม่เท่ากับ 1
            // if (
            //     $row->age < 5 && $row->risk1 == 1
            // ) {
            //     $this->addCases(6, $row_id, $row);
            // }

            // //7. จะต้องไม่มีรหัสบาดเจ็บในอาชีพจากการทำร้ายตนเอง Injby = 2
            // //หากมีการบาดเจ็บจากการทำงานในอาชีพ (injoccu=1)

            // if (
            //     $row->injoccu == 1 && $row->injby == 2
            // ) {
            //     $this->addCases(7, $row_id, $row);
            // }

            // //8. DBA จะต้องมีรหัสการนำส่งทุกราย STAER == 1 and Atohosp == 0
            // if (
            //     $row->staer == 1 && $row->atohosp == 0
            // ) {
            //     $this->addCases(8, $row_id, $row);
            // }

            // // 9. อุบัติเหตุตกน้ำ จมน้ำ ICDcause จะเป็นรหัส W65-W74 และจุดเกิดเหตุจะต้องไม่มีรหัสที่เป็นบริเวณถนน รหัส คือ Apoint = 5 หรือเริ่มต้นด้วย 5 (เช่น 501, 502, 503)
            // if (
            //     preg_match('/^5/', $row->apoint) &&
            //     self::checkICD10InRange($row->icdcause, "W65", "W74")
            // ) {
            //     $this->addCases(9, $row_id, $row);
            // }

            // //10. บาดเจ็บจากอาชีพ รหัส คือ injoccu จะต้องมีรหัสการบันทึกอาชีพ (occu) ทุกราย
            // if ($row->injoccu == 1 && self::checkEmpty($row->occu)) {
            //     $this->addCases(10, $row_id, $row);
            // }

            // //11. ผู้บาดเจ็บ ที่มี br = 6, ais = 1 แล้ว และไม่สมควรเสียชีวิต หากเสียชีวิตควรมีเหตุผลแนบ ดังนี้
            // //1. จำหน่ายผู้ป่วยออกจาก ER (staer = 1 : เสียชีวิตก่อนถึง รพ., 6 : ถึงแก่กรรม) และ
            // //2. จำหน่ายผู้ป่วยออกจาก ward (staward = 5 : ถึงเเก่กรรม)
            // if (in_array($row->staer, [1, 6]) || $row->staward == 5) {
            //     $isHighInjury = false;
            //     for ($i = 1; $i <= 6; $i++) {
            //         if (!self::checkEmpty($row->{"br$i"})) {
            //             if ($row->{"br$i"} != 6 && $row->{"ais$i"} != 1) {
            //                 $isHighInjury = true;
            //             }
            //         }
            //     }
            //     if ($isHighInjury == false) {
            //         $this->addCases(11, $row_id, $row);
            //     }
            // }

            // //12. ข้อมูลเพศจะต้องมีรหัส คือ sex : 1 = ชาย และ 2 = หญิง ทุกราย
            // if (self::checkEmpty($row->sex)) {
            //     $this->addCases(12, $row_id, $row);
            // }

            // //13. bp, rr, p ไม่ควรเกินกว่ากำหนด (bp1 ไม่เกิน 300, bp2 ไม่เกิน 300, pr ไม่เกิน 200, rr ไม่เกิน 60)
            // // และไม่ควรมีค่าว่าง
            // if (
            //     ($row->bp1 > 300  &&  $row->bp1 < 998) ||
            //     ($row->bp1 >= 1000)  ||
            //     ($row->bp2 > 300 &&  $row->bp2 < 998) ||
            //     ($row->bp2 >= 1000)  ||
            //     ($row->pr > 200) ||
            //     ($row->rr > 60 && $row->rr <= 98) ||  $row->rr >= 100
            // ) {
            //     $this->addCases(13, $row_id, $row);
            // }

            // //14. จำหน่ายผู้ป่วยออกแล้ว (staward) และจะต้องมีวันที่จำหน่าย (rdate)
            // //   != 6
            // if ((!self::checkEmpty($row->staward) && $row->staward != 6) &&
            //     // if (($row->staward <= 1 || $row->staward <= 6) &&
            //     self::checkEmpty($row->rdate)
            // ) {
            //     $this->addCases(14, $row_id, $row);
            // }

            // //15.อายุเป็น ปี (age) หรือเดือน (month) หรือวัน (day)
            // // จะต้องมีรหัสทุกราย และอายุ จะต้องไม่เกิน 130 ปี และต้องมีรหัส AGE (อายุ) ทุกราย
            // // จะต้องไม่มีค่าว่าง ยกเว้น เคส DBA DER

            // if ($row->age > 130) {
            //     $this->addCases(15, $row_id, $row);
            // } else {
            //     if (($row->month == 0 && $row->age == 0) ||  (self::checkEmpty($row->age) && self::checkEmpty($row->month))) {
            //         if (self::checkEmpty($row->day)) {
            //             $this->addCases(15, $row_id, $row);
            //         }
            //     }
            // }

            // //25. คำนำหน้า (PRENAME) และ อาชีพ (OCCU) (พระ)  จะต้องมีรหัสที่สอดคล้องกัน

            // if ($this->checkWordInArray($row->prename, $this->MonkFrontname) == true) {
            //     if ($row->occu != 9) {
            //         $this->addCases(25, $row_id, $row);
            //     }
            // } else {
            //     if ($row->occu == 9) {
            //         $this->addCases(25, $row_id, $row);
            //     }
            // }

            // //26 ตัวแปร คำนำหน้า(PRENAME) และ อาชีพ (OCCU) (เด็ก) ต้องคลองจองกัน
            // if ($row->age < 15) {
            //     if ($row->age <= 3 and $row->occu != 17) {
            //         $this->addCases(26, $row_id, $row);
            //     } else if ($row->age > 3 and $row->occu == 00) {
            //         $this->addCases(26, $row_id, $row);
            //     } else if (!$this->checkWordInArray($row->prename, $this->kidFrontName)) {
            //         $this->addCases(26, $row_id, $row);
            //     }
            // }

            // //27. ตัวแปร INJOCCU ถ้าตอบ 1 จะเป็นเกิดจากอาชีพ และจะต้องมีรหัสอาชีพ (OCCU) ที่ไม่มีค่าว่าง
            // if (
            //     $row->injoccu == 1 && $row->age < 65   &&
            //     (self::checkEmpty($row->occu) || $row->occu == 0)
            // ) {
            //     $this->addCases(27, $row_id, $row);
            // }

            // //28. วันที่เกิดเหตุ (ADATE), เวลาเกิดเหตุ (ATIME), วันที่มารพ. (HDATE), เวลาที่มารพ. (HTIME) จะต้องมีรหัสครบถ้วนทุกราย
            // if (
            //     self::checkEmpty($row->adate) ||
            //     self::checkEmpty($row->atime) ||
            //     self::checkEmpty($row->hdate) ||
            //     self::checkEmpty($row->htime)
            // ) {
            //     $this->addCases(28, $row_id, $row);
            // }

            // //29. จุดเกิดเหตุจะต้องมีรหัส apoint ทุกราย
            // if (self::checkEmpty($row->apoint)) {
            //     $this->addCases(29, $row_id, $row);
            // }

            // //30. ตัวแปร APOINT จะต้องเป็นไปตามคู่มือ ถ้ามีรหัสนอกเหนือจากนั้น จะแจ้งรพ.เจ้าของข้อมูลทราบ
            // if (
            //     ($row->apoint < 1  && $row->apoint == 10)  || ($row->apoint > 17)
            // ) {
            //     $this->addCases(30, $row_id, $row);
            // }

            // //31. บาดเจ็บจากอุบัติเหตุรหัสเป็น Injby = 1 แต่สาเหตุการบาดเจ็บจะต้องไม่มีรหัสเป็นเจตนา icdcause = X60-Y09
            // if (
            //     $row->injby == 1 && self::checkICD10InRange($row->icdcause, "X60", "Y09")
            // ) {
            //     $this->addCases(31, $row_id, $row);
            // }

            // //32. บาดเจ็บจากการทำร้ายตนเองรหัสเป็น Injby = 2  และสาเหตุการบาดเจ็บจะต้องไม่มีรหัสทำร้ายตนเอง (ICD cause <> X60-X84)
            // if (!self::checkEmpty($row->caues)) {
            //     if (
            //         $row->injby == 2  &&   $row->cause == 2 && self::checkICD10InRange($row->icdcause, "X60", "X84")
            //     ) {
            //         $this->addCases(32, $row_id, $row);
            //     }
            // }


            // //33. บาดเจ็บจากการทำร้ายตนเองรหัสเป็น(Injby =3) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัสถูกทำร้ายด้วยวิธีต่าง ๆ (ICDcause = X85-Y09)
            // if (!self::checkEmpty($row->caues)) {
            //     if (
            //         $row->injby == 3 && self::checkICD10InRange($row->icdcause, "X85", "Y09")
            //     ) {
            //         $this->addCases(33, $row_id, $row);
            //     }
            // }


            // //34. บาดเจ็บจากภัยทางสงคราม (Injby =4) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัส ICDcause = Y35 – Y36
            // if (!self::checkEmpty($row->caues)) {
            //     if (
            //         $row->injby == 4 && ($row->cause == 2 || self::checkICD10InRange($row->icdcause, "Y35", "Y36"))
            //     ) {
            //         $this->addCases(34, $row_id, $row);
            //     }
            // }


            // //35. บาดเจ็บจากไม่ทราบ (Injby =  N) และสาเหตุการบาดเจ็บจะต้องไม่มีรหัส ICDcause = Y10 – Y34
            // if (
            //     strtoupper($row->injby) == "N" && ($row->cause != 2 || !self::checkICD10InRange($row->icdcause, "Y10", "Y34"))
            // ) {
            //     $this->addCases(35, $row_id, $row);
            // }

            // //36. บาดเจ็บในอาชีพจะต้องไม่มีรหัส Injby = 2 คือ การทำร้ายตนเอง
            // if (
            //     $row->injoccu == 1 && $row->injby == 2
            // ) {
            //     $this->addCases(36, $row_id, $row);
            // }

            // //37. CAUSE 1 และ 2 ICDCAUSE  จะต้องไม่มีรหัสว่าง
            // if (
            //     $row->injby != 1 &&
            //     in_array($row->cause, [1, 2]) && self::checkEmpty($row->icdcause)
            // ) {
            //     $this->addCases(37, $row_id, $row);
            // }

            // //38. ขับขี่รถจักรยานยนต์ จะต้องมีข้อมูลพฤติกรรมเสี่ยงเรื่องหมวกนิรภัย RISK4
            // if (
            //     $row->injp != 2 && ($row->injt == 2 && self::checkEmpty($row->risk4))
            // ) {
            //     $this->addCases(38, $row_id, $row);
            // }

            // //39. คนเดินเท้าจะต้องมีรหัสบาดเจ็บจากพาหนะอื่นด้วย รหัสคือ INJFROM
            // if (
            //     $row->injp == 1 && self::checkEmpty($row->injfrom)
            // ) {
            //     $this->addCases(39, $row_id, $row);
            // }

            // //40. ค่า COMA ตัวแปร E, V และ M จะต้องไม่มีรหัสว่าง
            // if ((!self::checkEmpty($row->coma) && ($row->coma < 3 || $row->coma > 15))  ||
            //     (
            //         ($row->e < 1 || $row->e > 4) ||
            //         ($row->v < 1 || $row->v > 5) ||
            //         ($row->m < 1 || $row->m > 6)
            //     )
            // ) {
            //     $this->addCases(40, $row_id, $row);
            // }

            //41. ตรวจสอบลักษณะการบาดเจ็บจะต้อมีรหัส TINJ ทุกราย
            if (self::checkEmpty($row->tinj)) {
                $this->addCases(41, $row_id, $row);
            }

            $this->type_1 = array_unique($this->type_1); //ความสมบูรณ์ (Completeness)
            $this->type_2 = array_unique($this->type_2); // ความสอดคล้อง (Consistency)
        } catch (\Exception $error) {
            dd($error);
        }
    }





    public function case_1_test($icdcause, $injby)
    {
        //1. อุบัติเหตุจากการขนส่ง รหัส V01-X59 และการบาดเจ็บ Injby จะต้องไม่มีรหัส 2 คือ
        //      ทำร้ายตนเอง (X60-X84),
        //      3 คือ ผู้อื่นทำร้าย  (X85-Y09) และ
        //      4 คือ ปฏิบัติทางกฏหมาย/สงคราม/สถานการณ์ (Y35-Y36)
        //
        //  dd($icdcause, $injby);

        if (self::checkICD10InRange($icdcause, "V01", "V89")) {
            echo '111';
            if ((int)$injby != 1) {
                echo '111';
            }
        }
        if (self::checkICD10InRange($icdcause, "X00", "X59")) {
            echo '222';
            if ((int)$injby != 1) {
                echo '222';
            }
        }
        if (self::checkICD10InRange($icdcause, "X60", "X84")) {
            echo '333';
            if ((int)$injby != 2) {
                echo '333';
            }
        }
        if (self::checkICD10InRange($icdcause, "X85", "Y99")) {
            echo '444';
            if ((int)$injby != 3) {
                echo '444';
            }
        }
        if (self::checkICD10InRange($icdcause, "Y00", "Y09")) {
            echo '555';
            if ((int)$injby != 3) {
                echo '555';
            }
        }
        // Update in meeting 7 9 2023
        if (self::checkICD10InRange($icdcause, "Y10", "Y34")) {
            echo '666';
            if ((int)$injby != 'N') {
                echo '666';
            }
        }
        if (self::checkICD10InRange($icdcause, "Y35", "Y36")) {
            echo '777';
            if ((int)$injby != 4) {
                echo '777';
            }
        }
        //  Add 3/10/65 By Anong
        if (self::checkICD10InRange($icdcause, "Y33", "Y34")) {
            echo '888';
            if ((int)$injby != 'N') {
                echo '888';
            }
        }
    }
    //                                          W56     V11     -   X59
    public static function checkICD10InRange($icd10, $icd10_start, $icd10_end)
    {

        // ตัด ICD-10 ให้เหลือ 3 หลัก
        $icd10 = self::trimICD10($icd10);
        $icd10_start = self::trimICD10($icd10_start);
        $icd10_end = self::trimICD10($icd10_end);

        // แปลงตัวอักษรใน ICD-10 เป็นเลขเพื่อเปรียบเทียบ
        $icd10 = self::convertICD10ToNumber($icd10);
        $icd10_start = self::convertICD10ToNumber($icd10_start);
        $icd10_end = self::convertICD10ToNumber($icd10_end);

        // ตรวจสอบว่าค่า ICD-10 อยู่ในช่วงที่กำหนด
        return ($icd10 >= $icd10_start && $icd10 <= $icd10_end);
    }

    private static function trimICD10($icd10)
    {
        // ตัด ICD-10 ให้เหลือเพียง 3 หลัก (ถ้ามีมากกว่า 3 หลัก)
        return substr($icd10, 0, 3);
    }


    private static function convertICD10ToNumber($icd10)
    {
        // แยกตัวอักษรและตัวเลขใน ICD-10
        $letters = substr($icd10, 0, 1);  // ตัวอักษร
        $numbers = substr($icd10, 1);     // ตัวเลข

        // แปลงตัวอักษรเป็นตัวเลข
        $lettersToNumber = ord($letters) - ord('A'); // 'A' จะเริ่มที่ 0, 'B' จะเป็น 1, ...

        // รวมค่าของตัวอักษรและตัวเลข
        return $lettersToNumber * 1000 + (int)$numbers;
    }



    // //                                          W56     V11     -   X59
    // public static function checkICD10InRange($icd10, $icd10_start, $icd10_end)
    // {
    //     $icd10_start = strtoupper($icd10_start);
    //     $icd10_end = strtoupper($icd10_end);
    //     //  V W X
    //     $alphas = range($icd10_start[0], $icd10_end[0]);
    //     $max_loop = count($alphas); // 3

    //     $char_1_start = $icd10_start[1];
    //     $char_2_start = $icd10_start[2];

    //     $char_1_end = $icd10_end[1];
    //     $char_2_end = $icd10_end[2];

    //     if (self::checkEmpty($icd10)) {
    //         return false;
    //     }

    //     $lengh = strlen($icd10);
    //     $icd10 = str_split(strtoupper($icd10));

    //     //


    //     $main = "";
    //     $char_1 = 0;
    //     $char_2 = 0;

    //     if ($lengh >= 1) {
    //         $main = $icd10[0];    // W
    //     }
    //     if ($lengh >= 2) {
    //         $char_1 = $icd10[1];  // 5
    //     }
    //     if ($lengh >= 3) {
    //         $char_2 = $icd10[2]; // 6
    //     }
    //     $index = 1;
    //     foreach ($alphas as $alpha) {
    //         if ($alpha == $main) {
    //             //check min start
    //             if ($index == 1) {
    //                 //   1           1
    //                 if ($char_1 < $char_1_start) {
    //                     return false;
    //                 }

    //                 //   0            1
    //                 if ($char_2 < $char_2_start) {
    //                     return false;
    //                 }

    //                 return true;
    //             } else if ($index == $max_loop) {

    //                 //   1           1
    //                 if ($char_1 > $char_1_end) {
    //                     return false;
    //                 }

    //                 //   0            1
    //                 if ($char_2 > $char_2_end) {
    //                     return false;
    //                 }
    //                 return true;
    //             } else {

    //                 // The match of main char is between start and end
    //                 // Don't have to check lower and upper icd number

    //                 return true;
    //             }
    //         }
    //         $index++;
    //     }
    //     return false;
    // }

    public static function checkEmpty($value)
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && trim($value) === '') {
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

            $this->case_array[$key_case]["is_ids"] ??= [];
            $this->case_array[$key_case]["is_datas"] ??= [];

            if (!isset($this->case_array[$key_case]["error_type"])) {
                $this->case_array[$key_case]["error_type"] = "type_1";
            }

            array_push($this->case_array[$key_case]["is_ids"], $row_id);
            array_push($this->case_array[$key_case]["is_datas"], $row);
            array_push($this->{$this->case_array[$key_case]["error_type"]}, $row_id);
        }
    }



    //func เช็คคำใน array (normalize input: lowercase, trim whitespace and periods)
    function checkWordInArray($word, $arrays = array())
    {
        $word = strtolower(trim($word, " ."));
        foreach ($arrays as $array) {
            $cleaned = strtolower(trim($array, " ."));
            if ($word === $cleaned) {
                return true;
            }
        }
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
        $result->type_1P = $this->get_percentage($total, sizeof($this->type_1)); //ถูกกี่ % ส่งค่าที่ผิด กับ ค่ารวม ไป จะได้ค่าที่ถูก % คืนมา
        $result->type_2P = $this->get_percentage($total, sizeof($this->type_2));
        $result->save();
    }

    function resetValue()
    {
        $this->type_1 = []; //รีค่า
        $this->type_2 = [];
        $this->case_array = [];
    }
}
