<?php
/**
 * Timeline Domain Model
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
// use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use AK\TimelineVis\Domain\Repository\PointRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Domain Model: Blog
 */
class Timeline extends AbstractEntity
{
    /**
     * Blog title
     *
     * @var string
     * @Validate("TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator")
     */
    protected $title = '';

    /**
     * Description of the Blog
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("StringLength", options={"minimum": 3})
     */
    protected $description = '';

    // @TYPO3\CMS\Extbase\Annotation\Validate("AK\TimelineVis\Domain\Validator\TimelineValidator")

    /**
     * The range in the timeline part
     *
     * @var \DateTime
     * 
     **/
    protected $rangeStart;

    /**
     * B. C. date start flag
     *
     * @var bool
     **/
    protected $dateStartBC = false;

    /**
     * The range in the timeline part
     *
     * @var \DateTime
     **/
    protected $rangeEnd;

    /**
     * B. C. date end flag
     *
     * @var bool
     **/
    protected $dateEndBC = false;

    /**
     * ID of a parent timeline element
     *
     * @var int
     **/
    protected $parentId = 0;

    /**
     * ID of a parent timeline element
     *
     * @var int
     **/
    protected $segments = 0;

    /**
     * Timeline points
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AK\TimelineVis\Domain\Model\Point>
     */
    protected $points = null;

    // /**
    //  * Pagination of timeline
    //  *
    //  * @var int
    //  **/
    // protected $enablePagination = false;

    /**
     * Creation timestamp
     *
     * @var int
     */
    protected $crdate;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Constructor
     */
    public function __construct($name = '', $description = '') {
        $this->setName($name);
        $this->setDescription($description);
    }

    // public function __construct()
    // {
    //     //Do not remove the next line: It would break the functionality
    //     $this->initStorageObjects();
    // }

    // /**
    //  * Initializes all ObjectStorage properties
    //  * Do not modify this method!
    //  * It will be rewritten on each save in the extension builder
    //  * You may modify the constructor of this class instead
    //  *
    //  * @return void
    //  */
    // protected function initStorageObjects()
    // {
    //     $this->posts = new ObjectStorage();
    // }

    // /**
    //  * Initializes all ObjectStorage properties
    //  * Do not modify this method!
    //  * It will be rewritten on each save in the extension builder
    //  * You may modify the constructor of this class instead
    //  *
    //  * @return void
    //  */
    // protected function initStorageObjects()
    // {
    //     $this->points = new ObjectStorage();
    // }

    /**
     * Returns the creation timestamp
     *
     * @return int
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @return \DateTime
     */
    public function getRangeStart(): ?\DateTime
    {
        return $this->rangeStart;
    }

    // TYPO3 \CMS\Extbase\Annotation\Validate("AK\TimelineVis\Domain\Validator\TimelineValidator", start="rangeStart", operator="greaterThan" end="rangeEnd")

    // /**
    //  * @param \DateTime $rangeStart
    //  *
    //  * @return Timeline
    //  */
    // public function setRangeStart(\DateTime $rangeStart): self
    // {
    //     $this->rangeStart = $rangeStart;

    //     return $this;
    // }

    /**
     * @return \DateTime
     */
    public function getRangeEnd(): ?\DateTime
    {
        return $this->rangeEnd;
    }

    // /**
    //  * @param \DateTime $rangeEnd
    //  *
    //  * @return Timeline
    //  */
    // public function setRangeEnd(\DateTime $rangeEnd): self
    // {
    //     $this->rangeEnd = $rangeEnd;
    //     return $this;
    // }

    public function setParentId(int $parentId) {
        $this->parentId = $parentId;
    }

    /**
     * Returns ID of parent timeline
     *
     * @return int
     */
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * Returns the points
     *
     * // param bool indicator the points should be ordered
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AK\TimelineVis\Domain\Model\Point> $points
     */
    public function getPoints()
    {
        // bool $ordering = false
        // if ($ordering) {
        //     $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        //     $logger->warning('getPoints ' . count($this->points) . $this->points[0]['title']);
        // }

        return $this->points;
    }

    /**
     * Returns sorted points
     * 
     * @param int $timelineId - ID of timeline
     * @return array|\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AK\TimelineVis\Domain\Model\Point> $points
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function getSortedPoints(int $timelineId)
    {
        // $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        // $logger->warning('getPoints ' . count($this->points) . $this->points[0]['title']);

        // return $this->points;
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(PointRepository::class)
            ->findPointsByTimelineUid($timelineId, 'order');
    }

    /**
     * Sets the points
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AK\TimelineVis\Domain\Model\Point> $points
     * @return void
     */
    public function setPoints(ObjectStorage $points)
    {
        $this->points = $points;
    }

    // /**
    //  * @return int
    //  */
    // public function getEnablePagination(): ?int
    // {
    //     return $this->enablePagination;
    // }

    // /**
    //  * @param int $enablePagination
    //  *
    //  * @return Timeline
    //  */
    // public function setEnablePagination(int $enablePagination): self
    // {
    //     $this->enablePagination = $enablePagination;
    //     return $this;
    // }

    /**
     * Get date start B. C. flag
     * 
     * @return bool
     */
    public function getDateStartBC(): ?int
    {
        return $this->dateStartBC;
    }

    // /**
    //  * @param bool $dateStartBC
    //  *
    //  * @return void
    //  */
    // public function setDateStartBC(int $dateStartBC): void
    // {
    //     $this->dateStartBC = $dateStartBC;
    // }

    /**
     * Get date end B. C. flag
     * 
     * @return bool
     */
    public function getDateEndBC(): ?int
    {
        return $this->dateEndBC;
    }

    // /**
    //  * @param bool $dateEndBC
    //  *
    //  * @return void
    //  */
    // public function setDateEndBC(int $dateEndBC): void
    // {
    //     $this->dateEndBC = $dateEndBC;
    // }
}
