### Helper funcion

**url**: Retorna uma [URL](https://github.com/elegancephp/http/tree/main/.doc/url.md)

    url(): String

---

**redirect**: Redireciona o backend para uma url

    redirect(...): never

**redirectResponse**: Retorna um objeto de [resposta](https://github.com/elegancephp/http/tree/main/.doc/response.md) com um redirecionamento

    redirectResponse(...): Response

---

**minify_html**: Minifica uma string HTML

    minify_html($input)
    
**minify_css**: Minifica uma string CSS

    minify_css($input)
    
**minify_js**: Minifica uma string Javascript

    minify_js($input)

---

**view**: Retorna a string do conteúdo de uma [view](https://github.com/elegancephp/http/tree/main/.doc/view.md)

    view(string $viewRef, array|string $prepare = []): String

**viewIn**: Retorna a string do conteúdo de uma view dentro da [view](https://github.com/elegancephp/http/tree/main/.doc/view.md) original

    viewIn(string $viewRef, array|string $prepare = []): String