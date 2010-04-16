<?php
/**
 * NodeSchema Model
 *
 * Holds records that indicate which tables hold extended data for nodes of certain types.
 *
 * @category Model
 * @package  Node Schema
 * @author   Tom Maiaroto <tom@shift8creative.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.shift8creative.com
 */
class NodeSchema extends NodeSchemaAppModel {
	
	var $name = 'NodeSchema';	
	var $hasAndBelongsToMany = array('Type');
	var $hasMany = array(
		'NodeSchemaField' => array(
			'className'     => 'NodeSchema.NodeSchemaField',
            'foreignKey'    => 'node_schema_id',
            'order'    => 'NodeSchemaField.name DESC',
            'dependent'=> true
		)
	);
	var $nodeSchemaTablePrefix = 'node_'; // If for some reason this needs to change, this is used while saving new schemas (not that they need a node_ prefix, but I like that convention)
	
	var $validate = array(
		'title' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
        'table_name' => array(
            'table_name_not_empty_rule' => array(
            	'rule' => 'notEmpty',
            	'message' => 'This field cannot be left blank.'
            ),
            'table_name_unique_rule' => array(
            	'rule' => 'isUnique',
            	'message' => 'This table is already in use.'
            ),
            'table_name_conventional_rule' => array(
            	'rule' => '/^[a-z_]+$/',
            	'message' => 'This field must conform to convention, use only lowercase letters and underscores.'
            ),
            'table_name_double_prefix_rule' => array(
            	'rule' => '/^((?!node_).)*$/i',
            	'message' => 'Do not use the prefix "node_" for the table name, it will automatically be added for you.',
            ),
            'table_name_multiple_underscore_rule' => array(
            	'rule' => '/^((?!_{2}_*).)*$/',
            	'message' => 'This field must conform to convention, don\'t put more than one underscore next to each other.'
            )
        ),
	);
	
}
?>
