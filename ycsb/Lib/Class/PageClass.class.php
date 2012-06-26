<?php
/**
 * 分页类
 *
 * @package
 * @version
 * @copyright  2010 The HunanTV.
 * @copyright
 * @author hjj <hjj@hunantv.com> 2010-8-12
 * @update     gettyying@gmail.com  2011-04-29
 */
class PageClass
{
        /**
  * config ,public
  */
    var $page_name="";//page标签，用来控制url页。
    var $next_page='>';//下一页
    var $pre_page='<';//上一页
    var $first_page='First';//首页
    var $last_page='Last';//尾页
    var $pre_bar='<<';//上一分页条
    var $next_bar='>>';//下一分页条
    var $format_left='';
    var $format_right='';
    var $condition='';
    var $is_ajax=false;//是否支持AJAX分页模式
    var $type = false;//支持前后台0默认1应用在后台
    public $link_type = 0;


        /**
  * private
  *
  */
    var $pagebarnum=8;//控制记录条的个数。
    var $totalpage=0;//总页数
    var $ajax_action_name='';//AJAX动作名
    var $nowindex=1;//当前页
    var $url="";//url地址头
    var $offset=0;

        /**
  * constructor构造函数
  *
  * @param array $array['total'],$array['perpage'],$array['nowindex'],$array['url'],$array['ajax']...
  */
        function page($array) {
            if(is_array($array)){
                if(!array_key_exists('total',$array))$this->error(__FUNCTION__,'need a param of total');
                $total=intval($array['total']);
                $perpage=(array_key_exists('perpage',$array))?intval($array['perpage']):10;
                $nowindex=(array_key_exists('nowindex',$array))?intval($array['nowindex']):'';
                $url=(array_key_exists('url',$array))?$array['url']:'';
                $this->condition = $array['condition'] ? '?'.http_build_query($array['condition']) : '';
            } else {
                $total=$array;
                $perpage=10;
                $nowindex='';
                $url='';
            }
            if((!is_int($total))||($total<0))$this->error(__FUNCTION__,$total.' is not a positive integer!');
            if((!is_int($perpage))||($perpage<=0))$this->error(__FUNCTION__,$perpage.' is not a positive integer!');
            if(!empty($array['page_name']))$this->set('page_name',$array['page_name']);//设置pagename
            $this->pagedata = $array;
            $this->_set_nowindex($nowindex);//设置当前页
            $this->_set_url($url);//设置链接地址
            $this->totalpage=ceil($total/$perpage);
            $this->offset=($this->nowindex-1)*$perpage;
        if ($array['type']) $this->type=true;
                if(!empty($array['ajax']))$this->open_ajax($array['ajax']);//打开AJAX模式
                if(!empty($array['link_type']))$this->link_type = $array['link_type'];//打开AJAX模式
        }
        /**
  * 设定类中指定变量名的值，如果改变量不属于这个类，将throw一个exception
  *
  * @param string $var
  * @param string $value
  */
        function set($var,$value)
        {
                if(in_array($var,get_object_vars($this))){
                $this->$var=$value;
        }else {
                        $this->error(__FUNCTION__,$var." does not belong to PB_Page!");
                }

        }

    function locationPage()
    {
        $html = '<input class="input_blur" name="page" id="page" size="5" onkeydown="if(event.keyCode==13) {location.href=(\''.$this->url.'\'+this.value + '.'\''.$this->condition.'\'); return false;}" type="text">';
        //$html = '<input class="input_blur" name="page" id="page" size="5" onkeydown="if(event.keyCode==13) {location.href=(\''.$this->url.'\'+this.value); return false;}" type="text">';
        return $html;

    }
        /**
  * 打开倒AJAX模式
  *
  * @param string $action 默认ajax触发的动作。
  */
        function open_ajax($action)
        {
                $this->is_ajax=true;
                $this->ajax_action_name=$action;
        }
        /**
  * 获取显示"下一页"的代码
  *
  * @param string $style
  * @return string
  */
        function next_page($style='')
        {
                if($this->nowindex<$this->totalpage)
                {
                        return $this->_get_link($this->_get_url($this->nowindex+1),$this->next_page,$style);
                }
                return '<span style="display:none">'.$this->next_page.'</span>';
        }

        /**
  * 获取显示“上一页”的代码
  *
  * @param string $style
  * @return string
  */
        function pre_page($style='')
        {
                if($this->nowindex>1){
                        return $this->_get_link($this->_get_url($this->nowindex-1),$this->pre_page,$style);
                }
                return '<span style="display:none">'.$this->pre_page.'</span>';
        }

        /**
  * 获取显示“首页”的代码
  *
  * @return string
  */
        function first_page($style='')
        {
                if($this->nowindex==1){
                        return '<span class="'.$style.'">'.$this->first_page.'</span>';
                }
                return $this->_get_link($this->_get_url(1),$this->first_page,$style);
        }

        /**
  * 获取显示“尾页”的代码
  *
  * @return string
  */
        function last_page($style='')
        {
                if($this->nowindex==$this->totalpage){
                        return '<span class="'.$style.'">'.$this->last_page.'</span>';
                }
                return $this->_get_link($this->_get_url($this->totalpage),$this->last_page,$style);
        }

        function nowbar($style='',$nowindex_style='mod-bt bt-page-number tc')
        {
                $plus=ceil($this->pagebarnum/2);
                if($this->pagebarnum-$plus+$this->nowindex>$this->totalpage)$plus=($this->pagebarnum-$this->totalpage+$this->nowindex);
                $begin=$this->nowindex-$plus+1;
                $begin=($begin>=1)?$begin:1;
                $return='';
                for($i=$begin;$i<$begin+$this->pagebarnum;$i++)
                {
                        //echo $this->totalpage."aaa";
                        if($i<=$this->totalpage)
                        {
                                if($i!=$this->nowindex)
                                $return.=$this->_get_text($this->_get_link($this->_get_url($i),$i,$style));
                                //hjj只有一页记录的时候不显示
                                elseif ($this->totalpage == 1)
                                $return.=$this->_get_text('<li style="display:none;"><a href="#" class="'.$nowindex_style.'">'.$i.'</a></li>');
                                else
                                $return.=$this->_get_text('<li><a href="#" class="'.$nowindex_style.'">'.$i.'</a></li>');
                        }
                        else
                        {
                                break;
                        }
                        $return.="\n";
                }
                unset($begin);
                return $return;
        }
        /**
  * 获取显示跳转按钮的代码
  *
  * @return string
  */
        function select()
        {
                $return='<select name="PB_Page_Select">';
                for($i=1;$i<=$this->totalpage;$i++)
                {
                        if($i==$this->nowindex)
                        {
                                $return.='<option value="'.$i.'" selected>'.$i.'</option>';
                        }
                        else
                        {
                                $return.='<option value="'.$i.'">'.$i.'</option>';
                        }
                }
                unset($i);
                $return.='</select>';
                return $return;
        }

        /**
  * 获取mysql 语句中limit需要的值
  *
  * @return string
  */
        function offset()
        {
                return $this->offset;
        }

        /**
  * 控制分页显示风格（你可以增加相应的风格）
  *
  * @param int $mode
  * @return string
  */
    function show($mode=1)
    {
        switch ($mode){
            case '1':
                    //$this->next_page='<li><a class="mod-bt bt-page-next"  href="#"> </a></li>';
                    //$this->pre_page='<li><a class="mod-bt bt-page-prev"   href="#"> </a></li>';
                    return $this->pre_page('mod-bt bt-page-prev').$this->nowbar().$this->next_page('mod-bt bt-page-next');
                    break;
            case '2':
                    $this->next_page='下一页';
                    $this->pre_page='上一页';
                    $this->first_page='首页';
                    $this->last_page='尾页';
                    return $this->first_page().$this->pre_page().'[第'.$this->nowindex.'页]'.$this->next_page().$this->last_page().'第'.$this->select().'页';
                    break;
                case '3':
                        $this->next_page='下一页';
                        $this->pre_page='上一页';
                        $this->first_page='首页';
                        $this->last_page='尾页';
                        return '当前第'.$this->pagedata['nowindex'].'页  '.$this->first_page().$this->pre_page().$this->next_page().$this->last_page().'跳转到'.$this->locationPage().' 共'.$this->totalpage.'页 '.'总数：'.$this->pagedata['total'];
                        break;
                case '4':
                        $this->next_page='下一页';
                        $this->pre_page='上一页';
                        return '第'.$this->select().'页';
                        return $this->pre_page().$this->nowbar().$this->next_page().'第'.$this->select().'页';
                        break;
                case '5':
                        return $this->pre_bar().$this->pre_page().$this->nowbar().$this->next_page().$this->next_bar();
    case '6':
        return $this->pre_page().$this->nowbar('','').$this->next_page();
                        break;
            case '7':
                        $this->next_page='下一页';
                        $this->pre_page='上一页';
                        return $this->pre_page("l pre").$this->next_page(" next");
                        break;
        }
    }
        /*----------------private function (私有方法)-----------------------------------------------------------*/
        /**
  * 设置url头地址
  * @param: String $url
  * @return boolean
  * @update gettyying@gmail.com 2011-04-29 如果设定 URL 参数，则使用指定的 URL
  */
    function _set_url($url) {
    
        if (!empty($url)) {
            $this->url = $url;
        } else {
            if(stristr($_SERVER['REQUEST_URI'],$this->page_name.'/')) {
                    //地址存在页面参数
                    //$this->url=str_replace('/'.$this->page_name.'/'.$this->nowindex,'',$_SERVER['REQUEST_URI']);
                    //this->name为空的情况下这样处理hjj
                    $this->url=str_replace('/'.$this->nowindex,'',$_SERVER['REQUEST_URI']);
                    $this->url.= substr($this->url,-1) == '/'?$this->page_name:'/'.$this->page_name;
            } else {

                $this->url=$_SERVER['REQUEST_URI'].'/'.$this->page_name.'/';
            }
        }
    }

        /**
  * 设置当前页面
  *
  */
        function _set_nowindex($nowindex)
        {
                if(empty($nowindex))
                {
                        //系统获取

                        if(isset($_GET[$this->page_name])){
                                $this->nowindex=intval($_GET[$this->page_name]);
                        }
                }
                else
                {
                        //手动设置
                        $this->nowindex=intval($nowindex);
                }
        }

    /**
    * 为指定的页面返回地址值
    *
    * @param int $pageno
    * @return string $url
    */
    function _get_url($pageno=1)
    {
        
        $pageurl = $this->url.$pageno;
        if (substr($pageurl, -1) == '/')
        {
            $pageurl = substr($pageurl, 0, -1);
        }
        return $pageurl.$this->condition;
    }

        /**
  * 获取分页显示文字，比如说默认情况下_get_text('<a href="">1</a>')将返回[<a href="">1</a>]
  *
  * @param String $str
  * @return string $url
  */
        function _get_text($str)
        {
                return $this->format_left.$str.$this->format_right;
        }

        /**
   * 获取链接地址
 */
        function _get_link($url,$text,$style='')
        {
        	$style=(empty($style))?'':'class="'.$style.'"';
        	if ($this->link_type == 1) {
        		return '<div '.$style.'><a title='.$url.' href="javascript:void(0)">'.$text.'</a></div>';
        	} elseif($this->is_ajax){
        		//如果是使用AJAX模式
        		return '<li><a title='.$url.' '.$style.' href="javascript:void(0)">'.$text.'</a></li>';
        		//return '<li><a '.$style.' href="javascript:'.$this->ajax_action_name.'(\''.$url.'\')">'.$text.'</a></li>';
        	} else if ($this->type) {
        		return '<a '.$style.' href="'.$url.'">'.$text.'</a>';
        	} else{
        		return '<li><a '.$style.' href="'.$url.'">'.$text.'</a></li>';
        	}
        }
        /**
   * 出错处理方式
 */
        function error($function,$errormsg)
        {
                die('Error in file <b>'.__FILE__.'</b> ,Function <b>'.$function.'()</b> :'.$errormsg);
        }
}
?>
