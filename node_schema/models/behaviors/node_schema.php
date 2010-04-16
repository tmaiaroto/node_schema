<?php
/**
 * NodeSchemaHook Behavior
 *
 * Hooks in to add a behavior to save the data to the extended schema table(s).
 *
 * @category Behavior
 * @package  Node Schema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class NodeSchemaBehavior extends ModelBehavior {

	var $model;	
	
	function setup(&$model, $settings=array()) {	
		$this->model = $model;
		// if(isset($settings['value'])): $this->value = $settings['value']; endif;
	}

	function cleanup(&$model) {	}
	
	function afterSave() {
		// The main node record is now saved. The rest is technically associated data.
		
		//debug($this->model->data); exit();
		if(isset($this->model->data['NodeSchemas'])) {
			foreach($this->model->data['NodeSchemas'] as $schema) {				
				$this->model->data[$schema['modelClass']]['node_id'] = $this->model->id; // set the associated node id to the last inserted node id
				// Instantiate the model
				$model = new Model(false, Inflector::tableize($schema['modelClass']));							
				$model->alias = $schema['modelClass'];				
				// Save/Update
				$model->save($this->model->data);
			}
		}
	}
	
	function beforeDelete($cascade) {
		// Remove the extended data to be tidy.		
		// First get the type id
		App::Import('Model', 'Type');
		$Type = new Type();
		$Type->recursive = -1;
		$type_record = Set::extract('/Type/id', $Type->find('first', array('fields' => array('Type.id'), 'conditions' => array('Type.alias' => $this->model->data['Node']['type']))));
		$type_id = $type_record[0];
		
		// Cool, now find all node schemas
		App::Import('Model', 'NodeSchema.NodeSchema');
		$NodeSchema = new NodeSchema();
		$NodeSchema->actsAs = array('Containable');
		$schemas = $NodeSchema->find('all', array(
			'fields' => array('NodeSchema.table_name'), 
			'contains' => array(
				'Type' => array(
					'conditions' => array('Type.id' => $type_id)
				)
			)
		));
		
		// Now loop through and check for records on those tables to remove
		if((is_array($schemas)) && (count($schemas) > 0)) {
			foreach($schemas as $schema) {
				$table_name = $schema['NodeSchema']['table_name'];
				$model = new Model(false, $table_name);			
				$model->primaryKey = 'node_id'; // set the primary key to the node_id
				if($model->delete($this->model->data['Node']['id'], false)) {
					return true;
				} else {					
					// return false; // There was some sort of error deleting the associated data. Do we even need this? It doesn't redirect, it stops. Have to handle the error.
				}
			}
		}
		
		return true;	
	}
		
}
?>
