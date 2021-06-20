<?php

namespace App\Utils;

final class RequestParams
{
    /** @var array<string, mixed> */
    private array $query;

    /** @var array<string, mixed> */
    private array $headers;

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $headers
     */
    private function __construct(array $query = [], array $headers = [])
    {
        $this->query = $query;
        $this->headers = $headers;
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $headers
     *
     * @return RequestParams
     */
    public static function create(array $query = [], array $headers = []): self
    {
        return new self($query, $headers);
    }

    /**
     * @return array<string, mixed>|array[]
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return array<string, mixed>|array[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, array<string, mixed>>|array[]
     */
    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'headers' => $this->headers,
        ];
    }
}
