<div class="node_schemas form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php $formUrl = array('plugin' => 'node_schema', 'controller' => 'node_schemas', 'action' => 'admin_import'); ?>
    <?php echo $form->create('NodeSchema', array('url' => $formUrl, 'type' => 'file'));?>
        <fieldset>
        <?php
            echo $form->input('NodeSchema.file', array('before' => __('Caution: This will overwrite existing schemas and empty table data if the table name is the same as an existing record. Also note, applying to types below won\'t remove old associations if this is replacing an existing schema.', true).'<br /><br />', 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('File must be a valid CakePHP schema php class file.', true).'</span>', 'label' => __('Schema File', true), 'type' => 'file'));
            echo $form->input('NodeSchema.title', array('label' => __('Title', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('Optional, will re-use an existing title if replacing schema or will generate a title from table name this is a new schema.', true).'</span>'));
            echo $form->input('Type.Type', array('label' => __('Apply to Node Type(s)', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">Ctrl-click or shift-click to choose multiple types.</span>'));
        ?>
        </fieldset>
     <?php
        echo $form->input('token_key', array(
            'type' => 'hidden',
            'value' => $this->params['_Token']['key'],
        ));
        echo $form->end('Submit');
    ?>
</div>
