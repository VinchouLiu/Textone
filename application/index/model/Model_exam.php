<?php
/**
 * 考试模块
 */
namespace app\index\model;
use think\Db;
class Model_exam {
    private $_table = "exam";

    //获取数据
    public function getexamList($Post)
    {
        $begin = ($Post['page'] - 1) * $Post['rows'];
        $data['rows'] = db($this->_table)
            ->where(["is_delete"=>0])
            ->limit($begin,$Post['rows'])
            ->order('id desc')
            ->select();
        $data['count'] = db($this->_table)->where(["is_delete"=>0])->count();
        return $data;
    }

}