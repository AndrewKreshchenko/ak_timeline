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

class TimelineItemsProcFunc {
  // @TODO fix a problem and use TimelineRepository (commented now)
  // Fatal error: Uncaught ArgumentCountError: Too few arguments to function TYPO3\CMS\Core\Imaging\IconFactory::__construct()
  // @TODO Also provide options for user to create Timeline widget (backend module)

  /**
   * Set Timeline stlye
   *
   * @param array &$config configuration array
   */
  public function getTimelineStyles(array &$config, TcaSelectItems $fObj)
  {
    // @TODO Provide opportunity for user to handle Timeline styles (backend module)

    $config['items'] = [
      ['Vertical, right-side line', 'verticalRight1'],
      ['Vertical, both-sides timestamps', 'verticalBothSides'],
      ['Horizontal', 'horizontal'],
      ['Pie', 'pie']
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
       
      $result = [
        ['None', 0]
      ];
      // @TODO use LocalizationUtility:
      // $title = LocalizationUtility::translate(['uid' => $item[1]], $tableName);

      foreach (($resultArray  ?? []) as $item) {
        $result[] = [
          $item['title'], (int)$item['uid']
        ];
      }

      return $result;
    }
  }
}
