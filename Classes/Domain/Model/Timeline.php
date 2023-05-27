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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use AK\TimelineVis\Domain\Repository\PointRepository;

// use TYPO3\CMS\Core\Log\LogManager;

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
     * @var ObjectStorage<Point>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $points = null;

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

    /**
     * @return \DateTime
     */
    public function getRangeEnd(): ?\DateTime
    {
        return $this->rangeEnd;
    }

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
     * @return ObjectStorage<Point> $points
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Returns sorted points
     * 
     * @param int $timelineId - ID of timeline
     * @return array|ObjectStorage<Point> $points
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function getSortedPoints(int $timelineId)
    {
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(PointRepository::class)
            ->findPointsByTimelineUid($timelineId, 'order')->toArray();
    }

    /**
     * Sets the points
     *
     * @param ObjectStorage<Point> $points
     * @return void
     */
    public function setPoints(ObjectStorage $points)
    {
        $this->points = $points;
    }

    /**
     * Get date start B. C. flag
     * 
     * @return bool
     */
    public function getDateStartBC(): ?int
    {
        return $this->dateStartBC;
    }

    /**
     * Get date end B. C. flag
     * 
     * @return bool
     */
    public function getDateEndBC(): ?int
    {
        return $this->dateEndBC;
    }
}
