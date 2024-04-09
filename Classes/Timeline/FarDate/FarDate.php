<?php

declare(strict_types=1);

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Timeline\FarDate;

use \AK\TimelineVis\Timeline\FarDate\FarDateInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

final class FarDate implements FarDateInterface
{
  protected $farDate;

  protected $timestamp;

  protected $isBC = false;

  /**
   * @param $farDate - Consists of "date" part as array of integers, and "isBC" bool indicator that indicates the era.
   * "date" part is reduced when FarDate timestamp is being calculed
   * 
   * @todo Maintain Open/Closed and Interface Segregation principles (https://phptherightway.com/#dependency_injection)
   */
  public function __construct(int $timestamp, bool $isBC)
  {
    $this->timestamp = $timestamp;
    $this->isBC = $isBC;

    $this->constructFarDate();
  }

  public function getFarDateTimestamp(): int
  {
    if (isset($this->farDate) && !is_null($this->farDate)) {
      return $this->farDate['timestamp'];
    }

    return $this->timestamp;
  }

  public function getDateBC(): bool
  {
    return $this->isBC;
  }

  public function getFarDate(): ?array
  {
    return $this->farDate;
  }

  public function getEpoch(): int
  {
    return FarDateInterface::EPOCH_TSTAMP;
  }

  public function constructFarDate(): void
  {
    $farDateTimestamp = $this->timestamp - FarDateInterface::EPOCH_TSTAMP;

    $this->farDate = array(
      'timestamp' => $this->isBC ? -$farDateTimestamp : $farDateTimestamp,
      'isBC' => $this->isBC
    );
  }

  public function logDate(): string
  {
    $date = new \DateTime();

    return  $date->setTimestamp($this->timestamp)->format('Y-m-d');
  }
}
