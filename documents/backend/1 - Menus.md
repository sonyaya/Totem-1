Sumário                                                                                                                                    <a name="summary"></a>
=======

1. [Introdução](#intro)
2. [Entendendo os arquivos de menu](#menu)

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

> É a etiqueta do link que será apresentada para o usuário.

#### link

> É a URL de acesso do itém atual do menu, normalmente algo do tipo **?action=**ACAO**&path=**CAMINHO-DO-FORMULARIO
> onde o valor para *path* deve ser o caminho do formulário a ser apresentado (considere 
> que o raiz sempre será a pastas *modules*), e o valor para *action* são as possíveis 
> ações que podem ser executadas pelo sistema para este formulário, segue uma lista
> de todas as ações existentes:
>
> ##### view-dashboard
>
> > teste
>
> ##### view-insert-form
> ##### view-update-form
> ##### view-dummy-form
> ##### view-list-form
> ##### view-inTabs-form
> ##### view-insert-window-form
> ##### view-update-window-form
> ##### view-dummy-window-form
> ##### view-list-window-form
> ##### view-change-password
> ##### delete-form
> ##### save-form
> ##### type-ajax
> ##### login
> ##### logout
> ##### recover-password
> ##### change-password

#### submenu

> Deve conter um sub array com os submenus, com as propriedades descritas acima 
> inclusive é possível utilizar a propriedade submenu para adiciona ainda mais níveis.
> ao menu

Segue um exemplo comentado de como pode ser criado um menu utilizando menus simples
e menus de módulos, considerem que nosso sistema esta instalado em *root/system/backend*,
notem que no topo de cada exemplo eu irei informar onde o arquivo YAML esta armazenado:

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