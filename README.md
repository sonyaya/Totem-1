<a name="summary" id="summary"></a>
Sumário
=======

1. [Totem](#intro)
    - [Config](documents/default/config.md)
    - Melhoria contínua
        - [To-do](documents/default/todo.md)
        - [Bugs](documents/default/bugs.md)
    - Equipe
        - Daniel de Andrade Varela
2. Backend
    - [Menus](documents/backend/menus.md)
    - [Formulários](documents/backend/forms.md)
    - [Tipos (types)](documents/backend/types.md)
3. Frontend
4. Bridge
5. Console


Totem
=====

Totem é uma framework desenvolvida em php para criação de sites e sistemas, com o intuito de separar de maneira consisa a responsabilidade do desenvolvedor frontend e backend, separando os processos em:

### Documentação geral
- [Config](documents/config.md)

Backend
-------

Módulo capaz de criar relatórios, formulários de inserção e edição no banco de dados, listagem de dados, exclusão de dados, além de formulários que não possuem relação com o banco de dados, tudo  desenvolvido baseando-se em arquivos YML o que torna muito simples a criação e customização conforme as necessidades do projeto.

### Documentação Backend

- [Menus](documents/backend/menus.md)
- [Formulários](documents/backend/forms.md)
- [Tipos (types)](documents/backend/types.md)

Frontend
--------

Módulo desenvolvido com a finalidade de facilitar o desenvolvimento de interfaces para o usuário final de sistemas desenvolvidos basea-dos no totem, esse módulo é capaz de absorver todas as informações estocadas e processadas pelo módulo backend de forama direta ou por requisições ao módulo bridge.

### Documentação Frontend

- ...

Bridge
------

Modulo capaz de criar uma Rest API automaticamente ou de forma manual desenvolvido diretamente com os recursos deste módulo, isso significa que ao criar um formulário no módulo backend é possivel acessa-lo via Rest API diretamente no módulo bridge, porém caso necessite a criação de alguma funcionalidade especifica cujo a qual não existe formulário no módulo vackend é possivel desenvolver diretamente no módulo bridge.


### Documentação Bridge (Rest API)

- ...

Console
-------

Em breve...

### Documentação Console

- ...
