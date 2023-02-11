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
     * Point
     *
     * @var string
     * @Validate("NotEmpty")
     */
    protected $point = '';

    /**
     * Point text content
     *
     * @var string
     */
    protected $pointcontent = '';

    /**
     * pointdate
     *
     * @var \DateTime
     */
    protected $pointdate = null;

    /**
     * Creation timestamp
     *
     * @var int
     */
    protected $crdate;

    /**
     * Returns the point
     *
     * @return string $point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Sets the point
     *
     * @param string $point
     * @return void
     */
    public function setPoint($point)
    {
        $this->point = $point;
    }

    /**
     * Returns the pointcontent
     *
     * @return string $pointcontent
     */
    public function getPointcontent()
    {
        return $this->pointcontent;
    }

    /**
     * Sets the pointcontent
     *
     * @param string $pointcontent
     * @return void
     */
    public function setPointcontent($pointcontent)
    {
        $this->pointcontent = $pointcontent;
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
     * Returns the creation timestamp
     *
     * @return int
     */
    public function getCrdate()
    {
        return $this->crdate;
    }
}
