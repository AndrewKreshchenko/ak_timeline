<?php

namespace AK\TimelineVis;

use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use \TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class InfoUpdate
{
    private const TABLE_NAME = 'tx_timelinevis_domain_model_point';

    /**
     * Hook for updating points order after save form records
     *
     * @param string $status
     * @param string $table - DB table to process
     * @param int $id - Timeline ID
     * @param array $fieldArray
     * @param $obj (TYPO3\CMS\Core\DataHandling\DataHandler)
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$obj): void
    {
        if ($table !== 'tx_timelinevis_domain_model_timeline') {
            return;
        }

        if (!is_int($id) || !array_key_exists(self::TABLE_NAME, $obj->datamap)) {
            return;
        }

        $updatedFields = $obj->datamap[self::TABLE_NAME];
        $orderCount = 0;

        // $this->updatePointsOrder($resultArray, $orderCount, $timelineId, true);
        $this->updatePointsOrder($updatedFields, $orderCount, $id, true);
        $this->updatePointsOrder($updatedFields, $orderCount, $id, false);
    }

    /**
     * Update order of Timeline points
     *
     * @param array|null $formData - Array of updated data to sort
     * @param int $orderCount - Iterator to handle all points (with both B. C. and A. D. dates)
     * @param int $timelineId - timeline ID
     * @param bool $withDataBC - B. C. indicator
     * @return void
     */
    protected function updatePointsOrder($formData, &$orderCount, $timelineId, $withDataBC = false) {
        $sortable = [];

        foreach (($formData ?? []) as $id => $item) {
            $dateV = new \DateTime($item['pointdate']);

            if ((bool)$item['pointdate_b_c'] == $withDataBC) {
                $sortable[] = array(
                    'uid' => $id,
                    'dateStr' => $item['pointdate'],
                    'date' => $dateV->getTimestamp(),
                );
            }
        }

        if ($withDataBC) {
            usort($sortable, function($next, $prev) {
                return $next['date'] < $prev['date'];
            });
        } else {
            usort($sortable, function($next, $prev) {
                return $next['date'] > $prev['date'];
            });
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::TABLE_NAME);

        for ($i = 0; $i <= count($sortable); $i++, ++$orderCount) {
            if (gettype($sortable[$i]['date']) == 'integer') {
                $queryBuilder->update(
                    self::TABLE_NAME,
                    [
                        'order' => $orderCount
                    ],
                    [
                        'timeline' => (int)$timelineId,
                        'uid' => (int)$sortable[$i]['uid']
                    ]
                );
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
}
