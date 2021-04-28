<?php

declare(strict_types=1);

namespace App\CustomTwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class YouTubeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('youtubeLinkToId', [$this, 'convertYoutubeVideoLinkToVideoId'])
        ];
    }

    /**
     * Display YouTube ID for iframe generating : source www.youtube.com/watch?v=Q0gBzfe or Q0gBzfe
     * @param $youtubeLink
     * @return string
     */
    public function convertYoutubeVideoLinkToVideoId($youtubeLink): string
    {
        $explode = explode("v=", $youtubeLink);
        if (isset($explode[1])) {
            $result = explode("&", $explode[1]);
            return $result[0];
        }
        $explode = explode("://", $youtubeLink);
        return $explode[1];
    }
}
