### Helper config

**IN_DEV**: Se o projeto está rodando em modo desenvolvedor

    IN_DEV=false

**SERVER_PORT**: Porta pardão para servidor embutido

    SERVER_PORT=8333

**FORCE_SSL**: Força a requisição a se comportar com um status especifico de SSL

    FORCE_SSL=false

**RESPONSE_CACHE**: Tempo de cache para arquivos em horas

    RESPONSE_CACHE=null //Não altera o comportamento de cache
    RESPONSE_CACHE=true //Não altera o comportamento de cache
    
    RESPONSE_CACHE=false //Bloqueia cache
    RESPONSE_CACHE=0 //Bloqueia cache

    RESPONSE_CACHE=24 //Utiliza um cache de 24 horas

**RESPONSE_CACHE_EXEMPLE**: Tempo de cache para arquivos de uma extensão [.exemple] em horas

    RESPONSE_CACHE_JPG=672
    RESPONSE_CACHE_ICO=672
    RESPONSE_CACHE_ZIP=24
    RESPONSE_CACHE_PDF=12
    RESPONSE_CACHE_...

**PATH_VIEW**: Diretório onde se encontra as views

    PATH_VIEW=view

**VIEW_MINIFY**: Se o conteúdo das VIEWs deve ser minificado

    VIEW_MINIFY=true

**JWT_PASS**: Chave para utilização de JWT

    JWT_PASS=lpnwtvywyimwoswtmooxqwywpxnxrwoiig
    