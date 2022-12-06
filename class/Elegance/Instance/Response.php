<?php

namespace Elegance\Instance;

use Elegance\File;

class Response
{
    protected array $header = [];

    protected ?int $status;
    protected ?string $type;
    protected mixed $content;

    protected null|int|bool $cache = 0;

    protected bool $download = false;
    protected ?string $downloadName = null;

    function __construct(mixed $content = null, ?int $status = null,  array $header = [])
    {
        $this->status($status);
        $this->header($header);
        $this->type(null);
        $this->content($content);
    }

    function __toString()
    {
        return $this->getContent();
    }

    /** Define o status HTTP da resposta */
    function status(?int $status): static
    {
        $this->status = $status;
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
        $type = $type ?? 'html';

        $type = trim($type, '.');
        $type = strtolower($type);
        $type = EX_MIMETYPE[$type] ?? $type;

        $this->type = $type;

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

    /** Envia a resposta finalizando a aplicação */
    function send(?int $status = null): never
    {
        if (is_class($this->content, Response::class))
            $this->content->send($status);

        $content = $this->getContent();
        $headers = $this->getHeders();

        http_response_code($status ?? $this->status ?? STS_OK);

        foreach ($headers as $name => $value)
            header("$name: $value");

        die($content);
    }

    /** Retorna conteúdo da resposta */
    protected function getContent(): string
    {
        return match (true) {
            is_class($this->content, Response::class) => $this->content->getContent(),
            is_array($this->content) => json_encode($this->content),
            default => strval($this->content)
        };
    }

    /** Retorna cabeçalhos de resposta */
    protected function getHeders(): array
    {
        return [
            ...$this->header,
            ...$this->getHeader_Cache(),
            ...$this->getHeader_type(),
            ...$this->getHeader_Download()
        ];
    }

    /** Retorna cabeçalhos de cache */
    protected function getHeader_Cache(): array
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
    protected function getHeader_type(): array
    {
        return ['Content-Type' => $this->type ?? EX_MIMETYPE['html']];
    }

    /** Retorna cabeçalhos de download */
    protected function getHeader_Download(): array
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