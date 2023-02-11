<?php

// declare(strict_types=1);

namespace AK\TimelineVis\Hooks\Backend\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
// use AK\TimelineVis\Domain\Repository\TimelineRepository;

class TimelineItemsProcFunc {
  // NOTE fix a problem and use TimelineRepository (commented now)
  // Fatal error: Uncaught ArgumentCountError: Too few arguments to function TYPO3\CMS\Core\Imaging\IconFactory::__construct()

  // protected $timelineRepository;

  // public function injectTimelineRepository(TimelineRepository $timelineRepository): void
  // {
  //   $this->TimelineRepository = $timelineRepository;
  // }

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
      // $translatedItems = $items;

      if (isset($items) && is_array($items)) {
        $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $resultArray = $queryImage
          ->select($tableName . '.uid','pid', 'title')
          ->where(
            $queryImage->expr()->in('hidden', 0)
          )
          ->from($tableName)
          ->execute()->fetchAll();
         
        $result = [];
        // use LocalizationUtility:
        // $title = LocalizationUtility::translate(['uid' => $item[1]], $tableName);

        foreach (($resultArray  ?? []) as $item) {
          $result[] = [
            $item['title'], (int)$item['uid']
          ];
        }

        $result[] = ['None', 0];
        // $items = $translatedItems;
        return $result;
      }
    }
}
