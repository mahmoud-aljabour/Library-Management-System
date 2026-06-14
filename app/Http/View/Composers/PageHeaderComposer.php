<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class PageHeaderComposer
{
    public function compose(View $view): void
    {
        $routeName = Route::currentRouteName() ?? '';
        $data = $view->getData();

        $section = $data['book'] ?? $data['member'] ?? $data['author'] ?? $data['publisher'] ?? $data['category'] ?? null;

        $module = $this->moduleFromRoute($routeName);
        $action = $this->actionFromRoute($routeName);

        $pageTitle = $this->resolveTitle($routeName, $action, $section);
        $breadcrumbs = $this->resolveBreadcrumbs($module, $pageTitle);

        $view->with(compact('pageTitle', 'breadcrumbs'));
    }

    private function moduleFromRoute(string $routeName): ?array
    {
        $modules = [
            'books' => ['label' => 'Books', 'route' => 'books.index'],
            'authors' => ['label' => 'Authors', 'route' => 'authors.index'],
            'publishers' => ['label' => 'Publishers', 'route' => 'publishers.index'],
            'categories' => ['label' => 'Categories', 'route' => 'categories.index'],
            'members' => ['label' => 'Members', 'route' => 'members.index'],
            'borrowings' => ['label' => 'Borrowings', 'route' => 'borrowings.index'],
        ];

        foreach ($modules as $prefix => $module) {
            if (str_starts_with($routeName, $prefix . '.')) {
                return $module;
            }
        }

        return null;
    }

    private function actionFromRoute(string $routeName): string
    {
        if (! str_contains($routeName, '.')) {
            return 'index';
        }

        return substr($routeName, strrpos($routeName, '.') + 1);
    }

    private function resolveTitle(string $routeName, string $action, mixed $section): string
    {
        if ($routeName === 'dashboard') {
            return 'Dashboard';
        }

        $name = $this->sectionName($section);

        return match ($action) {
            'index' => match (true) {
                str_starts_with($routeName, 'books.') => 'Books',
                str_starts_with($routeName, 'authors.') => 'Authors',
                str_starts_with($routeName, 'publishers.') => 'Publishers',
                str_starts_with($routeName, 'categories.') => 'Categories',
                str_starts_with($routeName, 'members.') => 'Members',
                str_starts_with($routeName, 'borrowings.') => 'Borrowings',
                default => 'Library System',
            },
            'create' => 'Add ' . $this->singularModule($routeName),
            'edit' => 'Edit ' . ($name ?? $this->singularModule($routeName)),
            'show' => $name ?? $this->singularModule($routeName) . ' Details',
            default => 'Library System',
        };
    }

    private function resolveBreadcrumbs(?array $module, string $pageTitle): array
    {
        if (Route::currentRouteName() === 'dashboard') {
            return [
                ['label' => 'Dashboard', 'url' => null],
            ];
        }

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('dashboard')],
        ];

        if ($module && $module['label'] !== $pageTitle) {
            $breadcrumbs[] = [
                'label' => $module['label'],
                'url' => route($module['route']),
            ];
        }

        $breadcrumbs[] = ['label' => $pageTitle, 'url' => null];

        return $breadcrumbs;
    }

    private function sectionName(mixed $section): ?string
    {
        if (! is_object($section)) {
            return null;
        }

        return $section->name ?? $section->title ?? null;
    }

    private function singularModule(string $routeName): string
    {
        return match (true) {
            str_starts_with($routeName, 'books.') => 'Book',
            str_starts_with($routeName, 'authors.') => 'Author',
            str_starts_with($routeName, 'publishers.') => 'Publisher',
            str_starts_with($routeName, 'categories.') => 'Category',
            str_starts_with($routeName, 'members.') => 'Member',
            str_starts_with($routeName, 'borrowings.') => 'Borrowing',
            default => 'Item',
        };
    }
}
