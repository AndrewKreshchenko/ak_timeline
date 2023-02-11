<?php

namespace AK\TimelineVis;

use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use \TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use \TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\DataHandling\DataHandler;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class Div
{

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

        /* Clear Cache */
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

        /* General Storage Folder */
        if ($id = self::getGeneralStorageFolder()) {
            $fieldArray['pid'] = $id;
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
}
