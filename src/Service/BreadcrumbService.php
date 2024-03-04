<?php

namespace App\Service;

class BreadcrumbService
{
    private array $breadcrumbs = [];

    public function add(string $title, string $route, array $arguments = []): void
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'route' => $route,
            'arguments' => $arguments
        ];
    }
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}