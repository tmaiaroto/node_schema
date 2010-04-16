<?php
/**
 * NodeSchemaField Model
 *
 * Holds definition data about fields that belong to various tables that extend node data. 
 * The data in this model is not used for anything more than generating tables that are referenced in the NodeSchema model.
 * Or in other words, this is a model that helps created a "schema builder." 
 *
 * @category Model
 * @package  Node Schema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class NodeSchemaField extends NodeSchemaAppModel {
	
	var $name = 'NodeSchemaField';	
	var $useTable = 'node_schema_fields';
	var $belongsTo = array(
		'NodeSchema' => array(
			'className'    => 'NodeSchema.NodeSchema',
            'foreignKey'    => 'node_schema_id'
		)
	);
	
	function beforeSave() {
		// Convert all the uuid keys to ordered numbers		
		if (isset($this->data['NodeSchemaField']) &&
            is_array($this->data['NodeSchemaField']) &&
            count($this->data['NodeSchemaField']) > 0 &&
            !Set::numeric(array_keys($this->data['NodeSchemaField']))) {
            $nodeSchemaField = $this->data['NodeSchemaField'];
            $this->data['NodeSchemaField'] = array();
            $i = 0;
            foreach ($nodeSchemaField as $nodeSchemaFieldUuid => $nodeSchemaFieldArray) {            	
                $this->data['NodeSchemaField'][$i] = $nodeSchemaFieldArray;
                unset($this->data['NodeSchemaField'][$nodeSchemaFieldUuid]); // don't need this uuid field anymore
                $i++;
            }
        }     
		return true;
	}
}
?>
