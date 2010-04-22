<?php 
/* SVN FILE: $Id$ */
/* NodeSchemaFields schema generated on: 2010-04-15 19:04:02 : 1271377082*/
class NodeSchemaFieldsSchema extends CakeSchema {
	var $name = 'NodeSchemaFields';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $node_schema_fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25),
		'rule' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255),
		'node_schema_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 11)
	);
}
?>
