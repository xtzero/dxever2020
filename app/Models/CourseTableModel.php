<?php


namespace App\Models;


use App\Models\Base\BaseModel;
use App\Support\DateSupport;
use Illuminate\Support\Facades\DB;

class CourseTableModel extends BaseModel
{
    protected $table = 'course_table';

    public function saveCourseData($openid, $courseData, $year, $studName)
    {
        if (empty($courseData)) {
            return false;
        }
        (new CourseAccountModel())->baseUpdate([
            'openid' => $openid
        ], [
            'studname' => $studName
        ]);
        $this->baseDelete([
            'year' => $year,
            'openid' => $openid
        ]);
        $nowDate = DateSupport::now();
        $sql = "insert into ". $this->table ."(`openid`, `year`, `week`, `day`, `cheapter`, `name`, `teacher`, `teacher_sign`, `classroom`, `create_time`, `is_del`)";
        $valuesArr = [];
        foreach ($courseData as $k => $v) {
            foreach ($v['weeks'] as $kk => $vv) {
                $valuesArr[] = "('{$openid}', '{$year}', {$vv}, {$v['day']}, {$v['cheapter']}, '{$v['name']}', '{$v['teacher']}', '', '{$v['classroom']}', '{$nowDate}', 0)";
            }
        }
        $sql .= ' values' . implode(',', $valuesArr) . ';';
        return DB::insert($sql);
    }

    public function getCourseData($openid, $year, $week)
    {

    }
}
