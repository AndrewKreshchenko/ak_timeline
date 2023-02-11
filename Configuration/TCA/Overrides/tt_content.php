<?php
/**
 * TYPO3 Configuration Array (TCA) to override the tt_content model
 *
 * @package EXT:simpleblog
 * @author Michael Schams <michael@example.com>
 * @link https://www.extbase-book.org
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


// $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['timelineexample'] = 'select_key';
// $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['timelineexample'] = 'pi_flexform,recursive';

// ExtensionManagementUtility::addPiFlexFormValue(
//     'timelineexample',
//     'FILE:EXT:timeline_example/Configuration/FlexForms/PluginSettings.xml'
// );
