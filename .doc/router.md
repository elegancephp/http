### Router

Controla rotas do sistema

    use Elegance/Router

### Criando rotas 
A classe conta um metodo para adiconar rotas manualmente

 - **Router::add**: Adiciona uma rota para todas as requisições

    Router::add($template,$response);

> As ordem de declaração das rotas não importa pra a interpretação. A classe vai organizar as rotas da maneira mais segura possivel. 

Para resolver as rotas, utilize o metodo **solve**

    Router::solve();

### Import
Pode-se importar arquivos de rota para facilitar a organização

    Router::import(['file1', 'file2', ...], $path)

Os arquivos serão importados de dentro do diretório $path
Caso o diretório $path não seja especificado, sera utilizado a variavel de ambiente PATH_ROUTE

### Template
O template é a forma como a rota será encontrada na URL.

    Router::add('shop')// Reponde a URL /shop
    Router::add('blog')// Reponde a URL /blog
    Router::add('blog/post')// Reponde a URL /blog/post
    Router::add('')// Reponde a URL em branco

Para fazer com que rotar respondam apenas a um tipo de requisição HTTP, utilize um prefixo

    Router::add('post:route')// Response as requisições do tipo POST
    Router::add('get:route')// Response as requisições do tipo GET
    Router::add('route')// Response requisições não especificadas

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

    Router::get('', function (){
        return ...
    });

Pode recuperar um parametro dinamico informando-o como parametro para a função

    Router::get('blog/[#postId]', function ($postId){
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

Pode-se adicionar varias middlewares de uma vez enviando um array para a action

    Router::middleware('route',['middleware1','middleware2'...]);

Pode-se adicionar varias rotas em uma unica declaração

    Router::middleware(['route1','route2'],['middlewares'...]);

É possivel remover uma middleware de uma rota prefixando-a com o sinal menos (-)

    Router::middleware('route','-middleware');

Pode-se definir que uma middleware não será executada em certas rotas prefixando as rotas com (!)

    Router::middleware(['route','!route2'],'middleware');

**veja**: [middleware](https://github.com/elegancephp/http/tree/main/.doc/middleware.md)

> Uma rota personalizada para favicon.ico já é implementada. Isso evita um bug em navegadores que chamam este arquivo de forma automática. A rota pode ser subistituída a qualquer momento.