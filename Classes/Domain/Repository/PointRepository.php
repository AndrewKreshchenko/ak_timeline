<?php
/**
 * Point Repository
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Repository class: Point
 */
class PointRepository extends Repository
{
    /**
     * Returns the last point posted
     *
     * @return QueryResult
     */
    public function findLastRecordCreated(): QueryResult
    {
        $query = $this->createQuery();
        $query->setOrderings(['pointdate' => QueryInterface::ORDER_DESCENDING]);
        $query->setLimit(1);
        return $query->execute();
    }

    /**
     * Returns timeline points
     *
     * @param int uid - ID as value to get the timeline by a key
     * @return Typo3DbQueryParser
     */
    public function findPointsByTimelineUid(int $uid): ?QueryResult
    {
        $query = $this->createQuery();
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);
        $query->matching(
            $query->equals('timeline', $uid),
            // $query->logicalAnd(
            //     [
            //         $query->equals('timeline', $uid),
            //         $query->greaterThan('pid', 0),
            //     ]
            // )
        );

        return $query->execute();
    }
}
