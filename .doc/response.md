### Response

Cria respostas para a requisição atual

    $resp = new \Elegance\Instance\Response;

**status**: Define o status HTTP da resposta
    
    $resp->status(?int $status): self

**header**: Define variaveis do cabeçalho da resposta
    
    $resp->header(string|array $name, ?string $value = null): self

**type**: Define o contentType da resposta
    
    $resp->type(?string $type): self

**content**: Define o conteúdo da resposta
    
    $resp->content(mixed $content): self

**cache**: Define se o arquivo deve ser armazenado em cache

    $resp->cache(null|bool|int $time): self

**download**: Define se o navegador deve fazer download da resposta
    
    $resp->download(bool|string $download): self

**send**: Envia a resposta finalizando a aplicação
    
    $resp->send(?int $status = null): never
    
**getStatus**: Retorna o status HTTP da resposta
    
    $resp->getStatus(?int $status): static

**getHeader**: Retorna um ou todos os cabeçalhos da resposta
    
    $resp->getHeader(?string $name = null): array

**getType**: Retorna o contentType da resposta
    
    $resp->getType(): ?string

**getContent**: Retorna o conteúdo da resposta
    
    $resp->getContent(): mixed
    
> Caso o conteúdo da resposta for um objeto que contenha o metodo **send**, o conteúdo enviado será a resposta do metodo **send** do objeto.