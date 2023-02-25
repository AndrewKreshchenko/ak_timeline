<?php
/**
 * Extension configuration
 */

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_timelinevis_domain_model_timeline',
            'EXT:timelinevis/Resources/Private/Language/locallang_csh_tx_timelinevis_domain_model_timeline.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_timelinevis_domain_model_timeline'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_timelinevis_domain_model_point'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_timelinevis_timeline_content'
        );

        // Register backend module (chapter 16)
        // NOTE tmp. does't work
        // \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        //     'TimelineVis',
        //     'tools',
        //     'TimelinevisAdmin',
        //     'bottom',
        //     [
        //         'Dashboard' => 'index',
        //     ],
        //     [
        //         'access' => 'systemMaintainer',
        //         'icon' => 'EXT:timelinevis/Resources/Public/Icons/module-timelinevis.png',
        //         'labels' => 'LLL:EXT:timelinevis/Resources/Private/Language/locallang_mod.xlf',
        //     ]
        // );
    }
);
