<?php
/**
 * 在线用户模型
 * author:evila
 */

class OnlineModel extends BaseModel
{
    protected $fields = array('session_id','account_id','username','login_time','last_active_time','ip');

    /** 得到用来显示的在线用户信息 */
    public function getOnlineUser()
    {
       return $this->getFild('account_id','username');
    }

    /**
     * 刷新当前用户表
     * 默认是以30分钟容忍界限，即如果30分钟内没有进行任何活动，则删除掉，不计算在在线用户表内
     * 容忍时间界限在config.inc.php里设置
     * 公式：用户最后活动时间 + 容忍时间界限 < 当前时间 = 删除该用户在线
     * 最后，还更新当前用户的时间，因为触发此方法的条件是某用户执行了操作，所以顺便更新自己的最后活动时间
     */
    public function refreshOnlineUser()
    {
        $time_limit = intval(C('online_limit_time'));  //设置超时界限
        $cur_time = time(); //当前时间
        $this->where("last_active_time+$time_limit < $cur_time")->delete();

        $this->refreshOnlineUser();//update myself
    }

    /**
     * 更新当前用户的最后活动时间
     *
     */
    public function updateLastActiveTime()
    {
        $datas = array();
        $datas['session_id'] = session_id();
        $datas['last_active_time'] = time();
        $this->save($datas);
    }
}
?>