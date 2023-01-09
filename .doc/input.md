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

**get**: Recupera o valor de todos os campos do input, ou lança uma **InputException** em caso de erro

    $input->get();

**get**: Recupera o valor dos campos recebidos do input, ou lança uma **InputException** em caso de erro

    $input->getRecived();

**check**: Vefifica se todos os campos do input passam nas regras de validação

    $input->check($throw=true);

    $input->check(false); // Retorna o valor booleano
    $input->check(true); // Lança InputException em caso de erro

**error**: Recupera o array com as mensagens de de erro

    $feild->error() :?srting
