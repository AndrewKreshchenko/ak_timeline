<?php

declare(strict_types=1);

/**
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * 
 * @TODO There is gonna be an implementation
 */

namespace AK\TimelineVis\Timeline;

use \AK\TimelineVis\Timeline\WidgetInterface;

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Widget that used for uset interaction with Timeline visual part
 */

class Widget implements WidgetInterface
{
  // public function withWidget(string $widgetRegistry, int $timelineId) {}

  public function getTimelineRegion(int $regionDateStart, int $regionDateEnd): int
  {
    return $this->farDate->getFarDateNumber();
  }

  public function getPointsByCategory(array $categoryId): ?array {
    return null;
  }
}
