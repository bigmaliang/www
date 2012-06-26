<?php
class CateModel extends Model {
    public function insert($data)
    {
        return $this->add($data);   
    }

    public function set($where, $data)
    {
        return $this->where($where)->save($data);
    }

    public function option($cate_id, $type)
    {
    	
        $mainResult = $this->where(array('cup'=>0, 'type'=>$type))->findAll();
        $option = '';
        foreach ((array)$mainResult as $key=>$value) {
            $s = $cate_id ==  $value['cate_id'] ? 'selected' : '';
            $option .= '<option value="'.$value['cate_id'].'" '.$s.'>'.$value['cate_name'];
            $sunResult = $this->where(array('cup'=>$value['cate_id']))->findAll();
            foreach ((array)$sunResult as $k=>$v) {
                $sel = $cate_id ==  $v['cate_id'] ? 'selected' : '';
                $option .= '<option value="'.$v['cate_id'].'" '.$sel.'>>>'.$v['cate_name'];
            }
        }
        return $option;
    }
    public function newsoption($cate_id)
    {
        $mainResult = $this->where(array('cup'=>0))->findAll();
        $option = '';
        foreach ((array)$mainResult as $key=>$value) {
            $s = $cate_id ==  $value['cate_id'] ? 'selected' : '';
            $option .= '<option value="'.$value['cate_id'].'" '.$s.'>'.$value['cate_name'];
            $sunResult = $this->where(array('cup'=>$value['cate_id']))->findAll();
            foreach ((array)$sunResult as $k=>$v) {
                $sel = $cate_id ==  $v['cate_id'] ? 'selected' : '';
                $option .= '<option value="'.$v['cate_id'].'" '.$sel.'>>>'.$v['cate_name'];
            }
        }
        return $option;
    }
    public function getList($where)
    {
        return $this->where($where)->findAll();
    }

    public function getBackList($where)
    {   
        $result = $this->where($where)->findAll();
        foreach ((array)$result as $key=>$value) {
            $list = $this->where(array('cup'=>$value['cate_id']))->findAll();
            $result[$key]['sunlist'] = $list;
        }
        return $result;
    }

    public function getOne($cate_id)
    {
        return $this->where(array('cate_id'=>$cate_id))->find();
    }
}
