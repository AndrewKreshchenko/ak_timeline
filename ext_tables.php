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
    }
);
