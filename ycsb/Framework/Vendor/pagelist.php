<?php
/**
 * 分页类
 * 由于天生懒惰，没有做数据类型判断，只限内部测试使用
 * 如果你修改了本类，有新的样式，请别忘记了发一份到 evila@live.com 分享一下。
 *
 * @author evila[evila@qq.com]
 * @version 0.1 beta
 * @package evila's framework
 *
 * 需要传送的参数下如（其他内部属性不用管）：
 * $num 总记录数
 * $perpage 每页显示多少条记录
 * $curpage 当前是第几页
 * $left_limit 当前页左边最多显示几个页码 默认3
 * $right_limit 当前页右边最多显示几个页码] 默认3
 * $language 语言包 默认中文
 *
 *
 * $pagelist = new PageList(1000,20,12,'pagelist.class.php',3,3,'cn');
 * echo $pagelist->flickr();
 *
 */



class PageList
{
    public $num = 0;
    public $perpage , $curpage , $mpurl;
    public $left_limit = 3 ;
    public $right_limit = 3;
    public $language = array(
            'en' => array('first'=>'First','prev'=>'Prev','next'=>'Next','last'=>'Last','current'=>'Current : ','pages'=>' Pages'),
            'cn' => array('first'=>'首页','prev'=>'上一页','next'=>'下一页','last'=>'末页','current'=>'当前：','pages'=>' 页')
            );
    public $lang = array();

    private $total_page = 1;    //总页数
    private $form = 1 ; //列表左起页码数
    private $to = 1;    //列表右起页面数
    private $code = '';    //生成的代码


    public function __construct($_num,$_perpage,$_curpage,$_mpurl,$_left_limit = 3,$_right_limit = 3,$_language = 'cn')
    {
        $this->num = $_num;
        $this->perpage = $_perpage;
        $this->curpage = $_curpage;
        
        $this->mpurl = $_mpurl;
        $this->left_limit = $_left_limit;
        $this->right_limit = $_right_limit;
        $this->lang = $this->language[$_language];

        $this->total_page = ceil($_num/$_perpage);  //计算总页数
        $this->from = $_curpage - $_left_limit > 1 ? $_curpage - $_left_limit : 1; //页码起始位置
        $this->to = $_curpage + $_right_limit > $this->total_page ? $this->total_page : $_curpage + $_right_limit;

    }

    public function digg()
    {
        if($this->total_page <=1) return '';
        $css = $html = '';

        $css = '
                <style type="text/css">
                #pagination-digg ul{border:0; margin:0; padding:0;}

                #pagination-digg li{
                border:0; margin:0; padding:0;
                font-size:12px;
                list-style:none;
                float:left;
                margin-right:2px;}

                #pagination-digg a{
                border:solid 1px #9aafe5
                margin-right:2px;}

                #pagination-digg .previous-off,
                #pagination-digg .next-off {
                border:solid 1px #DEDEDE
                color:#DEDEDE
                display:block;
                float:left;
                font-weight:bold;
                margin-right:4px;
                padding:3px 4px; }

                #pagination-digg .next a,
                #pagination-digg .previous a {
                font-weight:bold;
                }

                #pagination-digg .active{
                background:#2e6ab1;
                color:#FFFFFF;
                font-weight:bold;
                display:block;
                float:left;
                padding:4px 6px;}

                #pagination-digg a:link,
                #pagination-digg a:visited {
                color:#0e509e
                display:block;
                float:left;
                padding:3px 6px;
                text-decoration:none;}

                #pagination-digg a:hover{
                border:solid 1px #0e509e}';
        $css .= "\r\n</style>\r\n";

        $html .='<ul id="pagination-digg">';

        /** 上一页 */
        $_prev_page= $this->curpage - 1;
        $html .= $this->curpage - $this->left_limit > 1 ? '<li><a href="'.$this->mpurl.'page='.$_prev_page.'" class="previous">'.$this->lang['prev'].'</a></li>' : '<li class="previous-off">'.$this->lang['prev'].'</li>';

        /** 页码列表 */
        for($i = $this->from; $i <= $this->to; $i++)
        {
           $html .= $i == $this->curpage ? '<li class="active">'.$i.'</li>' : '<li><a href="'.$this->mpurl.'page='.$i.'">'.$i.'</a></li>';
        }

        /** 下一页 */
        $_next_page= $this->curpage + 1;
        $html .= $this->to < $this->total_page ? '<li><a href="'.$this->mpurl.'page='.$_next_page.'" class="pbutton">'.$this->lang['next'].'</a></li>' : '<li class="pbutton">'.$this->lang['next'].'</li>';

        $html .='</ul>';

        $this->code = $css.$html;
        return $this->code;
    }


    public function flickr()
    {
        if($this->total_page <=1) return '';
        $css = $html = '';

        

        $html .='<ul id="pagination-flickr">';
        $html .= ''.$this->lang['current'].$this->curpage.'/'.$this->total_page.$this->lang['pages'].'';

        /** 上一页 */

        $_prev_page= $this->curpage - 1;
        $html .= $_prev_page  >= 1 ? '<a href="'.$this->mpurl.'/1" class="pbutton">'.$this->lang['first'].'</a><a href="'.$this->mpurl.'/'.$_prev_page.'" class="pbutton">'.$this->lang['prev'].'</a>' : ''.$this->lang['prev'].''.$this->lang['first'].'';

        /** 页码列表 */
        for($i = $this->from; $i <= $this->to; $i++)
        {
           $html .= $i == $this->curpage ? '<a href="'.$this->mpurl.'/'.$i.'">'.$i.'</a>' : '<a href="'.$this->mpurl.'/'.$i.'">'.$i.'</a>';
        }

        /** 下一页 */
        $_next_page= $this->curpage + 1;
        $html .= $this->curpage < $this->total_page ? '<a href="'.$this->mpurl.'/'.$_next_page.'" class="pbutton">'.$this->lang['next'].'</a><a href="'.$this->mpurl.'/'.$this->total_page.'" class="pbutton">'.$this->lang['last'].'</a>' : ''.$this->lang['next'].''.$this->lang['last'].'';

        $html .='</ul>';

        $this->code = $css.$html;
        return $this->code;
    }


    public function zoom()
    {
        if($this->total_page <=1) return '';
        $css = $html = '';

        $css = '
                <style type="text/css">


#PageList{float:left;height:50px;font-family:Verdana;text-align:center;font:12px Verdana,sans-serif;margin:0;filter:expression(document.execCommand("BackgroundImageCache", false, true))}

#PageList a{float:left;margin:10px 1px 0 1px;width:26px;height:20px;line-height:20px;color:#91ad00;font:12px;text-align:center;text-decoration:none;border:1px solid #91ad00}

#PageList a:hover{position:relative;margin:0 -10px 0 -10px;padding:0 9px;width:30px;line-height:40px;height:40px;color:#000;border:1px solid -10px;font-size:18px;font-weight:bold}

#PageList span{float:left;line-height:160%;padding:0px 8px;margin:10px 1px 0 1px;border:1px solid #91ad00;background:#91ad00;color:#FFF;font-weight:bold;}
                ';
        $css .= "\r\n</style>\r\n";

        $html .='<div id="PageList">';

        /** 上一页 */
        $_prev_page= $this->curpage - 1;
        $html .= $this->curpage - $this->left_limit > 1 ? '<a href="'.$this->mpurl.'page='.$_prev_page.'" class="previous">&laquo;</a>' : '';

        /** 页码列表 */
        for($i = $this->from; $i <= $this->to; $i++)
        {
           $html .= $i == $this->curpage ? "<span>$i</span>" : '<a href="'.$this->mpurl.'page='.$i.'">'.$i.'</a>';
        }

        /** 下一页 */
        $_next_page= $this->curpage + 1;
        $html .= $this->to < $this->total_page ? '<a href="'.$this->mpurl.'page='.$_next_page.'" class="pbutton">&raquo;</a>' : '';

        $html .='</ul>';

        $this->code = $css.$html;
        return $this->code;
    }

    public function classics()
    {
        if($this->total_page <=1) return '';
        $css = $html = '';

        $css = '
                <style type="text/css">

#pagelist { margin:0; padding:0;padding:6px 0px; height:20px;font-size:12px; font-family:Verdana;}
#pagelist a { color:#333; text-decoration:none;}
#pagelist ul { list-style:none;}
#pagelist ul li { float:left; border:1px solid #5d9cdf; height:20px; line-height:20px; margin:0px 2px;}
#pagelist ul li a, .pageinfo { display:block; padding:0px 6px; background:#e6f2fe;}
#pagelist .pageinfo  { color:#555;}
#pagelist .current { background:#a9d2ff; display:block; padding:0px 6px; font-weight:bold;}

                ';
        $css .= "\r\n</style>\r\n";

        $html .='<div id="pagelist"><ul>';
        $html .= '<li class="pageinfo">'.$this->lang['current'].$this->curpage.'/'.$this->total_page.$this->lang['pages'].'</li>';
        /** 上一页 */
        $_prev_page= $this->curpage - 1;
        $html .= $this->curpage - $this->left_limit > 1 ? '<li><a href="'.$this->mpurl.'page=1">'.$this->lang['first'].'</a></li><li><a href="'.$this->mpurl.'page='.$_prev_page.'">'.$this->lang['prev'].'</a></li>' : '';

        /** 页码列表 */
        for($i = $this->from; $i <= $this->to; $i++)
        {
           $html .= $i == $this->curpage ? '<li class="current">'.$i.'</li>' : '<li><a href="'.$this->mpurl.'page='.$i.'">'.$i.'</a></li>';
        }

        /** 下一页 */
        $_next_page= $this->curpage + 1;
        $html .= $this->to < $this->total_page ? '<li><a href="'.$this->mpurl.'page='.$_next_page.'">'.$this->lang['next'].'</a></li><li><a href="'.$this->mpurl.'page='.$this->total_page.'">'.$this->lang['last'].'</a></li>' : '';

        $html .='</ul></div>';

        $this->code = $css.$html;
        return $this->code;
    }

}