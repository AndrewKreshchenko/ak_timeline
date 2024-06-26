<?php
/**
 * Extension local configuration
 */

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
  function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
      'TimelineVis',
      'Listing',
      [
        \AK\TimelineVis\Controller\TimelineController::class => 'show, dispatch'
      ],
      // non-cacheable actions (in order to show fresh results)
      [
        \AK\TimelineVis\Controller\TimelineController::class => 'show, dispatch'
      ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
      'TimelineVis',
      'TestDemand',
      [
        \AK\TimelineVis\Controller\TimelineController::class => 'list',
      ]
    );

    // Add PageTSConfig (chapter 6)
    $languageFile = 'ak_timeline/Resources/Private/Language/locallang_be.xlf';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
      'mod {
        wizards.newContentElement.wizardItems.plugins {
          elements {
            listing {
              iconIdentifier = timelinevis-plugin-listing
              title = LLL:EXT:' . $languageFile. ':tx_timelinevis_listing.name
              description = LLL:EXT:' . $languageFile. ':tx_timelinevis_listing.description
              tt_content_defValues {
                CType = list
                list_type = timelinevis_listing
              }
            }
          }
          show = *
        }
      }'
    );

    // Register extension icon (chapter 6)
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
      \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
      'timelinevis-plugin-listing',
      \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
      ['source' => 'EXT:ak_timeline/Resources/Public/Icons/user_plugin_listing.svg']
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\AK\TimelineVis\Evaluation\TimelineValidator::class] = '';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\AK\TimelineVis\Evaluation\PointValidator::class] = '';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ak_timeline_proc'] = \AK\TimelineVis\InfoUpdate::class;
  }
);
