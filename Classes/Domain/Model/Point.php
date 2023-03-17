<?php
/**
 * Point Domain Model
 *
 * @package EXT:ak-timelinevis
 * @author Andrii Kreshchenko <mail2andyk@gmail.com>
 */

namespace AK\TimelineVis\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Annotation\Validate;

/**
 * Domain Model: Point
 */
class Point extends AbstractEntity
{
    /**
     * Point title
     *
     * @var string
     * @Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * Point text content
     *
     * @var string
     */
    protected $description = '';

    /**
     * pointdate
     *
     * @var \DateTime
     */
    protected $pointdate = null;

    /**
     * B. C. date flag
     *
     * @var bool
     **/
    protected $pointdateBC = false;

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
     * Returns the pointdate
     *
     * @return \DateTime $pointdate
     */
    public function getPointdate()
    {
        return $this->pointdate;
    }

    /**
     * Sets the pointdate
     *
     * @param \DateTime $pointdate
     * @return void
     */
    public function setPointdate(\DateTime $pointdate)
    {
        $this->pointdate = $pointdate;
    }

    /**
     * Get date B. C. flag
     * 
     * @return bool
     */
    public function getPointDateBC(): bool
    {
        return $this->pointdateBC;
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
}
