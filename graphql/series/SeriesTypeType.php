<?php

namespace app\graphql\series;

use app\graphql\helpers\EnumType;
use app\models\Series;

class SeriesTypeType extends EnumType
{
    public function __construct()
    {
        $config = [
            'values' => array_keys(Series::$types),
        ];

        parent::__construct($config);
    }
}