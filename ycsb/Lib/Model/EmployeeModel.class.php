<?php
import('RelationModel');

class EmployeeModel extends RelationModel
{
    protected $_link = array(
        'account'=>HAS_ONE,
        'department'=>BELONGS_TO,
        'job'=>BELONGS_TO
    );

	protected $_auto	 =	 array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>
