Sumário                                                                                                                                    <a name="summary"></a>
=======

1. [Introdução](#intro)
2. [Entendendo os arquivos de menu](#menu)
    - [Arquivos de menu comentados](#menu-file)


1. Menus                                                                                                                                   <a name="intro"></a>
========

[▲](#summary) O módulo *backend* possui uma parametrização de menus essencial para
o seu funcionamento, todo e qualquer módulo adicionado a *backend* precisa ser adicionado
ao menu.

Existem dois tipos básicos de menus, o primeiro que é o *menu simples* simplesmente
pode ser adicionado ao arquivo *menu.yml* na pasta raiz do sistema *backend*, o segundo
um pouco mais complexo é o *menu de módulo*, este apenas possui um referencia no
*menu.yml* da pasta raiz para um outro *menu.yml* contido em uma pasta de módulo.

Veja um exemplo de menu simples:

```yml
- label: Example
  submenu:
    - label: Dashboard
      link: ?action=view-dashboard&path=example/dashboards/dashboard

    - label: InTabs
      link: ?action=view-inTabs-form&path=example/forms/example
````

Veja um exemplo de menu de módulo:

> Arquivo menu.yml da pasta raiz:

```yml
- label: Usuários
  load-from-module: user
  module-start-url: ?action=view-dashboard&path=user/dashboards/dashboard
```

> Arquivo menu.yml da pasta modules/user/:

```yml
- label: "Postagens"
  link: "?action=view-inTabs-form&path=blog/forms/post"

- label: "Categorias"
  link: "?action=view-inTabs-form&path=blog/forms/category"

- label: "Tipos"
  link: "?action=view-inTabs-form&path=blog/forms/type"
```

> É possível customizar o menu de seu backend utilizando as duas forma de menu simultaneamente.


2. Entendendo os arquivos de menu                                                                                                          <a name="menu"></a>
=================================

[▲](#summary) Todos os arquivos de menu seguem o mesmo padrão de desenvolvimento,
porém você deve ter em mente que existem dois tipos de menus, menus simples e menus
de modulos, os menus simples são adicionados diretamente no arquivo principal de
menus contidos na pasta raiz do sistema cujo o nome é *menu.yml*, os menus de módulos
estão são referenciados no arquivo pricipal de menu, porém eles dever estar contidos
na pasta *modules* dentro da respectiva pasta do módulo e deve chamar-se *menu.yml*.

Os menus possuem basicamente quatro propriedades, são elas: label, link, submenu,
load-from-module e module-star-url, segue uma explicação para cada uma destas propriedades:

#### label

É a etiqueta/título do link que será apresentada para o usuário.

#### link

É a URL de acesso do itém atual do menu, normalmente algo do tipo **?action=**ACAO**&path=**CAMINHO-DO-FORMULARIO
onde o valor para *path* deve ser o caminho do formulário a ser apresentado (considere
que o raiz sempre será a pastas *modules*), e o valor para *action* são as possíveis
ações que podem ser executadas pelo sistema para este formulário, segue uma lista
de todas as ações existentes:

| Ações                               | Descrição                                                                                                                 |
| -----------------------------------:|:------------------------------------------------------------------------------------------------------------------------- |
| view-dashboard                      | Apresenta a tela de *dashboard* do formulário.                                                                            |
| view-insert-form                    | Apresenta a tela de *inserção* do formulário.                                                                             |
| view-update-form [\*](#cit-1)       | Apresenta a tela de *atualização* do formulário.                                                                          |
| view-dummy-form                     | Apresenta a tela de *boneco* do formulário.                                                                               |
| view-list-form                      | Apresenta a tela de *listagem de dados* do formulário.                                                                    |
| view-inTabs-form                    | Apresenta a tela contendo o agropamento por abas da tela de *atualização*, *inserção* e *listagem* do formulário.         |                                |
| view-insert-window-form             | Apresenta a tela de *inserção* do formulário em formato de popup.                                                         |
| view-update-window-form [\*](#cit-1)| Apresenta a tela de *atualização* do formulário em formato de popup.                                                      |
| view-dummy-window-form              | Apresenta a tela de *boneco* do formulário em formato de popup.                                                           |
| view-list-window-form               | Apresenta a tela de *listagem* do formulário em formato de popup.                                                         |
| view-change-password                | Apresenta a tela de *troca de senha* do formulário.                                                                       |
| delete-form [\*](#cit-1)            | Remove valore no banco de dados.                                                                                          |
| save-form                           | Recebe os valores do método POST da tela de inserção ou atualização e atualiza ou insere os dados conforme a necessidade. |
| type-ajax [\*\*](#cit-2)            | É utilizado para retornar valores a um determinado *tipo de input* via ajax.                                              |
| login                               |                                                                                                                           |
| logout                              |                                                                                                                           |
| recover-password                    |                                                                                                                           |
| change-password                     |                                                                                                                           |

\* <a name="cit-1">
_É preciso passar o parâmetro *id* pelo método GET para esta ação, para que seja
possível identifica em qual linha do banco esta ação ira  ser executada._</a>

\*\* <a name="cit-2">
_É preciso passar o parâmetro *type* pelo método GET para identificação de qual
**tipo de input** esta requisitando o ajax._</a>

#### submenu

Deve conter um sub array com os submenus, com as propriedades descritas acima inclusive
é possível utilizar a propriedade submenu para adiciona ainda mais níveis.

A quantidade de níveis é infinita e pode ser uma opção do programador, porém se
o template que setiver sendo utilizado não possuir suporte para multiplos níveis
eles não serão apresentados.

#### load-from-module

Este parâmetro é utilizado para referenciar submenus de módulos, ele recebe como
valor o nome de um módulo e caso ele seja utilizado o parâmetro link será ignorado.

#### module-star-url

Assim como o parâmetro link tem como valor uma URL porém, neste caso a URL refere-se
a página inicial do módulo.

2.1 Arquivos de menu comentados                                                                                                            <a name="menu-file"></a>
-------------------------------

> Segue um exemplo comentado de como pode ser criado um menu utilizando menus simples
> e menus de módulos, considerem que nosso sistema esta instalado em *root/system/backend*,
> notem que no topo de cada exemplo eu irei informar onde o arquivo YAML esta armazenado:

**menu.yml***
```yml
#
# ESTE É UM MENU DE MÓDULO
#

- label: Usuários
  load-from-module: user
  module-start-url: ?action=view-dashboard&path=user/dashboards/dashboard

#
# ESTE É UM MENU SIMPLES
#

- label: Example
  submenu:
    - label: Dashboard
      link: ?action=view-dashboard&path=example/dashboards/dashboard

    - label: InTabs
      link: ?action=view-inTabs-form&path=example/forms/example

    - label: Insert Form
      link: ?action=view-insert-form&path=example/forms/example

    - label: Update Form
      link: ?action=view-update-form&path=example/forms/example&id=1

    - label: Dummy Form
      link: ?action=view-dummy-form&path=example/forms/example
```

**modules/user/menu.yml**
```yml
- label: "Dashboard"
  link: "?action=view-dashboard&path=user/dashboards/dashboard"

- label: "Usuários"
  link: "?action=view-inTabs-form&path=user/forms/user"

- label: "Grupos de Usuários"
  link: "?action=view-inTabs-form&path=user/forms/group"

- label: "Mensagens"
  link: "?action=view-inTabs-form&path=user/forms/message"
```