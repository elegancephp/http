### Input
Formata dados de inserção do sistema

    use \Elegance\Instance\Input;

    $input = new Input();

### Definindo o input data
Por padrão, a classe utiliza os dados de **Request:body()**
Isso pode ser alterado, informando o parametro **$inputData**


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

O parametro **$rule** define a regra de validação.
Defina como **bool** para maracar o campo como obrigatório ou não.

    $field->validate(true);

Defina como **string** para verificar se o campo é igual a outro campo do input

    $field->validate('senha');

Defina como um **FILTER_VALIDATE** para aplica-lo automaticament

    $field->validate(FILTER_VALIDATE_EMAIL);

Defina como **Clousure** para definir uma validação personalizada

    $field->validate(fn($value)=>$value>=10);

O campo **$message** é a mensagem que deve ser lançada caso a validação não passe. 
Ele aplica automaticamente um prepare com o alias do campo

    $field->validate(true, 'O campo [#] deve ser informado');

---

**sanitaze**: Adiciona regras de senitização ao campo
    
    $field->sanitaze(mixed $sanitaze): self

O sanitaze é aplicado ao valor do campo, caso todas as regras de validações passem
Informe um **FILTER_SANITIZE** do PHP ou uma **Clousure** personalizada

    $filter->sanitaze(FILTER_SANITIZE_EMAIL);
    $filter->sanitaze(fn($value)=>strtolower($value));

**get**: Veririca e retorna o valor do campo
    
    $field->get(): mixed

Caso as regras de validações não passem, será lançado uma Exception 400

### Validação de campos
Para validar um campo especifico, utilize o metodo **get**

    $field->get();

> Caso as regras de validações não passem, será lançado uma Exception 400

> Caso o valor do campo for um array, a validação é aplicada a cada item do array

### Exemplo de inputs

    $input = new Input;

    $email = $input->field('email','Email')
                ->validate(FILTER_VALIDATE_EMAIL)
                ->sanitaze(FILTER_SANITIZE_EMAIL)
                ->get();

    $senha = $input->field('pass','Senha')->get();