<?php

namespace AK\TimelineVis;

use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use \TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use \TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\DataHandling\DataHandler;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

// use TYPO3\CMS\Core\Log\LogManager;

// Notice: sorting will work on saving form the second time, if new point was added

class Div
{
    private const ERA_BEGIN = -62167219200;

    /**
     * Hook for clear page caches on video change
     *
     * @param string $status
     * @param string $table
     * @param int $id
     * @param array $fieldArray
     * @param $obj
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$obj): void
    {
        if ($table !== 'tx_timelinevis_domain_model_timeline') {
            return;
        }

        // Clear Cache
        $table = 'tt_content';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $res = $queryBuilder->select('content.uid', 'content.pid')
            ->from($table, 'content')
            ->leftJoin(
                'content',
                'tx_timelinevis_timeline_content',
                'timeline_content',
                $queryBuilder->expr()->eq('content.uid', $queryBuilder->quoteIdentifier('timeline_content.content_uid'))
            )
            ->where($queryBuilder->expr()->eq('timeline_content.timeline_uid',
                $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)))
            ->execute()->fetchAll();
        $pages = [];
        foreach ($res as $r) {
            $pages[] = $r['pid'];
        }
        /** @var DataHandler $cache */
        $cache = GeneralUtility::makeInstance(DataHandler::class);
        if (!is_object($cache->BE_USER)) {
            $cache->BE_USER = $GLOBALS['BE_USER'];
        }
        foreach ($pages as $pid) {
            $cache->clear_cacheCmd($pid);
        }

        // General Storage Folder
        if ($id = self::getGeneralStorageFolder()) {
            $fieldArray['pid'] = $id;
        }

        // Order points
        $formData = GeneralUtility::_GP('data');
        $timelineId = key($formData['tx_timelinevis_domain_model_timeline']);

        if (!is_int($timelineId)) {
            return;
        }

        $queryImage = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_timelinevis_domain_model_point');
        $resultArray = $queryImage
            ->select('tx_timelinevis_domain_model_point' . '.uid','order','pointdate', 'pointdate_b_c')
            ->where(
                $queryImage->expr()->in('timeline', $timelineId)
            )
            ->from('tx_timelinevis_domain_model_point')
            ->execute()->fetchAll();

        // $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        // $logger->warning($value . ' - Div Timeline ID is ' . $timelineId . ', SQL query is ' . $queryImage->getSQL()); // . ', SQL query is ' . $queryImage->getSQL()
        
        $sortable = [];
        // @TODO use LocalizationUtility:
        // $title = LocalizationUtility::translate(['uid' => $item[1]], $tableName);

        foreach (($resultArray  ?? []) as $item) {
            $dateV = new \DateTime($item['pointdate']);
            $sortable[] = array('uid' => $item['uid'], 'date' => $item['pointdate_b_c'] ? $dateV->getTimestamp() + self::ERA_BEGIN : $dateV->getTimestamp());
        }

        usort($sortable, function($a, $b) {
            return $a['date'] > $b['date'];
        });

        $queryBuilder->getRestrictions()->removeAll();

        for ($i = 0; $i <= count($sortable); $i++) {
            if (gettype($sortable[$i]['date']) == 'integer') {
                $queryBuilder->update('tx_timelinevis_domain_model_point')
                    ->set('order', $i)
                    ->where(
                        $queryBuilder->expr()->eq('timeline', (int)$timelineId),
                        $queryBuilder->expr()->eq('uid', (int)$sortable[$i]['uid'])
                    )->execute();
                // $queryBuilder->statement('UPDATE `tx_timelinevis_domain_model_point` SET `order`=' . $i . ' WHERE timeline=' . $timelineId . ' AND `uid`=' . $sorted[$i]['uid']);
            }
        }
    }

    /**
     * Get the general record storage ID
     *
     * @return int
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getGeneralStorageFolder()
    {
        return self::getConfigurationValue('generalStorageFolder');
    }

    /**
     * Get a configuration value
     *
     * @param $key
     * @return int
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getConfigurationValue($key)
    {
        $config = '';
        if (isset($config[$key])) {
            return (int)$config[$key];
        } else {
            return 0;
        }
    }

    protected function compare($a, $b) {
        return strcmp($a->name, $b->name);
    }
}
