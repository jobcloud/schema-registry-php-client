<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Test\Registry\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Jobcloud\SchemaRegistryApi\Registry\Cache\DoctrineCacheAdapter;
use Jobcloud\SchemaRegistryApi\Registry\CacheAdapter;

class DoctrineCacheAdapterTest extends AbstractCacheAdapterTestCase
{
    protected function getAdapter(): CacheAdapter
    {
        return new DoctrineCacheAdapter(new ArrayCache());
    }
}
