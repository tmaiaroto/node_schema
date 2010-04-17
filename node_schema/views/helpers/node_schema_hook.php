<?php
/**
 * NodeSchemaHook Helper
 *
 *
 * @category Helper
 * @package  NodeSchema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class NodeSchemaHookHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    var $helpers = array(
        'Html',
        'Layout',
        'Form'
    );
    
    function addFormSection($action='add') {
    	// TODO: $actions to become $form_id .... try to dynamically get and pass the id of the form   	
	   		$jsBlock = '$(document).ready(function() { '; // open
    
    		$i=0;
    		foreach($this->Layout->View->viewVars['node_schema_model_classes'] as $model) {
    			// Add a section for each additional schema
    			$jsBlock .= '$("#node-main").append(\'<div id="'.$model->alias.'Section" class="node_schema_section"></div>\');'; 
    			// Give that section a title
    			// TODO: add collapsible and style
    			$jsBlock .= '$("#'.$model->alias.'Section").append(\'<a href="#" id="'.$model->alias.'Expand" class="node_schema_section_title_link"><h3 class="node_schema_section_title"><img src="/img/icons/bullet_arrow_down.png" rel="open" class="node_schema_expand_section_arrow" alt="expand or collapse section" />'.$model->userTitle.'</h3></a>\');';
    			$jsBlock .= '$("#'.$model->alias.'Expand").live("click", function() { 
    				$("#'.$model->alias.'SectionContent").slideToggle(); 
    				if($("#'.$model->alias.'Section .node_schema_expand_section_arrow").attr("rel") == "open") {
    					$("#'.$model->alias.'Section .node_schema_expand_section_arrow").attr("src", "/img/icons/bullet_arrow_right.png");
    					$("#'.$model->alias.'Section .node_schema_expand_section_arrow").attr("rel", "closed");
    				} else {
    					$("#'.$model->alias.'Section .node_schema_expand_section_arrow").attr("src", "/img/icons/bullet_arrow_down.png");
    					$("#'.$model->alias.'Section .node_schema_expand_section_arrow").attr("rel", "open");
    				}
    				return false; 
    			});';
    			// Add content section
    			$jsBlock .= '$("#'.$model->alias.'Section").append(\'<div id="'.$model->alias.'SectionContent"></div>\');'; 
    			 
    			// Add all the input fields for the schema    			
    			foreach($model->_schema as $k => $v) {
    				switch($k) {
    					case 'id':
    					case 'node_id':
    						// do nothing with these
    					break;
    					default:
    						$jsBlock .= '$("#'.$model->alias.'SectionContent").append(\''.$this->Form->input($model->alias.'.'.$k).'\'); '; 
    					break;
    				}
    			}
    			// For edit
    			if($action == 'edit') {
    				foreach($model->_schema as $k => $v) {
    					if($k == 'id') {
    						$jsBlock .= '$("#'.$model->alias.'SectionContent").append(\''.$this->Form->input($model->alias.'.'.$k, array('type' => 'hidden')).'\'); '; 
    					}
    				}
    			}
    		
    			// Add in hidden field that cues us into a list of additional schema
    			$jsBlock .= '$("#'.$model->alias.'SectionContent").append(\''.$this->Form->input('NodeSchemas.'.$i.'.modelClass', array('value' => $model->alias, 'type' => 'hidden')).'\'); '; 
    			$i++;
    		}
    	
    	
    		$jsBlock .= ' });'; // close
    		return $jsBlock;
    	
    }
    
    
/**
 * Called after activating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onActivate(&$controller) {
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onDeactivate(&$controller) {
    }
/**
 * Before render callback. Called before the view file is rendered.
 *
 * @return void
 */
    function beforeRender() {   
    	// IF ADMIN EDIT
    	if(($this->action == 'admin_edit') && ($this->params['controller'] == 'nodes')) { 
    		$this->Html->scriptBlock($this->addFormSection('edit'), array('inline' => false));	 
    		//debug($this->Layout->View->viewVars);
    		
    	} 
    	
    	// IF ADMIN ADD
    	if(($this->action == 'admin_add') && ($this->params['controller'] == 'nodes')) { 
    		$this->Html->scriptBlock($this->addFormSection('add'), array('inline' => false));	
    	}
    	   	    	
    }
/**
 * After render callback. Called after the view file is rendered
 * but before the layout has been rendered.
 *
 * @return void
 */
    function afterRender() { 
    	// IF ADMIN any action, we want to add our menu items to the content menu
    	if((isset($this->params['admin'])) && ($this->params['admin'] == 1)) {
    		echo $this->Html->scriptBlock('
    			$("#nav .sf-menu li:nth-child(2) ul:first").append(\'<li>'. $this->Html->link('<span class="ui-icon ui-icon-calculator"></span>'.__('Node Schema', true), '/admin/node_schema/node_schemas/index', array('escape' => false)) .'<ul><li>'. $this->Html->link('<span class="ui-icon ui-icon-calculator"></span>'.__('List', true), '/admin/node_schema/node_schemas/index', array('escape' => false)) .'</li><li>'. $this->Html->link('<span class="ui-icon ui-icon-plus"></span>'.__('Add New', true), '/admin/node_schema/node_schemas/add', array('escape' => false)) .'</li></ul></li>\');
    		');
    	}
    	   	
    	if($this->params['controller'] == 'nodes') {
    		if(($this->action == 'admin_add') || ($this->action == 'admin_edit')) {
       			echo $this->Html->css(array('/node_schema/css/schemas'));    	
       		}
    	}
    }
/**
 * Before layout callback. Called before the layout is rendered.
 *
 * @return void
 */
    function beforeLayout() {
    }
/**
 * After layout callback. Called after the layout has rendered.
 *
 * @return void
 */
    function afterLayout() {      		
    }
/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
    function afterSetNode() {
        // field values can be changed from hooks
        // $this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by ExampleHook]');
    }
/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
    function beforeNodeInfo() {
        //return '<p>beforeNodeInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
    function afterNodeInfo() {
        //return '<p>afterNodeInfo</p>';
    }
/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
    function beforeNodeBody() {
        //return '<p>beforeNodeBody</p>';
    }
/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
    function afterNodeBody() {
        //return '<p>afterNodeBody</p>';
    }
/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    function beforeNodeMoreInfo() {
       // return '<p>beforeNodeMoreInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    function afterNodeMoreInfo() {
        //return '<p>afterNodeMoreInfo</p>';
    }
}
?>
