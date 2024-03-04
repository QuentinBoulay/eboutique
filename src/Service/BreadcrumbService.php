<?php

namespace App\Service;

class BreadcrumbService
{
    private array $breadcrumbs = [];

    public function add(string $title, string $route): void
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'route' => $route
        ];
    }
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}