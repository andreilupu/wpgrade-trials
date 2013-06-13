<?php
// start the party
$trials_config = array(
    'role' => 'editor',
    'log_path'=> get_template_directory() . '/trial-logs',
    'blogdir_path' => 'C:\Winginx\home\localhost\public_html\wp-content\blogs.dir\\',
    'themes' => array(
        '2' => 'senna',
        '3' => 'swipe'
    )
);

global $trial; // ugly
$trial = new wpGrade_Trial( $trials_config );