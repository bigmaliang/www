<?php
class AboutModel extends Model {
    public function getOne($type)
    {
        return $this->where(array('type'=>$type))->find();
    }
}
