<?php

declare(strict_types=1);

namespace App\Actions;

use Symfony\Component\HttpFoundation\Response;

final class Home
{
    public function __invoke(): Response
    {
        return new Response("Bienvenue");
    }
}