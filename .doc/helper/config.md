### Helper config

**SERVER_PORT**: Porta pardão para servidor embutido


**FORCE_SSL**: Força a requisição a se comportar com um status especifico de SSL


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

**VIEW_MINIFY**: Se o conteúdo das VIEWs deve ser minificado

**PATH_ROUTE**: Diretório para os arquivos de rota
