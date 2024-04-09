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
        'order' => 'order',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:ak_timeline/Resources/Public/Icons/ak_timeline-point.png',
    ],
    'types' => [
        '1' => [
            'showitem' => 'hidden, title, description, source, images,
            --palette--;;paletteCore,
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
        'order' => [
            'readonly' => 1,
            'label' => 'order',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'default' => '0'
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
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'description' => [
          'exclude' => true,
          'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.description',
          'description' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.description.desc',
          'config' => [
              'type' => 'text',
              'enableRichtext' => true,
              'cols' => 20
            ]
        ],
        'source' => [
            'exclude' => false,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.source',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'placeholder' => 'A link text',
                'size' => 30,
                'eval' => 'www,trim',
                'softref' => 'typolink',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'images' => [
            'label' => 'Image ',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                [
                    'minitems' => 0,
                    'maxitems' => 1,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'pointdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.pointdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'dbType' => 'date',
                'eval' => 'date,required,' . \AK\TimelineVis\Evaluation\PointValidator::class,
            ],
        ],
        'pointdate_b_c' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_point.pointdatebc',
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
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'pointdate, --linebreak--, pointdate_b_c',
        ],
    ]
];
