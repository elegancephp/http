### Request

Gerencia a requisição atual

    use Elegance/Request

---

**method**: Retorna o metodo interpretado da requisição atual

    Request::method(): string

---

**header**: Retorna um ou todos os cabeçalhos da requisição atual

    Request::header(): mixed

---

**ssl**: Verifica se a requisição atual utiliza HTTPS

    Request::ssl(): bool

---

**host**: Retorna o host usado na requisição atual

    Request::host(): string

---

**path**: Retorna um ou todos os caminhos da URI da requisição atual

    Request::path(): mixed

---

**query**: Retorna um ou todos os dados passados na QUERY GET da requisição atual

    Request::query(): mixed

---

**data**: Retorna um ou todos os dados enviados no corpo da requisição atual

    Request::data(): mixed

---

**file**: Retorna um o todos os arquivos enviados na requisição atual

    Request::file(): array

---

**setHeader**: Define/Altera um cabeçalho da requisição atual
    
    setHeader(string $name, mixed $value): void
    
---

**setQuery**: Define/Altera um dos dados passados via QUERY GET na requisiçaõ atual
    
    setQuery(string $name, mixed $value): void
    
---

**setData**: Define/Altera um  dos dados enviados no corpo da requisição atual
    
    setData(string $name, mixed $value): void
    