<?php

namespace app\graphql\episode;

use app\graphql\helpers\EnumType;
use app\models\Episode;

class EpisodeTypeType extends EnumType
{
    public function __construct()
    {
        $config = [
            'values' => array_keys(Episode::$episodeTypes),
        ];

        parent::__construct($config);
    }
}