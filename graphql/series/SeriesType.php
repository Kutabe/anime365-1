<?php

namespace app\graphql\series;

use app\graphql\episode\EpisodeType;
use app\graphql\helpers\ActiveRecordHelpers;
use app\graphql\helpers\ObjectType;
use app\graphql\Types;
use app\models\Series;

class SeriesType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'id' => Types::id(),
                //'url' => Types::string(), // TODO: implement
                'episodes' => [
                    'type' => Types::listOf(EpisodeType::instance()),
                    'resolve' => ActiveRecordHelpers::relationResolver('episodes')
                ],
                'titleRu' => [
                    'type' => Types::string(),
                    'resolve' => function (Series $source) {
                        return !empty($source->titleRu) ? $source->titleRu : null; // TODO: make null-able in database?
                    }
                ],
                'titleOriginal' => [
                    'type' => Types::string(),
                    'resolve' => function (Series $source) {
                        return !empty($source->titleOriginal) ? $source->titleOriginal : null; // TODO: make null-able in database?
                    }
                ],
                'titleShort' => [
                    'type' => Types::string(),
                    'resolve' => function (Series $source) {
                        return !empty($source->titleShort) ? $source->titleShort : null; // TODO: make null-able in database?
                    }
                ],
                'posterUrl' => [
                    'type' => Types::string(),
                ],
                'type' => [
                    'type' => SeriesType::instance(),
                    'description' => 'Type of series, for example "tv", "movie", etc'
                ],
                'isOngoing' => [
                    'type' => Types::boolean(),
                    'columns' => 'isAiring',
                    'resolve' => function (Series $source) {
                        return $source->isAiring;
                    },
                    'description' => 'true when anime considered as ongoing (currently airing, or it will air soon, or it was aired recently)',
                ],
                'isAiring' => [
                    'type' => Types::boolean(),
                    'columns' => 'isReallyAiring',
                    'resolve' => function (Series $source) {
                        return $source->isReallyAiring;
                    },
                    'description' => 'true when series started airing/releasing but not finished (strict comparing to isOngoing)',
                ],
                'isHentai' => [
                    'type' => Types::boolean(),
                    'columns' => 'isHentaiValue',
                    'resolve' => function (Series $source) {
                        return $source->isHentaiValue;
                    },
                ],
                'isActive' => [
                    'type' => Types::boolean(),
                    'description' => 'true when series has at least one translation',
                ],
                'myAnimeListScore' => [
                    'type' => Types::float(),
                    'resolve' => function (Series $source) {
                        return $source->myAnimeListScore > 0 ? $source->myAnimeListScore : null; // TODO: make null-able in database?
                    },
                    'description' => 'Score on myanimelist.net',
                ],
                'worldArtScore' => [
                    'type' => Types::float(),
                    'resolve' => function (Series $source) {
                        return $source->worldArtScore > 0 ? $source->worldArtScore : null; // TODO: make null-able in database?
                    },
                    'description' => 'Score on world-art.ru',
                ],
                'worldArtTopPlace' => [
                    'type' => Types::int(),
                    'resolve' => function (Series $source) {
                        return $source->worldArtTopPlace > 0 ? $source->worldArtTopPlace : null; // TODO: make null-able in database?
                    },
                    'description' => 'Place in world-art.ru anime top',
                ],
                // TODO: Add "air start date", "air end date"
                'season' => [
                    'type' => Types::string(),
                ],
                'year' => [
                    'type' => Types::int(),
                ],
                'numberOfEpisodes' => Types::int(),
                'episodeDuration' => Types::float(),
                'fansubsId' => Types::int(),
                'worldArtId' => Types::int(),
                'myAnimeListId' => Types::int(),
                'aniDbId' => Types::int(),
                'animeNewsNetworkId' => Types::int(),
                'imdbId' => Types::int(),
                //'links' => Types::listOf(Types::string()),
                //'extraSources' => Types::listOf(Types::string())

                /*'countViews' => [ // TODO: looks like counter doesn't work properly
                    'type' => Types::int(),
                    'description' => 'how many visitors watched this series (at least one episode)',
                ],*/
            ]
        ];
        parent::__construct($config);
    }
}
