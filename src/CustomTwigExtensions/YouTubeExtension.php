<?php

declare(strict_types=1);


namespace App\CustomTwigExtensions;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class YouTubeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('youtubeLinkToId', [$this, 'convertYoutubeVideoLinkToVideoId'])
        ];
    }

    public function convertYoutubeVideoLinkToVideoId($youtubeLink)
    {
        $explode = explode("v=", $youtubeLink);
        $result = explode("&", $explode[1]);
        return $result[0];
    }
}