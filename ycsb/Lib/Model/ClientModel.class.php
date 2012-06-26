<?php
import('RelationModel');

class ClientModel extends RelationModel
{
    protected $_link = array(
        'contact'=>HAS_MANY,
        'project'=>HAS_MANY,
        'dhm'=>HAS_MANY,
        'finance'=>HAS_MANY,
        'client_status'=>BELONGS_TO,
        'client_size'=>BELONGS_TO,
        'client_company_type'=>BELONGS_TO,
        'industry'=>BELONGS_TO,
        'client_source'=>BELONGS_TO
    );
}
?>