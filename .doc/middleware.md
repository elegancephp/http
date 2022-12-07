# Middleware

Ações executadas antes da resposta da requisição

    use Elegance/Middleware

### Estrutura

As middlewares são funções que recebem um valor, realizam uma ação e chamam a proxima. 
O template basico de uma middleware é o seguinte

    function ($next){
        ...action
        return $next();
    }

### Adicionando Middlewares

    php mx create.middleware [nomeDaMiddleware]

Isso vai criair um arquivo dentro do namespace **Middleware** com o nome fornecido

### Registrando Middlewares
É possivel registrar uma middleware previamente para facilitar a chamada

    Middleware::register('customName',function($next){...});

Pode-se registrar um grupo de middlewares

    Middleware::register('groupName',['middlewares',...]);

### Executando Middlewares
Para executar middlewares, utilize o metodo estatiico **run**

    Middleware::run(['midlewares'...],$action);
