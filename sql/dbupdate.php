<#1>
<?php
if (!$ilDB->tableExists('comparison_data')) {
    $fields = array(
        'exercise_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'ass_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'returned_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'compared_with_returned_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'threshold' => array(
            'type' => 'integer',
            'length' => 2,
            'notnull' => true
        ),
        'k_gram' => array(
            'type' => 'integer',
            'length' => 2,
            'notnull' => true
        ),
        'match_percent' => array(
            'type' => 'float',
            'notnull' => true
        )
    );
    $ilDB->createTable("comparison_data", $fields);
    $ilDB->addUniqueConstraint("comparison_data", array("exercise_id", "ass_id", "returned_id", "compared_with_returned_id"));
}

if (!$ilDB->tableExists('comparison_error')) {
    $fields = array(
        'exercise_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'ass_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'returned_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'error_text' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => true
        )
    );
    $ilDB->createTable("comparison_error", $fields);
    $ilDB->addUniqueConstraint("comparison_error", array("exercise_id", "ass_id", "returned_id"));
}
?>