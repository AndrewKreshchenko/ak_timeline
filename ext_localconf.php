<?php
/**
 * Extension local configuration
 */

defined('TYPO3_MODE') || die('Access denied.');

// NOTE for Typo3 v.8 - configurePlugin will be a little bit another:
// \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
//   'TimelineVis',
//   'Listing',
//   [
//     'Timeline' => 'list, addForm, add, show, updateForm, update, deleteConfirm, delete',
//   ],
//   // non-cacheable actions ...
// );

call_user_func(
  function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
      'TimelineVis',
      'Listing',
      [
        \AK\TimelineVis\Controller\TimelineController::class => 'list, addForm, add, show, updateForm, update, deleteConfirm, delete',
      ],
      // non-cacheable actions
      [
        \AK\TimelineVis\Controller\TimelineController::class => 'list, addForm, add, show, updateForm, update, deleteConfirm, delete',
      ]
    );

    // Add PageTSConfig (chapter 6)
    $languageFile = 'ak_timeline/Resources/Private/Language/locallang_db.xlf';
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

    // Register TypeConverter (chapter 15)
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(
      \AK\TimelineVis\Property\TypeConverter\UploadedFileReferenceConverter::class
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\AK\TimelineVis\Evaluation\TimelineValidator::class] = '';

    // Draw content into content elements
    // NOTE Intended to be prepared later
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ak_timeline_div'] = \AK\TimelineVis\Div::class;
  }
);
