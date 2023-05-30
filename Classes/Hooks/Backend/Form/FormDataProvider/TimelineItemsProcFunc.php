<?php

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Hooks\Backend\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LanguageService;

class TimelineItemsProcFunc {
  /**
   * Set Timeline style
   *
   * @param array &$config configuration array
   */
  public function getTimelineStyles(array &$config, TcaSelectItems $fObj)
  {
    $locale = $this->getFileLocale();
    // @TODO add icons for visual representation of views
    // @TODO Provide opportunity for user to handle Timeline styles (backend module)

    $languageService = $this->getLanguageService();

    $config['items'] = [
      [
        $languageService->sL($locale . ':timeline.style.verticalright1'),
        'verticalRight1',
        // 'EXT:ak_timeline/Resources/Public/Icons/ak_timeline-point.png'
      ],
      [
        $languageService->sL($locale . ':timeline.style.verticalbothsides'),
        'verticalBothSides',
      ],
      [
        $languageService->sL($locale . ':timeline.style.horizontal'),
        'horizontal'
      ],
      [
        $languageService->sL($locale . ':timeline.style.horizontalmulti'),
        'horizontalMulti'
      ],
      [
        'Cascade Kaskade',
        'cascade'
      ],
      [
        $languageService->sL($locale . ':timeline.style.pie'),
        'pie'
      ]
    ];
  }

  /**
  * Modifies the segments list options
  *
  * @param array &$config configuration array
  */
  public function getTimelinesList(array &$config, TcaSelectItems $fObj)
  {
    // change this to dynamically populate the list!
    $config['items'] = $this->translateSelectorItems($config['items'], 'tx_timelinevis_domain_model_timeline');
  }

  /**
   * Translate selector items array
   *
   * @param array $items: array of value/label pairs
   * @param string $tableName: name of timelines table
   *
   * @return array array of value/translated label pairs
   */
  protected function translateSelectorItems($items, $tableName)
  {
    if (isset($items) && is_array($items)) {
      $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
      $resultArray = $queryImage
        ->select($tableName.'.uid','pid','title')
        ->where(
          $queryImage->expr()->in('hidden', 0)
        )
        ->from($tableName)
        ->execute()->fetchAll();
      
      // NOTE may be needed to translate
      $result = [
        ['None', 0]
      ];

      foreach (($resultArray  ?? []) as $item) {
        $result[] = [
          $item['title'], (int)$item['uid']
        ];
      }

      return $result;
    }
  }

  /**
   * Get file with localization
   *
   * @return string
   */
  protected static function getFileLocale(): string
  {
    $lang = $GLOBALS['BE_USER']->uc['lang'] ?? '';
    $lang = $lang == 'default' ? '' : $lang . '.';

    return 'LLL:EXT:ak_timeline/Resources/Private/Language/' . $lang . 'locallang_be.xlf';
  }

  /**
   * @return LanguageService
   */
  protected function getLanguageService(): LanguageService
  {
    return $GLOBALS['LANG'];
  }
}
