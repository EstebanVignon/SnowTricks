<?php

namespace App\Action;

final class Home
{
    public function __invoke()
    {
        dd("Bienvenue sur la page d'accueil");
    }
}