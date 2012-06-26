<?php
import('RelationModel');

class FinanceModel extends RelationModel
{
    protected $_link = array(
        'client'=>BELONGS_TO
    );
}
?>