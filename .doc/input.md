### Input
Formata dados de inserção do sistema

    use \Elegance\Instance\Input;

    $input = new Input();

### Definindo o input data
Por padrão, a classe utiliza os dados de **Request:body()**
Isso pode ser alterado, informando o parametro **inputData**


    $input = new Input(Request:body());
    
    $input = new Input(Request:data());
    
    $input = new Input([
        'usuario'=>'contatoadmx@gmail.com',
        'senha'=>1234
    ]);

### Criando campos de verificação
Crie um campo de verificação utilizando o metodo **field**

    $field = $input->field($name, $alias = null);

Este metodo retorna uma instancia de Field. Utilize esta intancia para personalizar o campo

---

**validate**: Adiciona uma regra de validação ao campo
    
    $field->validate(mixed $rule, ?string $message = null): self

O parametro **rule** define a regra de validação.
Defina como **bool** para maracar o campo como obrigatório ou não.

    $field->validate(true);

Defina como **string** para verificar se o campo é igual a outro campo do input

    $field->validate('senha');

Defina como um **FILTER_VALIDATE** para aplica-lo automaticament

    $field->validate(FILTER_VALIDATE_EMAIL);

Defina como **Clousure** para definir uma validação personalizada

    $field->validate(fn($value)=>$value>=10);

O campo **message** é a mensagem que deve ser lançada caso a validação não passe. 
Ele aplica automaticamente um prepare com o alias do campo

    $field->validate(true, 'O campo [#] deve ser informado');

---

**sanitaze**: Adiciona regras de senitização ao campo
    
    $field->sanitaze(mixed $sanitaze): self

O sanitaze é aplicado ao valor do campo, caso todas as regras de validações passem
Informe um **FILTER_SANITIZE** do PHP ou uma **Clousure** personalizada

    $field->sanitaze(FILTER_SANITIZE_EMAIL);
    $field->sanitaze(fn($value)=>strtolower($value));

O valor do campo será sanitizado apenas se passar nos teste de validação. Alterar este comportamento, defina sanizate(true)

    $field->sanitaze(true)

### Tratamento individual de campos

**check**
Para validar um campo especifico, utilize o metodo **get**

    $field->check(bool $trow = true);

> Caso as regras de validações não passem, e o metodo **trow** for definido como **true**, será lançado uma Exception 400
> Caso as regras de validação não passem, e o metodo **trow** for definido como **false**, a mensagem de erro será retornada
> Caso o valor do campo for um array, a validação é aplicada individualmente a cada item do array

**get**: Retorna o valor do campo
    
    $field->get(bool $trow = true, bool $sanitaze = true): mixed

O parametro **trow** será aplicado ao **check** do campo. 
O parametros **sanitaze** define se o campo deve ser sanitizado antes de ser retornado.

**Exemplo de inputs com tratamento individual**

    $input = new Input;

    $email = $input->field('email','Email')
                ->validate(FILTER_VALIDATE_EMAIL)
                ->sanitaze(FILTER_SANITIZE_EMAIL)
                ->get();

    $senha = $input->field('pass','Senha')->get();

    dd($email,$senha);

### Tratamento geral de campos
Para tratar os campos de forma geral, itulize o objeto $input

**check**
Verifica se todos os campos do input passam nos testes de validação

    $input->check(bool $trow = true): array|bool

> Caso as regras de validações não passem, e o metodo **trow** for definido como **true**, será lançado uma Exception 400 com todos os erros
> Caso as regras de validação não passem, e o metodo **trow** for definido como **false**, será retornado um array com todos os errros
> Caso as regras passem, será retornado **true**

**data**
Retorna um ou todos os valores do input

    $input->data(bool|string|array $name = false);

> Se o parametro **name** for fornecido será retornado o valor de um campo especifico
> Se o valor do **name** for **true** será retornado os valores de todos os campos
> Se o valor do **name** for **false** será retornado apeanas os valores do campos recebidos

Pode-se retornar apenas alguns valores do input, fornecendo um array no parametro name

    $intut->data(['name', 'email']);

**Exemplo de input geral**

    $input = new Input;

    $input->field('email','Email')
                ->validate(FILTER_VALIDATE_EMAIL)
                ->sanitaze(FILTER_SANITIZE_EMAIL);

    $senha = $input->field('pass','Senha');

    $input->check();

    dd($input->data('email'), $input->data('senha'));