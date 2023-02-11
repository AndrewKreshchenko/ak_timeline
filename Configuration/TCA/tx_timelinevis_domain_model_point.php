<?php
/**
 * TYPO3 Configuration Array (TCA) for the Point domain model
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

$languageFile = 'ak_timeline/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title',
        // 'iconfile' => 'EXT:ak_timeline/Resources/Public/Icons/tx_timelinevis_domain_model_point.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title, description, source, pointdate',
    ],
    'types' => [
        '1' => [
            'showitem' => 'hidden, title, description, source, pointdate, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'
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
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.point',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'description' => [
          'exclude' => true,
          'label' => 'Point content', // 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.pointcontent',
          'description' => 'Point text content',
          'config' => [
              'type' => 'text',
              'enableRichtext' => true
          ]
        ],
        'source' => [
            'label' => 'Source',
            'config' => [
                'type' => 'input',
                'size' => 59,
                'eval' => 'www',
            ],
        ],
        'pointdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.pointdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'dbType' => 'date',
                'eval' => 'date,required',
                // 'default' => 0,
                // 'range' => [
                //     'upper' => mktime(0, 0, 0, 1, 1, 2038)
                // ]
            ],

            // 'type' => 'input',
            // 'renderType' => 'inputDateTime',
            // 'eval' => 'datetime,int',
            // 'default' => 0,
            // 'range' => [
            //     'upper' => mktime(0, 0, 0, 1, 1, 2038)
            // ]
        ],
    ],
];
