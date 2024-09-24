<?php

namespace App\Extensions;

use Dedoc\Scramble\Support\RouteInfo;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Types\StringType;


class AddCustomHeader extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        $operation->addParameters([
            Parameter::make('x-api-key', 'header')
                ->setSchema(
                    Schema::fromType(new StringType())
                )
                ->required(true)
                ->description('API Key for authentication')
                // ->example("925fdfe2-745c-45d4-aaaf-40f3455c0510")
        ]);
    }
}