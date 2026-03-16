#!/bin/bash
# Generates stub controllers for all remaining Janus backend modules.
# Each stub provides list/get/create/patch/delete endpoints following the same pattern.
set -e

BASE="src"

declare -A MODULES=(
  ["Collections"]="collections"
  ["Fields"]="fields"
  ["Items"]="items"
  ["Relations"]="relations"
  ["Files"]="files"
  ["Roles"]="roles"
  ["Permissions"]="permissions"
  ["Presets"]="presets"
  ["Notifications"]="notifications"
  ["Shares"]="shares"
  ["Dashboards"]="dashboards"
  ["Panels"]="panels"
  ["Flows"]="flows"
  ["Translations"]="translations"
  ["Schema"]="schema"
  ["Versions"]="versions"
  ["Deployments"]="deployments"
  ["Utils"]="utils"
  ["Comments"]="comments"
  ["Revisions"]="revisions"
)

for MODULE in "${!MODULES[@]}"; do
  ROUTE="${MODULES[$MODULE]}"
  DIR="${BASE}/${MODULE}/Presentation/Controller"
  FILE="${DIR}/${MODULE}Controller.php"

  mkdir -p "$DIR"

  if [ -f "$FILE" ]; then
    echo "Skipping existing: $FILE"
    continue
  fi

  cat > "$FILE" << PHPEOF
<?php

declare(strict_types=1);

namespace App\\${MODULE}\\Presentation\\Controller;

use Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController;
use Symfony\\Component\\HttpFoundation\\JsonResponse;
use Symfony\\Component\\HttpFoundation\\Request;
use Symfony\\Component\\HttpFoundation\\Response;
use Symfony\\Component\\Routing\\Attribute\\Route;

#[Route('/${ROUTE}', name: '${ROUTE}_')]
final class ${MODULE}Controller extends AbstractController
{
    /** GET /${ROUTE} */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request \$request): JsonResponse
    {
        return \$this->json(['data' => [], 'meta' => ['total_count' => 0]]);
    }

    /** GET /${ROUTE}/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string \$id): JsonResponse
    {
        return \$this->json(['data' => null], Response::HTTP_NOT_FOUND);
    }

    /** POST /${ROUTE} */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request \$request): JsonResponse
    {
        return \$this->json(['data' => []], Response::HTTP_CREATED);
    }

    /** PATCH /${ROUTE}/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string \$id, Request \$request): JsonResponse
    {
        return \$this->json(['data' => []]);
    }

    /** DELETE /${ROUTE}/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string \$id): Response
    {
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
PHPEOF
  echo "Created: $FILE"
done

echo ""
echo "All stub controllers generated."
