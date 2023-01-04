# Middleware

Ações executadas antes da resposta da requisição

    use Elegance/Middleware

### Estrutura

As middlewares são funções que recebem um valor, realizam uma ação e chamam a proxima. 
O template basico de uma middleware é o seguinte

    function (Closure $next){
        return $next();
    }

Caso a middlewares seja uma classe, deve ser implementado o metodo **__invoke**

    function __invoke(Closure $next){
        return $next();
    }

### Criando middlewares

    php mx middleware [nomeDaMiddleware]

Isso vai criair um arquivo dentro do namespace **Middleware** com o nome fornecido

### Registrando middlewares
É possivel registrar uma middleware previamente para facilitar a chamada

    Middleware::register('customName',function($next){...});

Pode-se registrar um grupo de middlewares

    Middleware::register('groupName',['middlewares',...]);

Pode-se criar atalhos para middlewares

    Middleware::register('name','namespace.middleware.name');

### Manipulando fila de middlewares
Para adicionar uma middleware na fila de execução, utilize o codigo abaixo

    Middleware::add('middlewareName');

Pode-se fornecer um segundo parametro para registrar e adicionar a middleware ao mesmo tempo

    Middleware::add('name',function($next){...});

Para remover uma middleware da fila de execução, utilize o codigo abaixo.

    Middleware::remove('middlewareName');

Para verificar quais as middlewares estão na fila de execução, utilize o codigo abaixo.

    Middleware::queue();

### Executando middlewares
Para executar middlewares, utilize o metodo estatiico **run**

    Middleware::run($action);
