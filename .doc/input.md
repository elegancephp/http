### Input
Formata dados de inserção do sistema

    use \Elegance\Instance\Input;

    $input = new Input($data);

Caso o parametro $data não for informado, o input utiliza o valor de **Request::data()**

Para adicionar campos utilize o metodo **feild**

    $input->field($name, $alias) : InputField

* O paraemtro $name é o nome do campo
* O parametro $alias é o nome que deve ser usado em mensagens de erro

> Este metod retorna uma instancia [InputField](https://github.com/elegancephp/http/tree/main/.doc/inputfield.md)

Para adicioanr varios campos de uma vez, utilize o metodo **feilds**

    $input->fields('nameField1','nameField2',...);

> Os campos adicionados via **fields** não farão uso do **alias**

**Utilização**

**get**: Retorna o valor de um ou mais campos do input

    // Recuperar o valor de um campo 'value'
    $input->get('fieldName'); 

    // Recuperar os valores de varios campos ['value1','value2',...]
    $input->get(['name1','name2',...]);
    ou 
    $input->get('name1','name2',...); 

**data** Retorna os valores dos campos do input em forma de array

    // Recuperar o valor de todos os campos ['name'=>'value']
    $input->data();

    // Recuperar o valor de alguns campos ['name1'=>'value1','name2'=>'value2',...]
    $input->data(['name1','name2',...]);
    ou
    $input->data('name1','name2',...);

**data** Retorna os valores dos campos recebidos do input em forma de array

    // Recuperar o valor de todos os campos recebidos ['name'=>'value']
    $input->data();

    // Recuperar o valor de alguns campos recebidos ['name1'=>'value1','name2'=>'value2',...]
    $input->data(['name1','name2',...]);
    ou
    $input->data('name1','name2',...);

**check**: Vefifica se todos os campos do input passam nas regras de validação

    $input->check($throw=true);

    $input->check(false); // Retorna o valor booleano
    $input->check(true); // Lança InputException em caso de erro

**error**: Recupera o array com as mensagens de de erro

    $feild->error() :?srting
