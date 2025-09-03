<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Exception;

class VersionNotFoundException extends AbstractSchemaRegistryException
{
    public const ERROR_CODE = 40402;
}
