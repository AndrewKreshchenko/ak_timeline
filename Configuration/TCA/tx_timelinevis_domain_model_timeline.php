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
    // 'ctrl' => [
    //     'label' => 'title',
    //     'label_alt' => 'cn_iso_2',
    //     'label_alt_force' => 1,
    //     'label_userFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaLabelProcessor::class . '->addIsoCodeToLabel',
    //     'adminOnly' => true,
    //     'rootLevel' => 1,
    //     'is_static' => 1,
    //     'readOnly' => 1,
    //     'default_sortby' => 'ORDER BY cn_short_en',
    //     'delete' => 'deleted',
    //     'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_countries.title',
    //     'iconfile' => 'EXT:static_info_tables/Resources/Public/Images/Icons/static_countries.svg',
    //     'searchFields' => 'cn_short_en,cn_official_name_local,cn_official_name_en',
    // ],
    'ctrl' => [
        'title' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'range_start' => 'range_start',
            'range_end' => 'range_end',
            'date_start_b_c' => 'date_start_b_c',
            'date_end_b_c' => 'date_end_b_c',
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:ak_timeline/Resources/Public/Icons/tx_timelinevis_domain_model_timeline.gif',
    ],
    // 'interface' => [
    //     'showRecordFieldList' => 'hidden, title, description, range_start, range_end, parent_id, points',
    // ],
    'types' => [
        '1' => [
            'showitem' => 'hidden, title, description,
            --palette--;;paletteCore,
            parent_id, points,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access'
        ],
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
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.crdate',
            'config' => [
                'type' => 'input'
            ],
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
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.description',
            'description' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.description.desc',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ]
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
        'date_start_b_c' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.datebc',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => false
                    ]
                ],
            ]
        ],
        'date_end_b_c' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.datebc',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => false
                    ]
                ],
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
                    'value' => 'asc',
                ],
                'size' => 1,
            ]
        ],
        'points' => [
            'exclude' => false,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.points', // 'LLL:EXT:' . $languageFile . ':tx_simpleblog_domain_model_post.points',
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
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'range_start, range_end, --linebreak--, date_start_b_c, date_end_b_c',
        ],
    ]
];
