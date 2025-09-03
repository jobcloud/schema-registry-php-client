<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Test\Registry\Cache;

use Jobcloud\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use Jobcloud\SchemaRegistryApi\Registry\CacheAdapter;

class AvroObjectCacheAdapterTest extends AbstractCacheAdapterTestCase
{
    protected function getAdapter(): CacheAdapter
    {
        return new AvroObjectCacheAdapter();
    }
}
