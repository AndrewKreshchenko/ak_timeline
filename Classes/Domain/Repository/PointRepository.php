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
use AK\TimelineVis\Domain\Model\Point;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository class: Point
 */
class PointRepository extends Repository
{
    /**
     * Returns point
     *
     * @param int uid - ID as value to get the timeline by a key
     * @return QueryResult|null
     */
    public function findLastCreatedPoint(int $uid): ?QueryResult
    {
        $query = $this->createQuery();
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->setLimit(1);

        $query->matching(
            $query->equals('timeline', $uid)
        );

        return $query->execute();
    }

    /**
     * Returns points array
     *
     * @param int $id - timeline's uid
     * @return Array|null
     */
    public function getPointData(int $id = 0)
    {
        $query = $this->createQuery();
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);
        $query->matching(
            $query->equals('timeline', $id)
        );

        $result = $query->execute()->toArray();
        $resultArray = [];

        foreach (($result ?? []) as $point) {
            $resultArray[] = array(
                'id' => $point->getUid(),
                'date' => $point->getPointdate(),
                'dateBC' => $point->getPointDateBC(),
                'title' => $point->getTitle(),
                'description' => $point->getDescription(),
                'source' => $point->getTitle(),
            );
        }

        return $resultArray;
    }

    /**
     * Returns timeline points
     *
     * @param int uid - ID as value to get the timeline by a key
     * @param string $ordering - order points by field (optional)
     * @return Typo3DbQueryParser
     */
    public function findPointsByTimelineUid(int $uid, string $ordering = ''): ?QueryResult
    {
        $query = $this->createQuery();
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);

        if (strlen($ordering) > 0) {
            $query->setOrderings(['order' => QueryInterface::ORDER_ASCENDING]);
        }

        $query->matching(
            $query->equals('timeline', $uid),
        );

        return $query->execute();
    }
}
