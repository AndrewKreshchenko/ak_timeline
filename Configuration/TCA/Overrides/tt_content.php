<?php
/**
 * Timeline Controller
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use AK\TimelineVis\Div;

ExtensionUtility::registerPlugin(
    'TimelineVis',
    'Listing',
    'Timeline',
    'EXT:ak_timeline/Resources/Public/Icons/user_plugin_listing.svg'
);

// include FlexForm of plugin "Listing" of extension EXT:ak_timeline
$pluginSignature = 'timelinevis_listing';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:ak_timeline/Configuration/FlexForms/PluginSettings.xml'
);

/* column */
// Or \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', [...
$GLOBALS['TCA']['tt_content']['columns']['tx_' . 'timelinevis' . '_timelines'] = [
    'exclude' => false,
    'label' => 'LLL:EXT:ak_timeline/Resources/Private/Language/locallang.xlf:timelines',
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tx_timelinevis_timeline_content',
        'foreign_field' => 'content_uid',
        'foreign_label' => 'timeline_uid',
        'foreign_sortby' => 'sorting',
        'foreign_selector' => 'timeline_uid',
        'foreign_unique' => 'timeline_uid',
        'maxitems' => '1',
        'appearance' => [
            'collapseAll' => false, // working RTE in TYPO3 > 7.4?!?!
            'expandSingle' => true,
            'useCombination' => 1,
            'useSortable' => true,
            'enabledControls' => [
                'info' => true,
                'new' => true,
                'dragdrop' => true,
                'sort' => true,
                'hide' => true,
                'delete' => true,
                'localize' => true,
            ],
        ],
    ],
];

$GLOBALS['TCA']['tt_content']['columns']['tx_' . 'timelinevis' . '_point'] = [
    'exclude' => false,
    'label' => 'Points', // 'LLL:EXT:ak_timeline/Resources/Private/Language/locallang.xlf:points',
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tx_timelinevis_timeline_point',
        'foreign_field' => 'content_uid',
        'foreign_label' => 'timeline_uid',
        'foreign_sortby' => 'sorting',
        'foreign_selector' => 'timeline_uid',
        'foreign_unique' => 'timeline_uid',
        'maxitems' => '100',
        'appearance' => [
            'collapseAll' => false, // working RTE in TYPO3 > 7.4?!?!
            'expandSingle' => true,
            'useCombination' => 1,
            'useSortable' => true,
            'enabledControls' => [
                'info' => true,
                'new' => true,
                'dragdrop' => true,
                'sort' => true,
                'hide' => true,
                'delete' => true,
                'localize' => true,
            ],
        ],
    ],
];

$storageId = Div::getGeneralStorageFolder();
if ($storageId) {
    unset($GLOBALS['TCA']['tt_content']['columns']['tx_timelinevis_timelines']['config']['foreign_selector']);
    unset($GLOBALS['TCA']['tt_content']['columns']['tx_timelinevis_timelines']['config']['foreign_unique']);
    // unset($GLOBALS['TCA']['tt_content']['columns']['tx_timelinevis_points']['config']['foreign_selector']);
    // unset($GLOBALS['TCA']['tt_content']['columns']['tx_timelinevis_timelines']['config']['foreign_unique']);
}

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['timelinevis' . '_listing'] = 'layout,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['timelinevis' . '_listing'] = 'pi_flexform,tx_timelinevis_timelines';

// ExtensionManagementUtility::addTCAcolumns('tx_timelinevis_domain_model_timeline', [
// 	'title' => [
//         'exclude' => false,
//         'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.title',
//         'config' => [
//             'type' => 'input',
//             'size' => 30,
//             'eval' => 'trim,required'
//         ],
//     ],
//     'description' => [
//         'exclude' => false,
//         'label' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.description',
//         'description' => 'LLL:EXT:' . $languageFile . ':tx_timelinevis_domain_model_timeline.description.desc',
//         'config' => [
//             'type' => 'text',
//             'enableRichtext' => true
//         ]
//     ],
//     'range_start' => [
//         'exclude' => false,
//         'label' => 'range start',
//         'config' => [
//             'type' => 'input',
//             'size' => 10,
//             'renderType' => 'inputDateTime',
//             'dbType' => 'date',
//             'eval' => 'date,' . \AK\TimelineVis\Evaluation\TimelineValidator::class
//         ],
//     ],
//     'range_end' => [
//         'exclude' => false,
//         'label' => 'range end',
//         'config' => [
//             'type' => 'input',
//             'size' => 10,
//             'renderType' => 'inputDateTime',
//             'dbType' => 'date',
//             'eval' => 'date,' . \AK\TimelineVis\Evaluation\TimelineValidator::class
//         ]
//     ],
// ]);

// ExtensionManagementUtility::addToAllTCAtypes('tx_timelinevis_domain_model_timeline', 'title, description, range_start, range_end', '', 'after:title');


// $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['timelineexample'] = 'select_key';
// $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['timelineexample'] = 'pi_flexform,recursive';

// ExtensionManagementUtility::addPiFlexFormValue(
//     'timelineexample',
//     'FILE:EXT:timeline_example/Configuration/FlexForms/PluginSettings.xml'
// );
