<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <www.3g4k.com>
// +----------------------------------------------------------------------
// $Id: SaeCounter.class.php 2504 2011-12-28 07:35:29Z liu21st $
/**
*SaeCounterģ����
*ʹ�������ݿ�洢ͳ������Ϣ��
*������ݱ�think_sae_counter
*/
class SaeCounter extends SaeObject{
	//����ͳ����
	public function create($name,$value=0){
		//�ж��Ƿ����
		if($this->exists($name)) return false;
		$setarr=array(
		'name'=>$name,
		'val'=>$value
		);
		if(M("SaeCounter")->add($setarr)){
			return true;
		}
	}
	//����
	public function decr($name,$value=1){
		if(!$this->exists($name)) return false;
		M("SaeCounter")->where("name='$name'")->setField('val',array('exp','val-'.$value));
		return M("SaeCounter")->where("name='$name'")->getField("val");
	}
	//�Ƿ����
	public function exists($name){
		$num=M("SaeCounter")->where("name='$name'")->count();
		return $num!=0?true:false;
	}
	public function get($name){
		if(!$this->exists($name)) return false;
		return M("SaeCounter")->where("name='$name'")->getField("val");
	}
	public function getall(){
		return M("SaeCounter")->getField("name,val");
	}
	//�ӷ�
	public function incr($name,$value=1){
		if(!$this->exists($name)) return false;
		M("SaeCounter")->where("name='$name'")->setField('val',array('exp','val+'.$value));
		return M("SaeCounter")->where("name='$name'")->getField("val");
	}
	public function length(){
		return M("SaeCounter")->count();
	}
	//��ö��ͳ������namesΪ����
	public function mget($names){
		$where=array(
		'name'=>array('in',$names)
		);
		return M("SaeCounter")->where($where)->getField("name,val");
	}
	public function remove($name){
		if(!$this->exists($name)) return false;
		return M("SaeCounter")->where("name='$name'")->delete();
	}
	//����ֵ
	public function set($name,$value){
		return M("SaeCounter")->where("name='$name'")->setField('val',$value);
	}
}


?>