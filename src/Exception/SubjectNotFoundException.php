<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class SubjectNotFoundException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 40401;
}
