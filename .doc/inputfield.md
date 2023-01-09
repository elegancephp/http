### InputField
Controla um campo especifico de input

    use \Elegance\Instance\InputField;

    $field = new InputField($name,$value);

**Validação**

Para adicionar regras de validação ao field, utilize o metodo Validate

    $field->validate($rule,$message);

*  O parametros $rule é a regra de validação.
*  O campo Message é a mensagem em casao de falha
 
Pada definir um campo como obrigatório, utilize o metodo validate. Por padrão, todo campo criado é considerado obrigatório

    $field->validate(true);//Campo obrigatório

    $field->validate(false);//Campo opcional

> Caso o campo não for obrigatório, as regras de validação não vão ser aplicadas a menos que o campo seja informado

Quando o valor do campo não é informado, o campo recebe o valor NULL e é considerado como Não recebido. Para considerar o valor NULL como recebido utilize o metodo validate

    $field->validate(null);//O valor NULL é um valor valido para o campo

> Caso o campo aceitar o valor NULL, as regras de validação vão ser aplicadas normalmente.

Pode-se definir filtros padrão do PHP como regras de validação. Cada um conta com a propria mensagem de erro padrão.

    $field->validate(FILTER_VALIDATE_EMAIL);
    $field->validate(FILTER_VALIDATE_URL);
    $field->validate(FILTER_VALIDATE_DOMAIN);

Caso precise verificar se um campo é igual a outro, informe o campo de comparação no metodo validate

    $feild1 = new InputField($name1,$value1);
    $feild2 = new InputField($name2,$value2);

    $field2->validate($field1);

Caso precis definir uma regra de validação personalizda, informe um objeto Closure no metodo validate

    $field->validate(function($v){
        return $v = 'validação';
    });

Multiplas validações podem ser adicionadas ao mesmo campo

    $field->validate(false)
        ->validate(FILTE_VALIDATE_EMAIL)
        ->validate(...);

**Validações automáticas**

**preventTag** Previne tags html no valor do campo (padrão TRUE)

    $field->preventTag(true);

**scapePrepare** Escapa tags de prepare no valor do campo (padrão TRUE)

    $field->scapePrepare(true);

**Sanitização**

Para adicinar uma forma de sanitizão do campo, utilize o metodo sanitaze

    $field->sanitaze($sanitaze);

Pode-se adicionar SANITIZE padrão do PHP

    $field->sanitaze(FILTER_SANITIZE_EMAIL);
    $field->sanitaze(FILTER_SANITIZE_NUMBER_INT);

Pode-se adicionar regras personalizadas de sanitaze

    $field->sanitaze(function($v){
        retrun strtolower($v);
    });

> As regras de sanitiação são aplicas **apos** as regras de validação

**Utilização**

**get**: Recupera o valor do campo, ou lança uma **InputException** em caso de erro

    $field->get();

**check**: Verifica se o campo passa nas regras de validação

    $field->check($throw=true);

    $field->check(false); // Retorna o valor booleano
    $field->check(true); // Lança InputException em caso de erro

**recived**: Verifica se o campo foi recebido

    $field->recived() :bool

**error**: Recupera a mensagem de erro

    $feild->error() :?srting

