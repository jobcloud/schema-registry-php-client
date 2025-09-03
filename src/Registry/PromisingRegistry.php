<?php

declare(strict_types=1);

namespace Jobcloud\SchemaRegistryApi\Registry;

use AvroSchema;
use Closure;
use Jobcloud\SchemaRegistryApi\AsynchronousRegistry;
use Jobcloud\SchemaRegistryApi\Exception\ExceptionMap;
use Jobcloud\SchemaRegistryApi\Schema\AvroReference;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use const Jobcloud\SchemaRegistryApi\Constants\VERSION_LATEST;
use function Jobcloud\SchemaRegistryApi\Requests\checkIfSubjectHasSchemaRegisteredRequest;
use function Jobcloud\SchemaRegistryApi\Requests\registerNewSchemaVersionWithSubjectRequest;
use function Jobcloud\SchemaRegistryApi\Requests\schemaRequest;
use function Jobcloud\SchemaRegistryApi\Requests\singleSubjectVersionRequest;
use function Jobcloud\SchemaRegistryApi\Requests\validateSchemaId;
use function Jobcloud\SchemaRegistryApi\Requests\validateVersionId;
use function sprintf;

/**
 * {@inheritdoc}
 */
class PromisingRegistry implements AsynchronousRegistry
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Closure
     */
    private $rejectedCallback;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $exceptionMap = ExceptionMap::instance();

        $this->rejectedCallback = static function (GuzzleException $exception) use ($exceptionMap) {
            return $exceptionMap($exception);
        };
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function register(string $subject, AvroSchema $schema, AvroReference ...$references): PromiseInterface
    {
        $request = registerNewSchemaVersionWithSubjectRequest((string) $schema, $subject, ...$references);

        $onFulfilled = function (ResponseInterface $response) {
            return $this->getJsonFromResponseBody($response)['id'];
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function schemaId(string $subject, AvroSchema $schema): PromiseInterface
    {
        $request = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $onFulfilled = function (ResponseInterface $response) {
            return $this->getJsonFromResponseBody($response)['id'];
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function schemaForId(int $schemaId): PromiseInterface
    {
        $request = schemaRequest(validateSchemaId($schemaId));

        $onFulfilled = function (ResponseInterface $response) {
            return AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function schemaForSubjectAndVersion(string $subject, int $version): PromiseInterface
    {
        $request = singleSubjectVersionRequest($subject, validateVersionId($version));

        $onFulfilled = function (ResponseInterface $response) {
            return AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function schemaVersion(string $subject, AvroSchema $schema): PromiseInterface
    {
        $request = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $onFulfilled = function (ResponseInterface $response) {
            return $this->getJsonFromResponseBody($response)['version'];
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function latestVersion(string $subject): PromiseInterface
    {
        $request = singleSubjectVersionRequest($subject, VERSION_LATEST);

        $onFulfilled = function (ResponseInterface $response) {
            return AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );
        };

        return $this->makeRequest($request, $onFulfilled);
    }

    /**
     * @param RequestInterface $request
     * @param callable         $onFulfilled
     *
     * @return PromiseInterface
     */
    private function makeRequest(RequestInterface $request, callable $onFulfilled): PromiseInterface
    {
        return $this->client
            ->sendAsync($request)
            ->then($onFulfilled, $this->rejectedCallback);
    }

    /**
     * @param ResponseInterface $response
     * @return array<mixed, mixed>
     */
    private function getJsonFromResponseBody(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        try {
            $decoded = \GuzzleHttp\json_decode($body, true);

            if (!is_array($decoded)) {
                throw new InvalidArgumentException(
                    sprintf('response content "%s" is not a valid json object', $body)
                );
            }

            return $decoded;
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                sprintf('%s - with content "%s"', $e->getMessage(), $body),
                $e->getCode(),
                $e
            );
        }
    }
}
