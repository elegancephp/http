### View

Controla a criação de HTML CSS e JS

    use Elegance/View

---

    View::render(string $view,array|string $prepare): string;

### Estrutura
Uma **view**, é uma pasta que contem os arquivos para montar uma visualiação
Estes arquivos são:

 - **content.php** ou **content.html:** HTML da view
 - **script.js** Javascript da view
 - **style.css** Folha de estilo da view
 - **data.php** ou **data.json:** Dados padrão para o prepare da view

> Todos estes arquivos são opcionais. Devem existir comforme a nescessidade

### Diretório de view
Você pode organizar as views em diretórios. Neste casso, não é preciso informar o nome da view nos arquivos

    view/nomeDaView
     - content.html 
     - data.json
     - script.js
     - style.css

### Funcionamento
Ao chamar uma view é o mesmo que executar um [prepare](https://github.com/elegancephp/core/tree/main/.doc/prepare.md) em seus arquivos.
A classe se encarrega de montar a view da melhor forma possivel 

### Helpers da view
Existe uma forma de adicionar Helper ou resposta a prepare especificos para view. Estas adições estarão disponives em **todas as views** chamada.
Para adicionar uma opção deve-se utilizar o metodo estatico abaixo

    View::prepare(string $name, mixed $response): void

