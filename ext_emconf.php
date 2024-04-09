<?php
/**
 * Extension Manager/Repository config file for the ext.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Timeline Visualizer',
    'description' => 'An extension to manage timelines and widgets to provide visual representation of a history unit.',
    'category' => 'plugin',
    'author' => 'Andrii Kreshchenko',
    'author_email' => 'mail2andyk@gmail.com',
    'state' => 'alpha',
    'uploadfolder' => 1,
    'createDirs' => 'fileadmin/timelinevis',
    'clearCacheOnLoad' => true,
    'version' => 'v1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'AK\\TimelineVis\\' => 'Classes'
        ]
    ],
];
