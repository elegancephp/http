### Assets
Cria respostas para arquivos fisicos

    use \Elegance\Assets;


**send**: Envia um arquivo assets como resposta da requisição

    Assets::send($path, $allowTypes): never

---

**download**: Realiza o download de um arquivo assets como resposta da requisiçã

    Assets::download($path, $allowTypes): never

---

**get**: Retorna o arquivo de [resposta](https://github.com/elegancephp/server/tree/main/.doc/response.md) de um arquivo assets

    Assets::get(string $path, array $allowTypes = []): Response
