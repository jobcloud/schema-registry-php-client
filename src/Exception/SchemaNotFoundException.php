<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class SchemaNotFoundException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 40403;
}
