<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class IncompatibleAvroSchemaException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 409;
}
