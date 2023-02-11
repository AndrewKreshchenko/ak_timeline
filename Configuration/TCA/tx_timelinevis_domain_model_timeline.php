<?php
/**
 * TYPO3 Configuration Array (TCA) for the Timeline domain model
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

$infoTimelineProcessor = \AK\TimelineVis\Hooks\Backend\Form\FormDataProvider\TimelineItemsProcFunc::class . '->getTimelinesList';
$languageFile = 'ak_timeline/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:ak_timeline/Resources/Public/Icons/tx_timelinevis_domain_model_timeline.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title, description, range_start, range_end, parent_id, points',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, title, description, range_start, range_end, parent_id, points, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'enablePagination' => [
            'exclude' => false,
            'label' => 'Enable pagination',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.crdate',
            'config' => [
                'type' => 'input'
            ],
        ],
        'range_start' => [
            'exclude' => false,
            'label' => 'range start',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'renderType' => 'inputDateTime',
                'dbType' => 'date',
                'eval' => 'date,' . \AK\TimelineVis\Evaluation\TimelineValidator::class
            ],
        ],
        // \JWeiland\Events2\Tca\Type\Time::class
        // make custom render type https://docs.typo3.org/m/typo3/reference-tca/main/en-us/ColumnsConfig/Type/Check/Properties/RenderType.html
        'range_end' => [
            'exclude' => false,
            'label' => 'range end',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'renderType' => 'inputDateTime',
                'dbType' => 'date',
                'eval' => 'date,' . \AK\TimelineVis\Evaluation\TimelineValidator::class
            ]
        ],
        'parent_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.parentid',
            'config' => [
                // app/public/typo3conf/ext/news/Configuration/TCA/tx_news_domain_model_news.php
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => $infoTimelineProcessor,
                'sortItems' => [
                    'value' => 'desc',
                ],
                'size' => 1,

                // 'renderType' => 'selectTree',
                // 'foreign_table' => 'tx_timelinevis_domain_model_timeline',
                // 'foreign_table_where' => 'ORDER BY tx_timelinevis_domain_model_timeline.pid',
                // 'MM' => 'tx_timelinevis_domain_model_timeline',
                // 'MM_opposite_field' => 'pages',
                // 'MM_match_fields' => [
                //     'pid' => 'pages',
                // ],
                // 'size' => 20,
                // 'maxitems' => 99,
                // 'treeConfig' => [
                //     'parentField' => 'pid',
                //     'appearance' => [
                //         'expandAll' => true,
                //         'showHeader' => true,
                //         'maxLevels' => 9,
                //     ],
                // ],
            ]
        ],

        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'description' => [
            'exclude' => false,
            'label' => 'Timeline description',
            'description' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ]
        ],
        'points' => [
            'exclude' => false,
            'label' => 'Points', // 'LLL:EXT:' . $languageFile . ':tx_simpleblog_domain_model_post.points',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_timelinevis_domain_model_point',
                'foreign_field' => 'timeline',
                'maxitems' => 3999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
        // 'point' => [
        //     'exclude' => false,
        //     'label' => 'Text of Timeline point',
        //     'description' => 'Point', // 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.point',
        //     'config' => [
        //         'type' => 'text',
        //         'enableRichtext' => true
        //     ]
        // ],
    ],
];
