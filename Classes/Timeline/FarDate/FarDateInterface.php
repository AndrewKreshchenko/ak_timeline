<?php

/*
 * This file is part of the package ak/ak-timelinevis.
 * 
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace AK\TimelineVis\Timeline\FarDate;

/**
 * Structure of far date object
 * 
 * Constructing array of long integers that in sum are timestamp FarDate,
 * considering "B. C." indicator (setFarDateTimestamp method).
 * Intended to lead UNIX-like timestamp to needed standard at the beginning,
 * so that in the result FarDate timestamp of zero year would be equal to 0.
 * 
 * Example
 * ========
 * 
 * Given date 100-01-01T00:00:00 A. D. (timestamp is -59011466524)
 * -59011466524 - -62167219200 = 3155752676 (2069 year)
 * 2069 - 1970 (Unix epoch) ~= 100
 * (tested with https://timestamp.online)
 */

interface FarDateInterface
{
  const UNIX_EPOCH = -62167219200;

  public function __construct(int $timestamp, bool $isBC);

  public function getFarDateTimestamp(): int;

  public function getDateBC(): bool;

  public function getFarDate(): ?array;
}
