<?php
import('RelationModel');

class DsdModel extends RelationModel
{
    protected $_link = array(
        'client'=>BELONGS_TO
    );
}
?>