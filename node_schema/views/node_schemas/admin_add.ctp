<?php echo $html->script(array('/node_schema/js/schemas'), false); ?>
<?php echo $html->css(array('/node_schema/css/schemas'), null, array(), false); ?>
<div class="types form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('NodeSchema');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#schema"><?php __('Node Schema'); ?></a></li>
                    <!--<li><a href="#schema-fields"><?php __('Fields & Rules'); ?></a></li>-->
                </ul>

                <div id="schema">
                <?php
                	echo $form->input('datasource', array('type' => 'hidden', 'value' => 'default'));
                    echo $form->input('title', array('label' => __('Title', true)));
                    echo $form->input('table_name', array('label' => __('Table Name', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('Table name must follow CakePHP table naming convention.', true).'</span>'));
                    echo $form->input('Type.Type', array('label' => __('Apply to Node Type(s)', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('Ctrl-click or shift-click to choose multiple types.', true).'</span>'));
                ?>
                </div>
                
                <?php // TODO: Fix errors and continue development on this
                /*
                <div id="schema-fields">
                	<div id="schema-field-fields">
                        <?php 
                            $fields = array();
                            if (count($fields) > 0) {
                                foreach ($fields AS $fieldKey => $fieldType) {
                                    echo $schemaForm->field($fieldKey, $fieldType);
                                }
                            } else {
                                echo $schemaForm->field();
                            }
                        ?>
                        <div class="clear">&nbsp;</div>
                    </div>
                    <a href="#" class="add-schema-field"><?php __('Add another field'); ?></a>
                </div>
                */ ?>
                                
            </div>
        </fieldset>
    <?php    	
    	echo $form->end('Submit');
    ?>
</div>
