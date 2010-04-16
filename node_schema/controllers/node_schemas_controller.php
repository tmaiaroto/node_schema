<?php
class NodeSchemasController extends NodeSchemaAppController {
	
	var $name = 'NodeSchemas';
	var $uses = array('NodeSchema.NodeSchema');
	var $helpers = array('NodeSchema.SchemaForm');
	var $paginate;
	var $nodeSchemaPath;
	
	function __construct() {
		$this->nodeSchemaPath = APP.'plugins'.DS.'node_schema'.DS.'config'.DS.'schema';
		parent::__construct();
	}
	
	function admin_index() {
        $this->set('title_for_layout', __('Node Schema', true));
        $this->NodeSchema->recursive = 0;
        $this->paginate['NodeSchema']['order'] = 'NodeSchema.title ASC';
        $this->set('records', $this->paginate());
    }
	
	function admin_add() {
		$this->set('title_for_layout', __('Add Node Schema', true));
		
        if (!empty($this->data)) {
        	// CSRF Protection
            if ($this->params['_Token']['key'] != $this->data['NodeSchema']['token_key']) {
                $blackHoleCallback = $this->Security->blackHoleCallback;
                $this->$blackHoleCallback();
            }

            $this->NodeSchema->create();
            if ($this->NodeSchema->saveAll($this->data)) {
                $this->Session->setFlash(__('The Node Schema has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Node Schema could not be saved. Please, try again.', true));
            }
        }
        
        $types = $this->NodeSchema->Type->find('list');
        $this->set(compact('types'));
	}
	
	function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Node Schema', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Node Schema', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
        	// CSRF Protection
            if ($this->params['_Token']['key'] != $this->data['NodeSchema']['token_key']) {
                $blackHoleCallback = $this->Security->blackHoleCallback;
                $this->$blackHoleCallback();
            }
            if ($this->NodeSchema->saveAll($this->data)) {
                $this->Session->setFlash(__('The Node Schema has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Node Schema could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->NodeSchema->read(null, $id);
        }

        $types = $this->NodeSchema->Type->find('list');
        $this->set(compact('types'));
    }
    
    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Node Schema', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->NodeSchema->delete($id)) {
            $this->Session->setFlash(__('Node Schema deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }
	
	function admin_add_schema_field() {
        $this->layout = 'ajax';
    }
    
    function admin_delete_schema_field($id = null) {
        $success = false;
        if ($id != null && $this->NodeSchema->NodeSchemaField->delete($id)) {
            $success = true;
        }

        $this->set(compact('success'));
    }
    
    function admin_export($id=null) {    	
		$this->layout = null;
		$this->autoRender = false;
		
        if (!$id) {
            $this->Session->setFlash(__('Invalid Node Schema', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->NodeSchema->recursive = -1;
        $this->set('node_schemas', $node_schemas = $this->NodeSchema->read(null, $id));
        
        App::Import('CakeSchema');
        $CakeSchema = new CakeSchema();
        //debug($CakeSchema->tables);
        //debug($CakeSchema->read(array('default', 'test'))); 
        // The above only works for tables that have models, our models are only instantiated when needed in memory
        $db =& ConnectionManager::getDataSource($node_schemas['NodeSchema']['datasource']); 
     
        //$tables = array();
		$Model = new Model(null, $node_schemas['NodeSchema']['table_name'], $node_schemas['NodeSchema']['datasource']); 
		$Model->name = $Model->alias = Inflector::classify($node_schemas['NodeSchema']['table_name']);		
		$Object = ClassRegistry::init(array('class' => Inflector::pluralize($Model->name), 'ds' => $node_schemas['NodeSchema']['datasource']));
				
		// These cause issues for one reason or another
		unset($Object->tableToModel);
		unset($Object->__associationKeys);
		unset($Object->__associations);
		unset($Object->_findMethods);
		// The rest here just aren't needed, but don't cause any issues (not sure if it makes the export any faster, but makes the import php file smaller)
		unset($Object->Behaviors);
		unset($Object->useCache);
		unset($Object->cacheSources);
		unset($Object->alias);
		unset($Object->recursive);
		unset($Object->primaryKey);
		unset($Object->table);
		unset($Object->useTable);
		unset($Object->displayField);		
		unset($Object->useDbConfig); // This may eventually get used, if I can figure how to set it so it writes to the file
		
		// This is weird and doesn't even seem right, but it renames the property and works
		$Object->$node_schemas['NodeSchema']['table_name'] = $Object->_schema;
		unset($Object->_schema);
		
		$CakeSchema->path = $this->nodeSchemaPath;
		$CakeSchema->file = $node_schemas['NodeSchema']['table_name'].'.php';		
		$CakeSchema->write($Object);

		if(file_exists($this->nodeSchemaPath . DS . $CakeSchema->file)) {
			$file = ($this->nodeSchemaPath . DS . $CakeSchema->file);
			header ("Content-Type: application/octet-stream");
			header ("Accept-Ranges: bytes");
			header ("Content-Length: ".filesize($file));
			header ("Content-Disposition: attachment; filename=".$CakeSchema->file);
			readfile($file);
		} else {
			$this->Session->setFlash(__('Could not export node schema, ensure the path: '.$this->nodeSchemaPath.' is writeable by the server.', true));
            $this->redirect(array('action'=>'index'));
		}		
    }
    
    function admin_import() {
    	$this->set('title_for_layout', __('Import Node Schema', true));
        if (!empty($this->data)) {
        	 // CSRF Protection
            if ($this->params['_Token']['key'] != $this->data['NodeSchema']['token_key']) {
                $blackHoleCallback = $this->Security->blackHoleCallback;
                $this->$blackHoleCallback();
            }
			// Set datasource // TODO
			$data_source = 'default'; // $this->data['NodeSchema']['datasource'];
			
			// Set file
            $file = $this->data['NodeSchema']['file'];
            unset($this->data['NodeSchema']['file']);
            $destination = $this->nodeSchemaPath . DS . $file['name'];
            // Ensure the file got there ok
            if($file['error'] != 0) {
            	$this->Session->setFlash(__('There was a problem uploading the file, please try again.', true));
            	unset($this->data);
            	$this->redirect(array('action'=>'admin_import'));
            }            
            // Ensure this is a valid file           
            if(explode('.', $file['name']) > 0) {
                $fileExtension = explode('.', $file['name']);
                if(strtolower($fileExtension[1]) != 'php') {
                	$this->Session->setFlash(__('Invalid file, the file must be a php file.', true));
                	unset($this->data);
            		$this->redirect(array('action'=>'admin_import'));
                }
            } else {
                $this->Session->setFlash(__('Invalid file, the file must be a php file.', true));
                unset($this->data);
            	$this->redirect(array('action'=>'admin_import'));
            }
            
			// Now move the file out of the tmp folder on disk
            move_uploaded_file($file['tmp_name'], $destination);
			// ...And call _admin_schema_import if it made it there
			if(file_exists($destination)) {
				$this->_admin_schema_import($file['name'], $data_source, $this->data);	
			} else {
				$this->Session->setFlash(__('There was a problem uploading the file, ensure the path: '.$this->nodeSchemaPath.' is writeable by the web server and please try again.', true));
				unset($this->data);
            	$this->redirect(array('action'=>'admin_import'));
			}	
        }  
        $types = $this->NodeSchema->Type->find('list');
        $this->set(compact('types'));      
    	
    }
    
    function _admin_schema_import($schema_file=null, $data_source='default', $data=null) {
    	// Check to see if we have a schema file.
    	if(strtolower(substr($schema_file, -3)) != 'php') {
    		$this->Session->setFlash(__('Invalid node schema file.', true));
            $this->redirect(array('action'=>'admin_import'));
    	}
    	if (!$schema_file) {
            $this->Session->setFlash(__('No node schema file specified.', true));
            $this->redirect(array('action'=>'admin_import'));
        }

        App::Import('CakeSchema');
        $CakeSchema = new CakeSchema();
        $db =& ConnectionManager::getDataSource($data_source);
        
        $class_name = Inflector::camelize(substr($schema_file, 0, -4)).'Schema';
        // More checking. We need to esnure the class name is properly formatted.
        if(!preg_match('/^[a-zA-Z]+$/', $class_name)) {
        	$this->Session->setFlash(__('Invalid node schema file.', true));
            $this->redirect(array('action'=>'admin_import'));
        }
              	
        $table_name = substr($schema_file, 0, -4);
        // Even more checking
        if(!preg_match('/^[a-zA-Z_]+$/', $table_name)) {
        	$this->Session->setFlash(__('Unconventional table name, import can not continue.', true));
            $this->redirect(array('action'=>'admin_import'));
	    }
	    // The only "security" I'm offering right now, check to ensure we aren't overwriting any "core" Croogo tables
        switch($table_name) {
        	case 'acos':
        	case 'aros':
        	case 'aros_acos':
        	case 'blocks':
        	case 'comments':
        	case 'contacts':
        	case 'i18n': // not allowing numbers anyway at the moment
        	case 'languages':
        	case 'links':
        	case 'menus':
        	case 'messages':
        	case 'meta':
        	case 'nodes':
        	case 'nodes_terms':
        	case 'node_schemas': // hey that's us!
        	case 'node_schemas_types':
        	case 'node_schema_fields':
        	case 'regions':
        	case 'roles':
        	case 'settings':
        	case 'terms':
        	case 'types':
        	case 'types_vocabularies':
        	case 'users':
        	case 'vocabularies':
        		$this->Session->setFlash(__('This table is a core table and can not be overwritten.', true));
            	$this->redirect(array('action'=>'admin_import'));
        	break;
        }
        
        include_once($this->nodeSchemaPath.DS.$schema_file); // Can app import also work here?
        $NewNodeSchema = new $class_name;      	
       	
       	$dropped = $created = false;
		if(isset($NewNodeSchema->tables[$table_name])) {
			$dropped = $db->execute($db->dropSchema($NewNodeSchema, $table_name));
			//debug($db->dropSchema($NewNodeSchema, $table_name));
			$created = $db->execute($db->createSchema($NewNodeSchema, $table_name));
			//debug($db->createSchema($NewNodeSchema, $table_name));
		}
		if(!empty($created)) {
			//debug('Successfully imported the schema.');
			// Continue on to save a record in the node_schemas table
			// CSRF Protection
            if($this->params['_Token']['key'] != $data['NodeSchema']['token_key']) {
                $blackHoleCallback = $this->Security->blackHoleCallback;
                $this->$blackHoleCallback();
            }
			// Let's see if we're updating a record or saving a new one (remember imports can overwrite existing schema)
			$this->NodeSchema->recursive = -1;
			$result = $this->NodeSchema->find('first', array('conditions' => array('NodeSchema.table_name' => $table_name)));			
			// Set some data to save/update
			if(isset($result['NodeSchema']['id'])) {
				$this->NodeSchema->id = $result['NodeSchema']['id'];
				$data['NodeSchema']['modified'] = date('Y-m-d H:i:s', time());
			} else {
				$this->NodeSchema->create();
				$data['NodeSchema']['created'] = date('Y-m-d H:i:s', time());
				if(empty($data['NodeSchema']['title'])) { $data['NodeSchema']['title'] = Inflector::humanize($table_name); } // just in case
			}
			$data['NodeSchema']['table_name'] = $table_name;
			// Save the record, no validation. I don't know why it doesn't work, but it can't save unless it skips validation. The data has to be set though if everything got to this point.
			if($this->NodeSchema->saveAll($data, array('validate' => false))) {
				$this->Session->setFlash(__('Successfully imported the node schema.', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The table was created successfully, but there was a problem saving a Node Schema record for it.', true));
				$this->redirect(array('action'=>'index'));
			}
		} else {
			//debug('There was a problem importing the schema.');
			$this->Session->setFlash(__('There was a problem importing the node schema.', true));
			$this->redirect(array('action'=>'admin_import'));
		}
			
    }
    
    
}
?>
