<?php

namespace Elegance\Instance;

use Elegance\File;

class Response
{
    protected array $header = [];

    protected ?int $status = null;
    protected ?string $type = null;
    protected mixed $content = null;

    protected null|int|bool $cache = 0;

    protected bool $download = false;
    protected ?string $downloadName = null;

    function __construct(mixed $content = null, ?int $status = null,  array $header = [])
    {
        if (is_class($content, static::class)) {
            $this->header = $content->header;
            $this->status = $content->status;
            $this->type = $content->type;
            $this->content = $content->content;
            $this->cache = $content->cache;
            $this->download = $content->download;
            $this->downloadName = $content->downloadName;
        } else {
            $this->content($content);
        }
        $this->status($status);
        $this->header($header);
        $this->type(null);
    }

    function __toString()
    {
        return $this->getMontedContent();
    }

    /** Define o status HTTP da resposta */
    function status(?int $status): static
    {
        $this->status = $status ?? $this->status;
        return $this;
    }

    /** Define variaveis do cabeçalho da resposta */
    function header(string|array $name, ?string $value = null): static
    {
        if (is_array($name)) {
            foreach ($name as $n => $v)
                $this->header($n, $v);
        } else {
            $this->header[$name] = $value;
        }

        return $this;
    }

    /** Define o contentType da resposta */
    function type(?string $type): static
    {
        if ($type) {
            $type = trim($type, '.');
            $type = strtolower($type);
            $type = EX_MIMETYPE[$type] ?? $type;

            if (!substr_count(strtolower($type), 'charset='))
                $type = "$type; charset=utf-8";

            $this->type = $type;
        }
        return $this;
    }

    /** Define o conteúdo da resposta */
    function content(mixed $content): static
    {
        $this->content = $content;
        return $this;
    }

    /** Define se o arquivo deve ser armazenado em cache */
    function cache(null|bool|int $time): static
    {
        $this->cache = $time;
        return $this;
    }

    /** Define se o navegador deve fazer download da resposta */
    function download(null|bool|string $download): static
    {
        if (is_string($download)) {
            $this->downloadName = $download;
            $download = true;
        }

        $this->download = boolval($download);

        return $this;
    }

    /** Retorna o status HTTP da resposta */
    function getStatus(?int $status): static
    {
        $this->status = $status;
        return $this;
    }

    /** Retorna um ou todos os cabeçalhos da resposta */
    function getHeader(?string $name = null): array
    {
        if (!is_null($name))
            return $this->header[$name] ?? null;

        return $this->header;
    }

    /** Retorna o contentType da resposta */
    function getType(): ?string
    {
        return $this->type;
    }

    /** Retorna o conteúdo da resposta */
    function getContent(): mixed
    {
        return $this->content;
    }

    /** Envia a resposta finalizando a aplicação */
    function send(?int $status = null): never
    {
        if (is_class($this->content, Response::class))
            $this->content->send($status);

        $content = $this->getMontedContent();
        $headers = $this->getMontedHeders();

        http_response_code($status ?? $this->status ?? STS_OK);

        foreach ($headers as $name => $value)
            header("$name: $value");

        die($content);
    }

    /** Retorna conteúdo da resposta */
    protected function getMontedContent(): string
    {
        return match (true) {
            is_class($this->content, Response::class) => $this->content->getMontedContent(),
            is_array($this->content) => json_encode($this->content),
            default => strval($this->content)
        };
    }

    /** Retorna cabeçalhos de resposta */
    protected function getMontedHeders(): array
    {
        return [
            ...$this->header,
            ...$this->getMontedHeader_Cache(),
            ...$this->getMontedHeader_type(),
            ...$this->getMontedHeader_Download()
        ];
    }

    /** Retorna cabeçalhos de cache */
    protected function getMontedHeader_Cache(): array
    {
        $headerCache = [];

        if (!is_null($this->cache)) {
            if ($this->cache === true) {
                $cacheEx = array_flip(EX_MIMETYPE)[$this->type] ?? null;
                $this->cache = env(strtoupper("RESPONSE_CACHE_$cacheEx")) ?? env("RESPONSE_CACHE");
            }

            if ($this->cache) {
                $this->cache = $this->cache * 60 * 60;
                $headerCache['Pragma'] = 'public';
                $headerCache['Cache-Control'] = 'max-age=' . $this->cache;
                $headerCache['Expires'] = gmdate('D, d M Y H:i:s', time() + $this->cache) . ' GMT';
            } else {
                $headerCache['Pragma'] = 'no-cache';
                $headerCache['Cache-Control'] = 'no-cache, no-store, must-revalidat';
                $headerCache['Expires'] = '0';
            }
        }

        return $headerCache;
    }

    /** Retorna cabeçalhos de tipo de conteúdo */
    protected function getMontedHeader_type(): array
    {
        return ['Content-Type' => $this->type ?? EX_MIMETYPE['html']];
    }

    /** Retorna cabeçalhos de download */
    protected function getMontedHeader_Download(): array
    {
        $headerDownload = [];

        if ($this->download) {
            $ex = array_flip(EX_MIMETYPE)[$this->type] ?? 'download';

            $fileName = $this->downloadName ?? 'download';

            File::ensure_extension($fileName, $ex);

            $headerDownload['Content-Disposition'] = "attachment; filename=$fileName";
        }

        return $headerDownload;
    }
}