<#1>
<?php
if (!$ilDB->tableExists('evhk_hsluaddpic_conf')) {
    $fields = array(
        'name' => array(
            'type' => 'text',
            'length' => '100',
            'notnull' => false
        ),
        'value' => array(
            'type' => 'text',
            'length' => '100',
            'notnull' => false
        )
    );
    
    $ilDB->createTable('evhk_hsluaddpic_conf', $fields);
}

if ($ilDB->query("SELECT * FROM evhk_hsluaddpic_conf WHERE name='ws_url'")->numRows() == 0) {
    $data = array(
            'name' => array('text', 'ws_url'),
            'value' => array('text', '')
    );
    $ilDB->insert('evhk_hsluaddpic_conf', $data);
}

if ($ilDB->query("SELECT * FROM evhk_hsluaddpic_conf WHERE name='ws_user'")->numRows() == 0) {
    $data = array(
            'name' => array('text', 'ws_user'),
            'value' => array('text', '')
    );
    $ilDB->insert('evhk_hsluaddpic_conf', $data);
}

if ($ilDB->query("SELECT * FROM evhk_hsluaddpic_conf WHERE name='ws_password'")->numRows() == 0) {
    $data = array(
            'name' => array('text', 'ws_password'),
            'value' => array('text', '')
    );
    $ilDB->insert('evhk_hsluaddpic_conf', $data);
}
?>