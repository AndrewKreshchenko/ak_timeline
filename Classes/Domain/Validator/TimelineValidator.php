<?php
namespace AK\TimelineVis\Domain\Validator;

use DateTime;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class TimelineValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
  protected function isValid($range)
  {
    // if (! $user instanceof \AK\TimelineVis\Domain\Model\User) { $this->addError('The given Object is not a User.', 1262341470);}
    // if (! $rangeStart instanceof \AK\TimelineVis\Domain\Model\Timeline) {
    //   $this->addError('The given Object is not a Timeline.', 1262341470);
    //   return;
    // }

    // @TODO make later "!$value instanceof WideDateTime"

    $result = true;

    if (!isset($range)) {
      throw new InvalidValidationOptionsException(
        'The operator doest not exist',
        1492090573991
      );
      $result = false;
    }

    if (is_null($range) || !($range instanceof DateTime)) {
      $this->addError('The datetime range is wrong.', 1262341470);
      $result = false;
    }

    // $result = true;
    // $firstPropertyName = $this->options['start'];
    // $secondPropertyName = $this->options['end'];
    // $operator = $this->options['operator'];

    // if (!in_array($operator, $this->validOperatorMethods)) {
    //   throw new InvalidValidationOptionsException(
    //       'The operator: "' . $operator . '" doest not exist',
    //       1492090573991
    //   );
    // }

    // $firstValue = ObjectAccess::getProperty($object, $firstPropertyName);
    // $secondValue = ObjectAccess::getProperty($object, $secondPropertyName);

    // if ($firstValue instanceof DateTime && $secondValue instanceof DateTime) {
    //   $firstValue = $firstValue->getTimestamp();
    //   $secondValue = $secondValue->getTimestamp();
    // }

    // $result = greaterThan($firstValue, $secondValue);
  

    // if ($timeline->getRangeEnd() > $rangeStart) {
    //   $this->addError('The datetime range is wrong.', 1262341470);
    // }

    return $result;
  }

  /**
     * @param mixed $firstValue
     * @param mixed $secondValue
     * @return bool
     */
  private function greaterThan($firstValue, $secondValue)
  {
    return $firstValue > $secondValue;
  }
}
