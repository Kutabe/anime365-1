<?php

namespace app\models;

use Yii;

/**
 * Table for anime catalog.
 * "Series" is either anime series, movie, OVA or other anime.
 *
 * @property integer $id
 * @property integer $numberOfEpisodes
 * @property integer $fansubsId
 * @property integer $worldArtId if > 0 then "animation", if < 0 then "cinema"
 * @property integer $myAnimeListId
 * @property integer $aniDbId
 * @property integer $animeNewsNetworkId
 * @property integer $imdbId
 * @property string $links other useful links
 * @property integer $isAiring = 1 when airing or aired recently
 * @property integer $isReallyAiring = 1 when really airing
 * @property integer $isActive = 1 when has some translations
 * @property string $type 'tv', 'movie', 'ova', etc, see more in self::$types
 * @property string $extraSources extra source ids like myAnimeListId, used if several ids exists for one series
 * @property integer $isHentaiValue 1 = has hentai in genres
 * @property string $posterUrl link to poster
 * @property double $worldArtScore
 * @property integer $worldArtTopPlace
 * @property string $season
 * @property integer $year
 * @property double $myAnimeListScore
 * @property double $episodeDuration typical episode duration in minutes
 * @property string $titleRu title in Russian (can be empty)
 * @property string $titleOriginal title in Romaji (shouldn't be empty)
 * @property string $titleShort short title (can be empty)
 * @property integer $countViews how many visitors watched this series (at least one episode)
 *
 * @property Episode $episodes
 *
 * @property string $posterUrlExternal deprecated
 * @property string $posterUrlSmall deprecated
 * @property string $exclude deprecated
 * @property string $specialsValue deprecated
 * @property string $posterLastCheck deprecated
 */
class Series extends \yii\db\ActiveRecord
{
    // Variants for $this->type
    public static $types = [
        'tv' => 'ТВ series',
        'ova' => 'OVA',
        'ona' => 'ONA',
        'movie' => 'Movie',
        'special' => 'Special',
        'music' => 'Music video'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'series';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    public function getEpisodes()
    {
        return $this->hasMany(Episode::className(), ['seriesId' => 'id'])->andWhere(['isActive' => 1])->andWhere(['not in', 'episodeType', ['preview', 'ending', 'opening', 'menu', 'bonus', 'other']])->orderBy("[[episodeType]] = 'tv' DESC, [[episodeType]] = 'movie' DESC, [[episodeType]] = 'ona' DESC, [[episodeType]] = 'ova' DESC, [[episodeType]] = 'special' DESC, [[episodeInt]] ASC");
    }
}
