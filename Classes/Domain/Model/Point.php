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
use AK\TimelineVis\Domain\Model\Point;

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
     * Point web link
     *
     * @var string
     */
    protected $source = '';

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $images;

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
     * order of Point
     *
     * @var int
     **/
    protected $order = 0;

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
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the web source
     *
     * @return string $source
     */
    public function getSource()
    {
        return $this->source;
    }

    public function getImages(): ?FileReference
    {
        $images = $this->images;
        if (!empty($images) && $images !== 0) {
            return $images;
        } else {
            return null;
        }
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
     * Get date B. C. flag
     * 
     * @return bool
     */
    public function getPointDateBC(): bool
    {
        return $this->pointdateBC;
    }

    /**
     * Get order
     * 
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
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
