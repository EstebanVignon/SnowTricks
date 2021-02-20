<?php

declare(strict_types=1);

namespace App\Responders;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewResponder
{
    /**
     * @var Environment
     */
    protected $templating;

    public function __construct(
        Environment $templating
    )
    {
        $this->templating = $templating;
    }

    /**
     * @param string $template
     * @param array $params
     * @return Response
     */
    public function __invoke(string $template, array $params): Response
    {
        return new Response($this->templating->render($template, $params));
    }
}