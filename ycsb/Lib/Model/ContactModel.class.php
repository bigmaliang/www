<?php
/**
 * 联系人模型
 */
import('RelationModel');

class ContactModel extends RelationModel
{
    protected $_link = array(
        'account'=>HAS_ONE,
        'client'=>BELONGS_TO
    );
}
?>