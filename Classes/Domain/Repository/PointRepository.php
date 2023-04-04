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
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Repository class: Point
 */
class PointRepository extends Repository
{
    /**
     * Returns point
     *
     * @param string type - key to get by from database
     * @param int id - ID as value to get the timeline by a key
     * @return Point|null
     */
    public function findPoint(string $type = 'pid', int $id = 0): ?Point
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals($type, $id)
        );

        return $query->execute()->getFirst();
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
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
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
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        $logger->warning('findPointsByTimelineUid ' . $uid);

        if (strlen($ordering) > 0) {
            $logger->warning('findPointsByTimelineUid ordering ' . $ordering);
            $query->setOrderings(['order' => QueryInterface::ORDER_ASCENDING]);
        }

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
