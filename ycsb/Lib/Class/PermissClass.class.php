<?php
/**
 * 权限控制
 * @update      2011-02-12 <rewind627@gmail.com>
 */
class PermissClass
{
    var $control = '';
    var $group_id = '';
    var $mright = '';
    var $default_control = array('admin');//定义系统默认控制器admin，所有人可操作
    function __construct($group_id, $control='')
    {
        $this->control = $control;
        $this->group_id = $group_id;
        $groupModel = M('group');
        $groupInfo = $groupModel->where(array('group_id'=>$this->group_id))->find();
        $mright = $groupInfo['mright'];
        $this->mright = unserialize(stripslashes($mright));
    }
    
    public function control() {
        if (!auth()) {
            showMsg('你没有权限操作此模块！');
        }
    }
    
    public function leftmenu() {
        $menuArray = require 'Conf/menu.php';
        foreach ($menuArray as $key=>$menu)
        {
            $mainMenu = $menu['name'];
            $subNum = $menu['step'];
            $subNums .= $subNum.',';
            
            ${'leftMenu'.$subNum} .= "<div class=\"list\"><a href=\"javascript:\" onclick=\"SwitchMenu('menu_{$subNum}')\">{$mainMenu}</a></div><span id=\"menu_{$subNum}\" style=\"display:none;\"> ";
            
            foreach ($menu['menu'] as $n=>$menuName)
            {
                if (in_array($menuName['linkurl'], $this->mright))
                {
                    ${'leftMenu'.$subNum} .="<div class=\"sonlist\"><a href=\"javascript:\" onclick=\"loadHtml('{$menuName['linkurl']}')\">{$menuName['name']}</a></div>";
                    $menuNum .= $subNum.',';
                }
            }
            ${'leftMenu'.$subNum} .= "</span>";
        }
        
        for ($i=1;$i<=count($menuArray);$i++)
        {
            if (!in_array($i, explode(',', substr($menuNum, 0, -1))))
            {
                unset(${'leftMenu'.$i});
            }
            
            $leftmenu .= ${'leftMenu'.$i};
        }
        return $leftmenu;
    }

    public function guide()
    {
        if (!is_array($GLOBALS['menuArray'])) return false;
        foreach ($GLOBALS['menuArray'] as $key=>$menu)
        {
            $mainMenu = $menu['name'];
            $subNum = $menu['step'];
            $subNums .= $subNum.',';
            $countmenus = count($menu['menu']);
            
            ${'leftMenu'.$subNum} .= "<tr><td colspan=\"10\"><b>{$menu['name']}</b></td></tr><tr>";

            foreach ($menu['menu'] as $n=>$menuName)
            {
                if (in_array($menuName['linkurl'], $this->mright))
                {
                    if (($n+1) == $countmenus)
                        ${'leftMenu'.$subNum} .="<td colspan='".(10-$countmenus)."'><a href=\"{$menuName['linkurl']}\">{$menuName['name']}</a></td>";
                    else
                        ${'leftMenu'.$subNum} .="<td><a href=\"{$menuName['linkurl']}\">{$menuName['name']}</a></td>";
                    $menuNum .= $subNum.',';
                }
            }
            ${'leftMenu'.$subNum} .= "</tr>";
        }
        
        for ($i=1;$i<=count($GLOBALS['menuArray']);$i++)
        {
            if (!in_array($i, explode(',', substr($menuNum, 0, -1))))
            {
                unset(${'leftMenu'.$i});
            }
            
            $leftmenu .= ${'leftMenu'.$i};
        }
        return $leftmenu;
    }
}
?>
