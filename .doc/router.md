### Router

Controla rotas do sistema

    use Elegance/Router

## Criando rotas manualmente
A classe conta um metodo para adiconar rotas manualmente

 - **Router::add**: Adiciona uma rota para todas as requisições

    Router::add($template,$response);

> As ordem de declaração das rotas não importa pra a interpretação. A classe vai organizar as rotas da maneira mais segura possivel. 

Para resolver as rotas, utilize o metodo **solve**

    Router::solve();

### Template
O template é a forma como a rota será encontrada na URL.

    Router::add('shop')// Reponde a URL /shop
    Router::add('blog')// Reponde a URL /blog
    Router::add('blog/post')// Reponde a URL /blog/post
    Router::add('')// Reponde a URL em branco

Pode-se definir rotas que so vão responder a um verbo HTTP

    Router::addIn('get','blog')// Responde apenas em requisições GET
    Router::addIn('post','blog')// Responde apenas em requisições POST

Para definir um parametro dinamico no template, utilize **[#]**

    Router::add('blog/[#]')// Reponde a URL /blog/[alguma coisa]
    Router::add('blog/post/[#]')// Reponde a URL /blog/post/[alguma coisa]

Caso a rota deva aceitar mais parametros alem do definido no template, utilize o sufixo **...**

    Router::add('blog...')// Reponde a URL /blog/[qualquer numero de parametros]

Os parametros dinamicos podem ser recuperados utilizando a classe **Router**

    Router::data();

Para nomear os parametros dinamicos, pasta adicionar um nome ao **[#]**

    Router::add('blog/[#postId]')
    Router::add('blog/post/[#imageId]')


Para filtrar o tipo de parametro da URL, utilize os templates **[#]**, **[!]**, **[%]**, **[$]**, **[-]**

    Router::add('[#var]',...) // Qualquer variavel
    Router::add('[!var]',...) // Variaveis do tipo INT
    Router::add('[%var]',...) // Variaveis do tipo INT e FLOAT
    Router::add('[$var]',...) // Variaveis de strings codificadas
    Router::add('[-var]',...) // Variaveis de strings cifradas
    Router::add('[=var]') // O mesmo que a rota fiza var

Os parametros nomeados, tambem podem ser recuperados da mesma forma dos não nomeados. Estes são tambem adicionados diretamente ao **data** da **Router**

    Router::data()['postId'];
    Router::data()['imageId'];
    
    ou
    
    Router::data('postId');
    Router::data('imageId');

### Resposta
A classe está preparada pra receber 7 tipos de repostas diferentes para as rotas

**null**
Trata a rota como não existente (404)

**callable**
Responda a rota com uma função anonima
A respota será o retorno da função anonima

    Router::add('', function (){
        return ...
    });

Pode recuperar um parametro dinamico informando-o como parametro para a função

    Router::add('blog/[!postId]', function ($postId){
        return ...
    });

**string iniciada em (#)**
Retorna uma string de dbug

**string iniciada em (@)**
Resolve a rota como um nome de classe controller

**string iniciada em (>)**
Redireciona para outra URL

**string**
Resolve a rota como um nome de classe

---

### Middlewares
Para adicionar um middleware você deve utilizar o metodod **middleware**

    Router::middleware('route','middleware');

É possivel remover uma middleware de uma rota prefixando-a com o sinal menos (-)

    Router::middleware('route','-middleware');

Pode-se definir que uma middleware não será executada em certas rotas prefixando as rotas com (!)

    Router::middleware('!route2','middleware');

Pode-se definir multiplas condições de rota para uma mesma middleware

    Router::middleware(['blog','!blog/all'],'middleware');

**veja**: [middleware](https://github.com/elegancephp/http/tree/main/.doc/middleware.md)

> Uma rota personalizada para favicon.ico já é implementada. Isso evita um bug em navegadores que chamam este arquivo de forma automática. A rota pode ser subistituída a qualquer momento.

---

## File Route System (FRS)
A classe pode mapear um diretório adicionando rotas e middlewares automaticamente

    Router::map($dir);

Cada arquivo dentro do diretório se transforma em uma rota. Os arquivos devem retornar uma classe com os metodos HTTP que devem responeder

    <?php

        return new class
        { 
            function get()
            {
                //...
            }

            function post()
            {
                //...
            }
        };

Para criar rotas usando o FRS, crie um arquivo dentro do difertório mapeado. Arquivos com o nome _index.php serão chamados como padrão

    dir
        blog.php
        contact.php

    // Equivalente

    Router::blog('blog');
    Router::blog('contact');

Você pode criar subrotas separando o nome do arquivo com o caracter **+**

    dir
        blog.php
        blog+post.php

    // Equivalente

    Router::add('blog');
    Router::add('blog/post');

Para organização, pode-se criar subrotas utilizando diretórios

    dir
        blog
            post.php
        blog.php

    // Equivalente

    Router::add('blog');
    Router::add('blog/post');

Um arquivo **_index.php** será chamado automaticamente como rota principal

    dir
        _index.php

    // Equivalente 

    Router::add('');

Pode adicionar o arquivo **_index.php** dentro de um diretório

    dir
        blog
            _index.php

    // Equivalente 

    dir
        blog.php

    // Equivalente

    Router::add('blog');
 
### Template
par se rotas dinamicas, adicione os templates ao nome dos arquivos

    dir
        [#var].php
        [!var].php
        [%var].php
        [$var].php
        [-var].php
        [=var].php

    // Equivalente 

    Router::add('[#var]') // Qualquer variavel
    Router::add('[!var]') // Variaveis do tipo INT
    Router::add('[%var]') // Variaveis do tipo INT e FLOAT
    Router::add('[$var]') // Variaveis de strings codificadas
    Router::add('[-var]') // Variaveis de strings cifradas
    Router::add('[=var]') // O mesmo que a rota fiza var

O sufixo **...** deve ser usado como um template **[...]**

    dir
        [...].php

    // Equivalente

    Route::add('...');

### Middleware
Para adicionar middlewares, basta adicionar um arquivo **_.php** no diretório

    dir
        _.php

O arquivo deve conter as alterações de middlewares e será chamado quando a rota for ativada. Todos os arquivos **_.php**, que foram compativeis com a rota, serão chamados e acumulados.

Um exemplo de um arquivo de middleware **_.php**

    <?php

        namespace Elegance;

        Middleware::add('md1');
        Middleware::add('md2');
        Middleware::remove('md3');

É possivel adicionar middlewares diretamente em um arquivo de rota antes do retorno da classe

    <?php

        Middleware::add('md1');
        
        return new class
        { 
            ...
        };
