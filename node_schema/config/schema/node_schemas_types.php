<?php 
/* SVN FILE: $Id$ */
/* NodeSchemasTypes schema generated on: 2010-04-15 12:04:05 : 1271354285*/
class NodeSchemasTypesSchema extends CakeSchema {
	var $name = 'NodeSchemasTypes';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $node_schemas_types = array(		
		'node_schema_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 11, 'key' => 'index'),
		'type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 11, 'key' => 'index')
	);
}
?>
