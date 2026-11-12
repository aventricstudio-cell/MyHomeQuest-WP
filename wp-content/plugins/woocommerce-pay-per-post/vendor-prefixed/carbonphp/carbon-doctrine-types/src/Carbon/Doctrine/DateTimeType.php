<?php

namespace Pramadillo\PayForPost\Carbon\Doctrine;

use Pramadillo\PayForPost\Carbon\Carbon;
use Doctrine\DBAL\Types\VarDateTimeType;

class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<Pramadillo\PayForPost\Carbon> */
    use CarbonTypeConverter;
}
