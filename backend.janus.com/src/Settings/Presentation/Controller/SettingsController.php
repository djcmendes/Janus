<?php

declare(strict_types=1);

namespace App\Settings\Presentation\Controller;

use App\Settings\Infrastructure\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings', name: 'settings_')]
final class SettingsController extends AbstractController
{
    public function __construct(
        private readonly SettingsRepository $settingsRepository,
    ) {}

    /**
     * GET /settings
     * Returns the current project settings.
     */
    #[Route('', name: 'get', methods: ['GET'])]
    public function get(): JsonResponse
    {
        $settings = $this->settingsRepository->getOrCreate();

        return $this->json(['data' => $settings->toArray()]);
    }

    /**
     * PATCH /settings
     * Updates one or more settings fields.
     */
    #[Route('', name: 'patch', methods: ['PATCH'])]
    public function patch(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data     = json_decode($request->getContent(), true) ?? [];
        $settings = $this->settingsRepository->getOrCreate();

        if (isset($data['project_name']))       $settings->setProjectName($data['project_name']);
        if (isset($data['default_language']))   $settings->setDefaultLanguage($data['default_language']);
        if (isset($data['default_appearance'])) $settings->setDefaultAppearance($data['default_appearance']);
        if (array_key_exists('project_url',     $data)) $settings->setProjectUrl($data['project_url']);
        if (array_key_exists('project_logo',    $data)) $settings->setProjectLogo($data['project_logo']);
        if (array_key_exists('project_color',   $data)) $settings->setProjectColor($data['project_color']);

        $this->settingsRepository->save($settings);

        return $this->json(['data' => $settings->toArray()]);
    }
}
