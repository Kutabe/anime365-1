<?php

namespace app\graphql\episode;

use app\graphql\helpers\ActiveRecordHelpers;
use app\graphql\helpers\ObjectType;
use app\graphql\translation\TranslationType;
use app\graphql\Types;
use app\models\Episode;

class EpisodeType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'id' => Types::id(),
                'translations' => [
                    'type' => Types::listOf(TranslationType::instance()),
                    'resolve' => ActiveRecordHelpers::relationResolver('translations')
                ],
                'episodeType' => new EpisodeTypeType(),
                'episodeInt' => Types::string(),
                'episode' => [
                    'type' => Types::string(),
                    'columns' => ['episodeType', 'episodeInt'],
                    'resolve' => function (Episode $source) {
                        return $source->episodeType . ' ' . $source->episodeInt;
                    }
                ],
                'isFirstUploaded' => [
                    'type' => Types::boolean(),
                    'description' => 'true when has at least one active translation',
                ],
                //'firstUploadedDateTime',
                'countViews' => [
                    'type' => Types::int(),
                    'description' => 'how many visitors watched this episode',
                ],
            ]
        ];
        parent::__construct($config);
    }
}
