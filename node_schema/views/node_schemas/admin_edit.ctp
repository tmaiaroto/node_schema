<div class="types form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $form->create('NodeSchema');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#schema"><?php __('Node Schema'); ?></a></li>
                    <!--<li><a href="#fields"><?php __('Fields & Rules'); ?></a></li>-->
                </ul>

                <div id="schema">
                <?php
                    echo $form->input('title', array('label' => __('Title', true)));
                    echo $form->input('table_name', array('disabled' => 'disabled', 'label' => __('Table Name', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('Table name must follow CakePHP table naming convention.', true).'</span>'));
                    echo $form->input('Type.Type', array('label' => __('Apply to Node Type(s)', true), 'after' => '<span style="font-style: italic; clear: left; display: block; margin: 0px 0px 5px 0px; padding: 0px; font-size: 10px;">'.__('Ctrl-click or shift-click to choose multiple types.', true).'</span>'));
                ?>
                </div>
                
                <div id="fields">
                <?php
                	// TODO
                    //echo $form->input('Field.name');                 
                ?>
                </div>
                                
            </div>
        </fieldset>
    <?php 
    	echo $form->input('token_key', array('type' => 'hidden', 'value' => $this->params['_Token']['key']));
    	echo $form->end('Submit');
    ?>
</div>
