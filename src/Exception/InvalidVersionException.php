<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class InvalidVersionException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 42202;
}
