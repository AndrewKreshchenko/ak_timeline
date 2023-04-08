<?php

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * 
 * @TODO maintain "Inversion of Control" principle
 * https://martinfowler.com/articles/injection.html
 */

namespace AK\TimelineVis\Timeline;

interface WidgetInterface
{
  // public function withWidget(string $widgetRegistry, int $timelineId): WidgetInterface;

  public function getTimelineRegion(int $regionDateStart, int $regionDateEnd);

  public function getPointsByCategory(array $categoryId): ?array;
}
