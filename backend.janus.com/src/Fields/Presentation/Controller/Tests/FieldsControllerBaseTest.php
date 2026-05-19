<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;

#[CoversClass(className: FieldsController::class)]
final class FieldsControllerBaseTest extends FieldsControllerTest
{
    public function testControllerInstantiates(): void
    {
        $this->assertInstanceOf(FieldsController::class, $this->class);
    }

    public function testControllerHasListMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('list'));
    }

    public function testControllerHasListByCollectionMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('listByCollection'));
    }

    public function testControllerHasGetMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('get'));
    }

    public function testControllerHasCreateMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('create'));
    }

    public function testControllerHasPatchMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('patch'));
    }

    public function testControllerHasDeleteMethod(): void
    {
        $ref = new ReflectionClass(FieldsController::class);

        $this->assertTrue($ref->hasMethod('delete'));
    }
}
