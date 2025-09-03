<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Test\Registry\Cache;

use Jobcloud\SchemaRegistryApi\Registry\Cache\CacheItemPoolAdapter;
use Jobcloud\SchemaRegistryApi\Registry\CacheAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CacheItemPoolAdapterTest extends AbstractCacheAdapterTestCase
{
    protected function getAdapter(): CacheAdapter
    {
        return new CacheItemPoolAdapter(new ArrayAdapter());
    }
}
