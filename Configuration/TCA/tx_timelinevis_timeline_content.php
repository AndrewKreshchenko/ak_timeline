<?php

return [
    'ctrl' => [
        'label' => 'Timeline/Content Relation',
        'title' => 'LLL:EXT:ak_timeline/Resources/Private/Language/locallang_be.xlf:timeline_relation',
        'hideTable' => true,
        'sortby' => 'sorting',
        'iconfile' => 'EXT:ak_timeline/Resources/Public/Icons/timeline-content.png',
    ],
    'columns' => [
        'content_uid' => [
            'label' => 'Content Element',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'tt_content',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'timeline_uid' => [
            'label' => 'Timeline',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_timelinevis_domain_model_timeline',
                'foreign_table_where' => ' AND tx_timelinevis_domain_model_timeline.uid IN (0,-1)',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'timeline_uid,content_uid']
    ],
    'palettes' => []
];
