<?php declare(strict_types=1);

namespace Pagerfanta\Twig\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\RouteGenerator\RouteGeneratorDecorator;
use Pagerfanta\RouteGenerator\RouteGeneratorInterface;
use Pagerfanta\View\View;
use Twig\Environment;

final class TwigView extends View
{
    public const DEFAULT_TEMPLATE = '@Pagerfanta/default.html.twig';

    private string $template = self::DEFAULT_TEMPLATE;

    public function __construct(
        private readonly Environment $twig,
        private readonly ?string $defaultTemplate = null,
    ) {}

    public function getName(): string
    {
        return 'twig';
    }

    /**
     * @param PagerfantaInterface<mixed>       $pagerfanta
     * @param callable|RouteGeneratorInterface $routeGenerator
     * @param array<string, mixed>             $options
     *
     * @phpstan-param callable(int $page): string|RouteGeneratorInterface $routeGenerator
     */
    public function render(PagerfantaInterface $pagerfanta, callable $routeGenerator, array $options = []): string
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);

        $this->calculateStartAndEndPage();

        return $this->twig->load($this->template)->renderBlock(
            'pager_widget',
            [
                'pagerfanta' => $pagerfanta,
                'route_generator' => $this->decorateRouteGenerator($routeGenerator),
                'options' => $options,
                'start_page' => $this->startPage,
                'end_page' => $this->endPage,
                'current_page' => $this->currentPage,
                'nb_pages' => $this->nbPages,
            ]
        );
    }

    /**
     * @param (callable(int $page): string)|RouteGeneratorInterface $routeGenerator
     */
    private function decorateRouteGenerator(callable|RouteGeneratorInterface $routeGenerator): RouteGeneratorDecorator
    {
        return new RouteGeneratorDecorator($routeGenerator);
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function initializeOptions(array $options): void
    {
        if (isset($options['template'])) {
            $this->template = $options['template'];
        } elseif (null !== $this->defaultTemplate) {
            $this->template = $this->defaultTemplate;
        }

        parent::initializeOptions($options);
    }
}
