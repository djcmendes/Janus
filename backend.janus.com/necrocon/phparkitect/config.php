<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\DependsOnlyOnTheseNamespaces;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\NotDependsOnTheseNamespaces;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;

return static function (Config $config): void {

    $srcSet = ClassSet::fromDir(__DIR__ . '/../../src');

    $rules = [];

    // ── Domain must not depend on Infrastructure or Presentation ─────────
    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\..Domain..'))
        ->should(new NotDependsOnTheseNamespaces(
            'App\..Infrastructure..',
            'App\..Presentation..',
        ))
        ->because('Domain is the innermost layer and must remain framework-free.');

    // ── Application must not depend on Infrastructure or Presentation ─────
    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\..Application..'))
        ->should(new NotDependsOnTheseNamespaces(
            'App\..Infrastructure..',
            'App\..Presentation..',
        ))
        ->because('Application orchestrates Domain; it must not know about delivery or persistence details.');

    // ── Infrastructure must not depend on Presentation ────────────────────
    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\..Infrastructure..'))
        ->should(new NotDependsOnTheseNamespaces('App\..Presentation..'))
        ->because('Infrastructure provides persistence; it must not depend on HTTP controllers.');

    // ── Controllers must live under Presentation\Controller ───────────────
    $rules[] = Rule::allClasses()
        ->that(new HaveNameMatching('*Controller'))
        ->should(new ResideInOneOfTheseNamespaces('App\..Presentation\Controller'))
        ->because('Controllers are a Presentation concern and must not leak into other layers.');

    // ── Handlers must live under Application\Handler ──────────────────────
    $rules[] = Rule::allClasses()
        ->that(new HaveNameMatching('*Handler'))
        ->should(new ResideInOneOfTheseNamespaces('App\..Application\Handler'))
        ->because('Handlers are an Application concern.');

    // ── DTOs must live under Application\DTO ─────────────────────────────
    $rules[] = Rule::allClasses()
        ->that(new HaveNameMatching('*Dto'))
        ->should(new ResideInOneOfTheseNamespaces('App\..Application\DTO', 'App\..Presentation\DTO'))
        ->because('DTOs belong to Application or Presentation layers only.');

    // ── Repositories (implementations) must live in Infrastructure ────────
    $rules[] = Rule::allClasses()
        ->that(new HaveNameMatching('*Repository'))
        ->that(new NotDependsOnTheseNamespaces('App\..Domain\Repository'))
        ->should(new ResideInOneOfTheseNamespaces('App\..Infrastructure\Repository', 'App\..Domain\Repository'))
        ->because('Repository implementations belong in Infrastructure; interfaces belong in Domain.');

    $config->add($srcSet, ...$rules);
};
