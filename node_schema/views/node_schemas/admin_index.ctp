<div class="types index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Node Schema', true), array('action'=>'add')); ?></li>
            <li><?php echo $html->link(__('Import Node Schema', true), array('action'=>'import')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('title'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($records AS $record) {
            $actions  = $html->link(__('Edit/Associate', true), array('plugin' => 'node_schema', 'controller' => 'node_schemas', 'action' => 'edit', $record['NodeSchema']['id']));
            $actions .= $html->link(__('Export', true), array('plugin' => 'node_schema', 'controller' => 'node_schemas', 'action' => 'export', $record['NodeSchema']['id']));
            $actions .= ' ' . $layout->adminRowActions($record['NodeSchema']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'node_schemas',
                'action' => 'delete',
                $record['NodeSchema']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $record['NodeSchema']['id'],
                $record['NodeSchema']['title'],
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
