<?php
/**
 * NodeSchemaHook Component
 *
 * Hooks in to extend the data on nodes merging in new schema(s).
 *
 * @category Component
 * @package  Node Schema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class NodeSchemaHookComponent extends Object {
	
	var $controller;
	
/**
 * Called after activating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onActivate(&$controller) {
        // ACL: set ACOs with permissions
        $controller->Croogo->addAco('NodeSchema'); // the controller
        $controller->Croogo->addAco('NodeSchema/admin_index');  // admin methods
        $controller->Croogo->addAco('NodeSchema/admin_add');
        $controller->Croogo->addAco('NodeSchema/admin_edit');
        $controller->Croogo->addAco('NodeSchema/admin_delete');
        $controller->Croogo->addAco('NodeSchema/admin_add_schema_field');
        $controller->Croogo->addAco('NodeSchema/admin_import');
        $controller->Croogo->addAco('NodeSchema/admin_export');
        
        // Install the database tables we need
        App::Import('CakeSchema');
        $CakeSchema = new CakeSchema();
        $db =& ConnectionManager::getDataSource('default');  // TODO: How do we change this for installs?
        
        // A list of schema files to import for this plugin to work
        $schema_files = array(
        	'node_schema_fields.php',
        	'node_schemas.php',
        	'node_schemas_types.php'
        );
        foreach($schema_files as $schema_file) {
        	$class_name = Inflector::camelize(substr($schema_file, 0, -4)).'Schema';
        	$table_name = substr($schema_file, 0, -4);
        	// Only build the tables if they don't already exist
        	if(!in_array($table_name, $db->_sources)) {
	        	include_once(APP.'plugins'.DS.'node_schema'.DS.'config'.DS.'schema'.DS.$schema_file); // Can app import also work here?
	        	$ActivateSchema = new $class_name;
	        	$created = false;
				if(isset($ActivateSchema->tables[$table_name])) {
					$db->execute($db->createSchema($ActivateSchema, $table_name));
				}
			}
        }
        
        
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onDeactivate(&$controller) {
        // ACL: remove ACOs with permissions
        $controller->Croogo->removeAco('NodeSchema');
    }
/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param object $controller Controller with components to startup
 * @return void
 */
    function startup(&$controller) {
    	$this->controller =& $controller;
    	
    	// Add the NodeSchema behavior that will save data for us
    	// Actually...couldn't all the data be loaded there too...setting what is below in an afterFind() ??
    	$this->controller->Node->Behaviors->attach('NodeSchema.NodeSchema');
    }
/**
 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
 * Controller::render()
 *
 * @param object $controller Controller with components to beforeRender
 * @return void
 */
    function beforeRender(&$controller) {
    	// Actions link for nodes list
    	if(Inflector::camelize(Inflector::singularize($controller->params['controller'])) == 'Node') {
    		Configure::write('Admin.rowActions.Schemas', 'plugin:node_schema/controller:node_schemas/action:index/:id');
    	}
        
        // Admin menu: admin_menu element of NodeSchema plugin will be shown in admin panel's navigation
        Configure::write('Admin.menus.node_schema', 1);        
        $this->controller =& $controller;
        
        // NON-ADMIN METHODS NEED TO GET DATA MERGED IN
        if((!isset($this->controller->params['admin'])) || ($this->controller->params['admin'] != 1)) {
			// IF there are nodes returned (multiple, like an indx page with paginated results)	   
	        if((isset($controller->viewVars['nodes'])) && (!empty($controller->viewVars['nodes']))) {	        	
		        if(count($controller->viewVars['nodes']) > 0) {
			        $i=0;
			        foreach($controller->viewVars['nodes'] as $node) {
			        	$this->_mergeData($controller, $node, $i);
			        	$i++;
			        }		        
		        } 		        
	        }
	        // OR IF there is one node returned (single, a view page,structure looks like $controller['viewVars']['node']['Node']['id'])
	        if((isset($this->controller->viewVars['node'])) && (!empty($this->controller->viewVars['node']))) {		    
		      	$this->_mergeData($this->controller);
		    }   
        }
       
        // debug($this->controller->viewVars); // this would show all the fused data
        
        // Type Alias stored here: $controller->viewVars['typeAlias']
        // Type here: $controller->viewVars['type']['Type']['id'] and so on
        
        // SOME ADMIN METHODS - NEED VALUES FOR FORMS (NodeSchemaHelper hook's calls to form helper need this data)
        if($this->controller->params['controller'] == 'nodes') {
        	if(($this->controller->action == 'admin_add') || ($this->controller->action == 'admin_edit')) {
		        // No reason for this not to be set, but lets check anyway
		        if(isset($controller->viewVars['type']['Type']['id'])) {
		        	$this->controller->loadModel('NodeSchema.NodeSchema'); // Load the model so we can associate
		        	$this->controller->NodeSchema->bindModel(array('hasOne' => array('NodeSchemasType')));
					$schemas = $this->controller->NodeSchema->find('all', array(
							'fields' => array('NodeSchema.table_name', 'NodeSchema.title'),
							'conditions'=>array('NodeSchemasType.type_id'=> $controller->viewVars['type']['Type']['id'])
					));
					// We now have the schemas associated with this type
					// We'll loop through that to generate model classes using classify() ...
					// This will serve another loop in the helper to output $form->input('ModelName.FIELD')
					// As well as create full models which have benefits like caching and validation now too			
					$modelClasses = array();
					foreach($schemas as $schema) {
						// Instantiate
						$model = new Model(false, $schema['NodeSchema']['table_name']);
				        $model->alias = Inflector::classify($schema['NodeSchema']['table_name']);
						$model->recursive = -1; // There's no associations anyway
						// TODO: add validation rules (added/edited and stored somewhere, where yet I don't know)
						
						$model->userTitle = $schema['NodeSchema']['title']; // Cool, we'll use this on the form
						// Build an array of model objects
						$modelClasses[] = $model;
						
						// IF admin_edit, set some more data into $controller->data
		        		if(($this->controller->params['controller'] == 'nodes') && ($this->controller->action == 'admin_edit')) {
							$schema_data = $model->find('first', array('conditions' => array('node_id' => $controller->data['Node']['id'])));
							if($schema_data) {
								$key = key($schema_data);
								$controller->data[$key] = $schema_data[$key];								
							}						
		        		}
		        		
					}
					// Set to view so form helper can use
					$controller->viewVars['node_schema_model_classes'] = $modelClasses;
					// debug($controller->viewVars['node_schema_model_classes']);
		        }
			}
        }
        
        
    }
/**
 * Called after Controller::render() and before the output is printed to the browser.
 *
 * @param object $controller Controller with components to shutdown
 * @return void
 */
    function shutdown(&$controller) {
    }
 

/**
 * This performs some magic and merges extended data with the node data.
 *
 * @param object $controller Controller object TODO: See if we even need to pass it, because $this->controller exists
 * @param array $node The current node record (in situations of multiple records from a find all, like index pages)
 * @param integer $i The counter number in the loop that's calling this method (in situations of multiple records)
 * @return void
 */
	function _mergeData($controller, $node=null, $i=null) {
		// Find the associated schema(s) and merge with the node
		$this->controller->loadModel('NodeSchema.NodeSchema');
		// Get the type id
		if(!empty($node)) {
			// Multiple results
			$node_type = $node['Node']['type'];
		} else {
			// Single result
			$node_type = $controller->viewVars['node']['Node']['type'];
		}
		$type_id = null;
		foreach($controller->viewVars['types_for_layout'] as $type) {
			if($type['Type']['alias'] == $node_type) {
				$type_id = $type['Type']['id'];
			}
		}
		
		// Get the schemas for the current node's type
		$schemas = null;
		if(!empty($type_id)) {
	    	$this->controller->NodeSchema->bindModel(array('hasOne' => array('NodeSchemasType')));
			$schemas = $this->controller->NodeSchema->find('all', array(
					'fields' => array('NodeSchema.table_name', 'NodeSchema.title'),
					'conditions'=>array('NodeSchemasType.type_id'=> $type_id)
			));									
		}
				        	
		// Merge results from the schema table(s) where the node id is equal to the current node
		if(!empty($schemas)) {
			foreach($schemas as $schema) {
				if((isset($schema['NodeSchema']['table_name'])) && (!empty($schema['NodeSchema']['table_name']))) {
					// A plain query could be made for this, but instantiating a class opens up the query to many more options, caching being one
					$model = new Model(null, $schema['NodeSchema']['table_name']);
					$model->alias = Inflector::classify($schema['NodeSchema']['table_name']);
					$model->recursive = -1; // There's no associations anyway
					// Get the node id so we can get the associated schema data
					if(!empty($node)) {
						// Multiple results
						$node_id = $node['Node']['id'];
					} else {
						// Single result
						$node_id = $controller->viewVars['node']['Node']['id'];
					}		
					$results = $model->find('first', array('fields' => array($model->alias.'.*'), 'conditions' => array($model->alias.'.node_id' => $node_id)));	
					// If there was a record found
					if($results) {
						// Set this just because it's good reference, but we do want to merge and adjust the Node data to better fuse these new values
						if(!empty($node)) {
							// Multiple results
							//$controller->viewVars['nodes'][$i] .= $results;
							$controller->viewVars['NodeSchema'][$model->alias][$i] = $results;
						} else {
							// Single result
							//$controller->viewVars['node'] .= $results;
							$controller->viewVars['NodeSchema'][$model->alias] = $results;
						}
						if(isset($results[$model->alias]['id'])) { unset($results[$model->alias]['id']); }
						if(isset($results[$model->alias]['node_id'])) { unset($results[$model->alias]['node_id']); }
						
						// Now merge the data
						if(!empty($node)) {
							// Multiple results
							if((is_array($controller->viewVars['nodes'][$i]['Node'])) && (is_array($results[$model->alias]))) {
								array_merge($controller->viewVars['nodes'][$i]['Node'], $results[$model->alias]);
							}
						} else {
							// Single result
							if((is_array($controller->viewVars['node']['Node'])) && (is_array($results[$model->alias]))) {
								array_merge($controller->viewVars['node']['Node'], $results[$model->alias]);
							}
						}
					}
				}				
			}
		}
	}
    
}
?>
