### Helper URL

Cria URLs baseando-se nos parametros fornecidos

    url(...$params);

---

### Primeiro parametro 
A helper retorna URLs diferentes dependendo do primeiro

**Sem parametros**
Retorna a URL atual removendo a query GET

    url();
    url('');
    url(null);

**Fornecendo uma URL**
Retorna a URL fornecida

    url('https://mundoizi.com');

**Fornecendo TRUE**
Retorna a URL atual mantendo a query GET

    url(true);
    url('TRUE');

**Fornecendo FALSE**
Retorna a URL atual removendo PATH, parametros, query GET

    url(false);
    url('FALSE');

**Fornecendo parametro numerico negativo**
Retorna a URL removendo os x ultimos itens dos PATH

    url(-1);
    url('-1');

**Fornecendo parametro numerico positivo**
Retorna a URL mantendo os x primeiros itens dos PATH

    url(1);
    url('1');

**Fornecendo parametro numerico 0 (zero)**
Retorna a URL mantendo todos os itens PATH

    url(0);
    url('0');

**Fornecendo parametro iniciado em . (ponto)**
Retorna a URL mantendo todos os itens PATH

    url('.');

---
### Demais parametros

Os demais parametros servem para adicionar caminhos PATH ou QUERY

**string**
Adiciona o item como path

**array**
Adiciona o item como query