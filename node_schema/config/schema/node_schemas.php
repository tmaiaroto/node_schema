<?php 
/* SVN FILE: $Id$ */
/* NodeSchemas schema generated on: 2010-04-15 19:04:02 : 1271377082*/
class NodeSchemasSchema extends CakeSchema {
	var $name = 'NodeSchemas';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $node_schemas = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150),
		'table_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150),
		'datasource' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150),
		'modified' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'),
		'created' => array('type' => 'timestamp', 'null' => true, 'default' => NULL)
	);
}
?>
