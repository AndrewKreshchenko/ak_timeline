<?php
/**
 * Timeline Repository
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use AK\TimelineVis\Domain\Model\Timeline;

// @TODO remove unused functions

/**
 * Repository class: Blog
 */
class TimelineRepository extends Repository
{
    /**
     * Returns timelines with a specific search term in the title
     *
     * @param string Search keyword
     * @return QueryResult
     */
    public function findSearchedTimeline($search): QueryResult
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

        $query = $this->createQuery();
        $query->matching($query->like('title', '%' . $search . '%'));
        $query->setOrderings(['title' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute();
    }

    /**
     * Returns timeline
     *
     * @param string type - key to get by from database
     * @param int id - ID as value to get the timeline by a key
     * @return Timeline|null
     */
    public function findTimeline(string $type = 'pid', int $id = 1): ?Timeline
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals($type, $id)
        );

        return $query->execute()->getFirst();
    }

    /**
     * Returns timelines match ID of parent timeline
     * 
     * @TODO order by B. C.
     *
     * @param string type - key to get by from database
     * @param int pid - ID as value to get the timeline by a key
     * @return QueryResult
     */
    public function findTimelinesSegments(int $parentId): QueryResult
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('parentId', $parentId)
        );

        $query->setOrderings(['rangeStart' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute();
    }

    /**
     * Returns the last Timeline created
     *
     * @return QueryResult
     */
    public function findLastRecordCreated(): QueryResult
    {
        $query = $this->createQuery();
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->setLimit(1);
        return $query->execute();
    }


    /**
     * All Queries withoud storagePID
     *
     * @return QueryInterface
     */
    public function createQuery(): QueryInterface
    {
        $query = parent::createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setRespectSysLanguage(false);
        return $query;
    }
}
