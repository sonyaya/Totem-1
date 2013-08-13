...

Sumário                                                                                                                                    <a name="summary"></a>
=======

1. [Totem](#intro)
2. [Backend](#backend)
3. [Frontend](#frontend)
4. [Bridge](#bridge)
5. [Console](#console)


1. Totem                                                                                                                                   <a name="intro"></a>
========

[▲](#summary) Totem é uma [*framework*](http://pt.wikipedia.org/wiki/Framework)
desenvolvida em PHP para criação de sites e sistemas, com o intuito de separar de
maneira consisa a responsabilidade do desenvolvedor frontend, backend e o gestor
de conteúdo, o *Totem* consiste nos módulos frontend, backend, bridge e console.

O módulo *backend* é onde o programador irá criar todas as regras do sistema e o
gestor de conteúdo  ira abastecer o banco de dados e obter informações sobre o site/sistema.

O módulo *frontend* é específico para criação da aparencia do site com HTML, CSS
e Javascript, ele dispõe de diversas extenções para facilitar a vida do programador.

O módulo *bridge* facilita para os programadores disponibilizando de maneira simples
uma API de maneira simples para terceiros que desejam acessar ou enviar informações
para o sistema.

O módulo *console* é responsável por facilitar a instalação e manutenção do sistema
em geral, como por exemplo a adição de novos módulos.

### Documentação geral

- [Requisitos mínimos](documents/common/1 - System Requirements.md)
- [Config](documents/common/2 - Config.md)
- Melhoria contínua
    - [To-do](documents/common/todo.md)
    - [Bugs](documents/common/bugs.md)
- [Equipe](#documents/common/team.md)


2. Backend                                                                                                                                 <a name="backend"></a>
==========

[▲](#summary) Módulo capaz de criar formulários de inserção, formulários de edição,
relatórios, listagem de dados, exclusão de dados, além de formulários que não necessariamente
possuem relação com o banco de dados (desenvolvidos para execução de algum processo
que o sistema não possua por padrão), tudo baseando em arquivos YAML e em casos mais
específicos PHP, o que torna muito simples a criação e customização do projeto.

### Como acessar o módulo *Backend*

- http://seu-domínio/admin
- http://seu-domínio/administrator
- http://seu-domínio/backend

### Documentação Backend

- [Menus](documents/backend/1 - Menus.md)
- [Formulários](documents/backend/2 - Forms.md)
- [Tipos (types)](documents/backend/3 - Types.md)


3. Frontend                                                                                                                                <a name="frontend"></a>
===========

[▲](#summary) Módulo desenvolvido com a finalidade de facilitar o desenvolvimento
de interfaces além de separar-lo do desenvolvimento de regras de sistema e abstração
de banco de dados, esse módulo é capaz de absorver todas as informações estocadas
e processadas pelo módulo *backend*, de forma direta ou por requisições ao módulo
bridge.

### Como acessar o módulo *frontend*

- http://seu-domínio/
- http://seu-domínio/site
- http://seu-domínio/frontend

> Adicione */sua-pagina* para acessar suas páginas, caso não seja adicionado a pagina
> que será apresenta é a index.

### Documentação Frontend

- ...


4. Bridge (Rest API)                                                                                                                       <a name="bridge"></a>
====================

[▲](#summary) Módulo capaz de criar uma (Rest API)[1] automaticamente ou de forma manual
(desenvolvido diretamente módulo *bridge*), isso significa que ao criar um formulário
no módulo *backend* é possivel acessá-lo via (Rest API)[1] sem a necessidade de programação,
porém caso exista a necessedade de ser desenvolvida alguma funcionalidade específica
cujo a qual não existe no formulário do módulo *backend* ou simplesmete o formulário
não exista no módulo *backend*, é possivel desenvolver manualmente no módulo *bridge*.

[1]: http://pt.wikipedia.org/wiki/REST

### Como acessar o módulo *Bridge*

- http://seu-domínio/api/...
- http://seu-domínio/rest/...
- http://seu-domínio/bridge/...

> Substitua os três pontos por seus comando Rest API, caso feita uma requisição
> vai post estes comando devem ser enviados via post. Leia mais na documentação
> a seguir:

### Documentação Bridge (Rest API)

- ...


5. Console                                                                                                                                 <a name="console"></a>
==========

[▲](#summary) Em breve...

### Documentação do módulo *Console*

- ...
