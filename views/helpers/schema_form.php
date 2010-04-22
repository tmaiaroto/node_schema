<?php
/**
 * SchemaForm Helper
 *
 * Helps format the form to allow users to build schemas by entering fields.
 *
 * @category Helper
 * @package  NodeSchema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class SchemaFormHelper extends AppHelper {

	var $helpers = array('Html', 'Form');
	
/**
 * Displays a form field that allows for the input of a field name, datatype, and validation rules.
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param integer $id (optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
    function field($key = '', $type = null, $id = null, $options = array()) {
        $_options = array(
            'name'   => array(
                'label'   => __('Field Name', true),
                'value'   => $key,
            ),
            'type' => array(
                'label'   => __('Value Type', true),
                'value'   => $key,
                'options' => array('integer' => 'Integer', 'string' => 'String', 'Text' => 'text', 'timestamp' => 'Timestamp', 'date' => 'Date'),
                'type' => 'select'
            ),
            'rule' => array(
            	'label' => __('Rule', true),
            	'value' => $key,
            	'options' => array('notEmpty' => 'Not Empty', 'alphaNumeric' => 'Numbers and Letters only (no spaces)', 'date' => 'Valid Date (YY-MM-DD)', 'email' => 'Valid E-Mail Address'),
            	'empty' => '--',
            	'type' => 'select'
            )
        );
        $options = array_merge($_options, $options);
        $uuid = String::uuid();

        $fields  = '';
        if ($id != null) {
            $fields .= $this->Form->input('NodeSchemaField.'.$uuid.'.id', array('type' => 'hidden', 'value' => $id));
        }
        $fields .= $this->Form->input('NodeSchemaField.'.$uuid.'.name', $options['name']);
        $fields .= $this->Form->input('NodeSchemaField.'.$uuid.'.type', $options['type']);
        $fields .= $this->Form->input('NodeSchemaField.'.$uuid.'.rule', $options['rule']);
        $fields = $this->Html->tag('div', $fields, array('class' => 'fields'));

        $actions = $this->Html->link(__('Remove', true), '#', array('class' => 'remove-schema-field', 'rel' => $id), null, null, false);
        $actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

        $output = $this->Html->tag('div', $actions . $fields, array('class' => 'schema-field'));
        return $output;
    }

}
?>
