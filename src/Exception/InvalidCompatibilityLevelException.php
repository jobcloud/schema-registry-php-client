<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class InvalidCompatibilityLevelException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 42203;
}
