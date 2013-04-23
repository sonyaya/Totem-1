Sumário <a name="summary"></a>
=======

1. [Introdução](#intro)
2. [Cabeçalho do Arquivo](#head)
3. [Tipos de Formulários](#form-types)
    - [Inserção (insert)](#save-form) / [Atualização (update)](#save-form)
    - [Boneco (dummy)](#dummy-form)
    - [Listagem (list)](#list-form)
    - [Exclusão (delete)](#delete-form)
    - [Formulário para Rest API (bridge)](#rest-form)
4. [Eventos de formulários](#events)
    - [beforeLoadData: Antes de carregar valores na interface](#event-beforeLoadData)
    - [afterLoadData: Após carregar valores na interface](#event-afterLoadData)
    - [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
    - [afterInsert: Após de executar *insert* no banco de dados](#event-afterInsert)
    - [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
    - [afterUpdate: Após de executar *update* no banco de dados](#event-afterUpdate)
    - [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
    - [afterDelete: Após de executar *delete* no banco de dados](#event-afterDelete)
5. [Como clonar formulários](#clone-form)
6. [Exemplo de formulário completo](#complete-form)


1. Formulários <a name="intro"></a>
==============

[▲](#summary) Os formulários são configurados por um arquivo YAML preferencialmente 
armazenados na pasta **system/backend/modules/seu modulo/forms/arquivo.yml**, este 
único arquivo deve conter a configuração para os seguintes formulários:

- [Formulário de Inserção](#save-form)
- [Formulário de Atualização](#save-form)
- [Formulário de Boneco (dummy)](#dummy-form)
- [Formulário para telas de listagem (list)](#list-form)
- [Formulário de exclusão (delete)](#delete-form)
- [E formulário para Rest API (bridge)](#rest-form)

São basicamente utilizados para informar ao módulo **backend** quais são as rotinas 
de inserção, atualização, exclusão além de rotinas mais específicas, porém também 
podem prover informações para outros módulos como o **bridge (api)** e consequentemente 
para o **frontend**.

2. Cabeçalho                                                                    <a name="head"></a>
============

[▲](#summary) Este é responsável por informar qual é o título principal dos formulários, 
além de descrever qual é a tabela e chave primária do banco de dados que serão  
utilizados por estes formulários, segue exemplo de cabeçalho do arquivo YAML:

```yaml
header:
    title: Nome principal de seus formulários
    table: nome_da_tabela_no_banco_de_dados
    p-key: nome_da_chave_primaria_da_tabela
```

3. Tipos de Formulários                                                         <a name="form-types"></a>
=======================

[▲](#summary) Existem diversos tipos de formulários cada um com uma funcionalidade 
específica, verifique a listagem com todos os tipos de formulários que o sistema 
disponibiliza:

- [Inserção (insert)](#save-form)
- [Atualização (update)](#save-form)
- [Boneco (dummy)](#dummy-form)
- [Listagem (list)](#list-form)
- [Exclusão (delete)](#delete-form)
- [Formulário para Rest API (bridge)](#rest-form)

3.1 Formulário de Inserção ou Atualização                                       <a name="save-form"></a>
-----------------------------------------

[▲](#form-types) Os dois formulários são criados exatamente iguais, portanto esta 
descrição funciona tanto pra um quanto pra outro diferenciando apenas no fato que 
a chave para referência de configuração são distintas, no caso do formulário de 
inserção o caminho da chave de referência é *forms/insert* e o formulário de 
atualização tem o caminho *forms/update*.

Estes formulários possuem três propriedades, são elas:

- title
    - refere-se ao titulo secundário do formulário, para uma descrição pouco mais aprimorada
- input
    - é responsável de informar quais os tipos e as propriedades dos tipos o formulário ira conter, para intender melhor o que são e como funcionam estes tipos leia [a documentação de tipos](types.md).
- merge-form
    - não é uma propriedade obrigatória, é utilizado quando existe a necessidade de copiar os inputs de um outro formulário, isso é útil quando temos dois formulários que irão utilizar exatamente os mesmo ou grande maiorias das propriedades de outro formulário, veja mais informações em [como clonar formulários](#clone-form).

```yaml
forms:
    insert: # no caso de formulário de atualização, o valor seria update:
        title: TÍTULO-PARA-ESTE-FORMULÁRIO
        input: 
              - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
                label: ETIQUETA-PARA-ESTE-INPUT
                column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
                parameter : 
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
              # podemos ter quantos inputs forem necessários
```


3.2 Boneco (dummy)                                                              <a name="dummy-form"></a>
------------------

[▲](#form-types) É o tipo de formulário utilizado para criação de qualquer processo 
que não possa ser feito com os outros formulários, também é o mais indicado para 
criação de relatórios.

Seus parâmetros são muito semelhantes aos parâmetros dos formulários de inserção 
e atualização além de funcionarem basicamente igual, porém com o diferencial que 
por padrão este formulário não executa diretamente inserção, atualização ou qualquer 
outro processo, ele apenas irá executar o arquivo *PHP* que for indicado pelo parâmetro 
*php:*, veja como este formulário é configurado:
   

```yaml
forms:
    dummy:
        title: TÍTULO-PARA-ESTE-FORMULÁRIO
        php: ARQUIVO-PHP-A-SER-EXECUTADO.php
        input: 
              - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
                label: ETIQUETA-PARA-ESTE-INPUT
                column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
                parameter : 
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
              # podemos ter quantos inputs forem necessários
```

Exemplo de como pode ser criado o arquivo PHP (ARQUIVO-PHP-A-SER-EXECUTADO.php):

```php
<?php
    if( !empty($_POST['nome']) ){
        $html = "<h1>Example</h1><pre>" . print_r($_POST, true) . "</pre>";
        return Array(
            "title" => "Retorno em HTML",
            "mime" => "text/html",
            "file" => base64_encode($html)
        );
    }else{
        return Array(
            "error"     => true,
            "message"   => "Essa é uma mensagem de erro criada pelo programador do formulário dummy."
        );   
    }
```
   
3.3 Formulário de Listagem                                                      <a name="list-form"></a>
-------------------------- 

[▲](#form-types) O formulário de listagem é exatamente igual ao formulário de inserção 
e ao formulário de atualização, exceto pelo fato de possuir uma propriedade extra, 
utilizada especificamente para informar quantos itens serão apresentados em cada 
página listagem, esta propriedade não é obrigatória, porém caso não informada o 
sistema passará a considerar o valor informados no arquivo de configuração 
*config.ini.php* na propriedade backend/rows-per-page.

Veja um exemplo de como este trecho do formulário pode ser:

```yaml
forms:
    insert:
        title: TÍTULO-PARA-ESTE-FORMULÁRIO
        rows-per-page: 200 # este é o único ponto que difere dos formulários de inserção e atualização
        input:
              - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
                label: ETIQUETA-PARA-ESTE-INPUT
                column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
                parameter : 
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
              # podemos ter quantos inputs forem necessários
```
> Vale resaltar que os valores para column dos inputs neste formulário podem ser uma sub-query, exemplo: (SELECT `name` FROM _m_group WHERE id = _m_user.group_id)

3.4 Formulário de Exclusão                                                      <a name="delete-form"></a>
--------------------------

[▲](#form-types) O formulário de exclusão funciona exatamente como os formulários 
de edição e inserção, porém com o diferencial que neste formulário os tipos (inputs) 
não são usados para entrada de dados, eles são utilizados apenas para validar se 
os dados que serão removidos realmente podem ser eliminados ou executar alguma ação 
específica antes ou após a exclusão, por não se tratar de um formulário visual, 
este formulário dispensa a necessidade do parâmetro *title*. 

Veja um exemplo de copo pode ser o trecho deste tipo de formulário no arquivo YAML:

```yaml
forms:
    delete:
        input:
              - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
                label: ETIQUETA-PARA-ESTE-INPUT
                column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
                parameter : 
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
              # podemos ter quantos inputs forem necessários
```

3.5 Formulário Rest API (bridge)                                                <a name="rest-form"></a>
--------------------------------

[▲](#form-types) Este formulário é reponsável por fornecer ao módulo **bridge** 
quais serão os tipos de inputs que serão executados, assim como o formulário de 
exclusão ele não é  um formulário visual, porém a API pode retornar conteúdos paginados 
o que faz com que este formulário possa ter o parâmetro *rows-per-page*.

Veja como pode ser este trecho do YAML:

```yaml
forms:
    bridge:
        rows-per-page: 200 # este é o único ponto que difere do formulário de exclusão
        input:
              - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
                label: ETIQUETA-PARA-ESTE-INPUT
                column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
                parameter : 
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
                    PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
              # podemos ter quantos inputs forem necessários
```

4. Eventos de formulários                                                       <a name="events"></a>
=========================

[▲](#summary) É possível adicionar eventos para qualquer formulário, estes eventos 
podem executar qualquer tipo de ação utilizando os dados do formulário ou não, para 
adicionar tais eventos aos formulários é preciso criar um arquivo de classe PHP 
na mesma pasta e mesmo nome do arquivo YAML, caso tenhamos um arquivo de formulário 
chamado *user.yml* na pasta *modules/user/user.yml* para adicionar eventos a este 
formulário termos que ter um arquivo chamado *user.php* nesta mesma pasta, e por 
sua vez esse arquivo deve conter uma classe PHP chamada *FormEvents* com ao menos 
um dos métodos a seguir:

- [beforeLoadData: Antes de carregar valores na interface](#event-beforeLoadData)
- [afterLoadData: Após carregar valores na interface](#event-afterLoadData)
- [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
- [afterInsert: Após de executar *insert* no banco de dados](#event-afterInsert)
- [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
- [afterUpdate: Após de executar *update* no banco de dados](#event-afterUpdate)
- [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
- [afterDelete: Após de executar *delete* no banco de dados](#event-afterDelete)

As classe possuem parâmetros com nomes padronizados para facilitar a criação de 
eventos, segue a explicação de cada um destes parâmetros:

<a name="$pkey"></a>
### $pkey 

> Contém o array com a chave primária do valor que esta sendo inserido, atualizado, deletado ou listado, com nome do campo e valor.

<a name="$config"></a>
### $config 

> Contém o array com os valores do arquivo de configuração.
    
<a name="$loadedData"></a>
### $loadedData 

> Contém o array dos valores carregados a partir do banco de dados.
	
<a name="$data"></a>
###$data 

> Contém um array com os valores enviados para os métods a partir da interface.

4.1 beforeLoadData: Antes de carregar valores na interface                      <a name="event-beforeLoadData"></a>
----------------------------------------------------------

[▲](#events) Antes mesmo de buscar as informações que serão alteradas o método 
*beforeLoadData* da classe *FormEvents*, este método possui apenas os parâmetros:

- [$pkey](#$pkey)
- [$config](#$config)

O que significa que não é possível saber quais são os valores que serão alterados 
ou mesmo alterá-los antes de apresenta-los em tela para o usuário, porém com o valor 
da chave primária e os valores de configuração do formulário é possível executar 
os mais diversos processos antes mesmo dos valores serem solicitados ao banco de 
dados, veja um exemplo:

```php
<?php
    class FormEvents {
        function beforeLoadData($pkey, $config){
           // seu código aqui
        }
    }
```

4.2 afterLoadData: Após carregar valores na interface                           <a name="event-afterLoadData"></a>
-----------------------------------------------------

[▲](#events) Quando um formulário de atualização é solicitado é feita uma busca 
no banco de dados das informações que serão modificadas, logo após essa requisição 
ser efetuada é executado o método *afterLoadData* da classe *FormEvents*, este método 
possui os seguintes parâmetros:

- [$loadedData](#$loadedData)
- [$pkey](#$pkey)
- [$config](#$config)

Com este evento é possível alerar ou formatar os valores que serão apresentados 
no formulário, verificar se os dados que serão mostrados são válido ou executar 
quaquer outro processo PHP antes de apresentar o valor para o usuário.

veja um exemplo da implementação deste evento:

```php
<?php
    class FormEvents {
        afterLoadData(&$loadedData, $pkey, $config){
           $loadedData['login'] = $loadedData['login'] . " Alterado";
        }
    }
```

4.3 beforeInsert: Antes de executar *insert* no banco de dados                  <a name="event-beforeInsert"></a>
--------------------------------------------------------------

[▲](#events) Antes que o formulário de inserção efetue a perpetuação dos dados 
no banco de dados o evento *beforeInsert* é executado, permitindo assim a possibilidade 
de validar os dados que serão gravados ou mesmo executar algum processo relevante 
a perpetuação dos dados, este formulário possui os seguintes parâmetros:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

Para o exemplo a seguir, imagine que temos um valor que no banco de dados é númerico 
porém na interface este número possui um mascara, logo ao tentar inserir este número 
no banco de dados sera retornado um eerro de incompatibilidade, para reseolver esta 
situação podemos fazer o seguinte:

```php
<?php
    class FormEvents {
        function beforeInsert(&$data, $pkey, $config){
           $data['numero'] = preg_replace("/\D/", "", $data['numero']);
        }
    }
```

4.4 afterInsert: Após de executar *insert* no banco de dados                    <a name="event-afterInsert"></a>
------------------------------------------------------------

[▲](#events) Este evento é executa logo após a inserção de dados no banco de dados 
pelo formulário de inserção, isso significa que em caso de não existir nenhuma situação 
adversa durante a inserção, o evento *afterInsert* será executado, nos permitindo 
executar qualquer processo, este evento possui os seguintes parâmetros:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

Imagine que após a inserção dos dados é preciso que seja criada uma pasta cujo o 
nome será o valor de um dos campos do formulário, veja o exemplo a seguir:

```php
<?php
    class FormEvents {
        function afterInsert($data, $pkey, $config){
           mkdir($data['dir'], 0777);
        }
    }
```

4.5 beforeUpdate: Antes de executar *update* no banco de dados                  <a name="event-beforeUpdate"></a>
--------------------------------------------------------------

[▲](#events) Tem o funcionamento semelhante ao evento [beforeInsert](#event-beforeInsert), 
diferenciando apenas que este evento é exectado antes da atualização de dados feita 
pelo formulário de atualização, este método possui os mesmos parâmetros do 
*beforeInsert*:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

Veja o exemplo do [beforeInsert](#event-beforeInsert) apenas tendo em mente que 
o nome do método é diferente.

4.6 afterUpdate: Após de executar *update* no banco de dados                    <a name="event-afterUpdate"></a>
------------------------------------------------------------

[▲](#events) Tem o funcionamento semelhante ao evento [afterInsert](#event-afterInsert), 
diferenciando apenas que este evento é exectado após a atualização de dados feita 
pelo formulário de atualização, este método possui os mesmos parâmetros do 
*afterInsert*:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

Veja o exemplo do [afterInsert](#event-afterInsert) apenas tendo em mente que o 
nome do método é diferente.

4.7 beforeDelete: Antes de executar *delete* no banco de dados                  <a name="event-beforeDelete"></a>
--------------------------------------------------------------

[▲](#events) Antes do formulário de exclusão remover valores do banco de dados 
é possível executar este evento, permitindo assim que seja executado qualquer processo 
ou mesmo a verificação e validação dos dados antes da exclusão, o evento *beforeDelete* 
possui os seguintes parâmetros:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

No exemplo a seguir temos o seguinte cenário, caso o valor do campo *id* for igual 
a *1* o processo de exclusão será cancelado e será apresentado uma mensagem de erro 
ao usuário:

```php
<?php
    use backend/Log;

    class FormEvents {
        function beforeDelete($data, $pkey, $config){
            if($data['id'] == 1){
                Log::ajaxError("001", "Não é possível excluir id = 1.");
            }
        }
    }
```

4.8 afterDelete: Após de executar *delete* no banco de dados                    <a name="event-afterDelete"></a>
------------------------------------------------------------

[▲](#events) Após o formulário de exclusão remover valores do banco de dados é 
possível executar este evento, permitindo assim que seja executado qualquer processo, 
o evento *afterDelete* possui os seguintes parâmetros:

- [$data](#$data)
- [$pkey](#$pkey)
- [$config](#$config)

Imagine um cenário onde após os dados forem excluidos do banco de dados, necessitamos 
excluir uma pasta no sistema cujo o nome é o valor da chave primária excluida, veja 
o exemplo a seguir:

```php
<?php
    class FormEvents {
        function afterDelete($data, $pkey){
            rmdir( $pkey[ $config['p-key'] ] );
        }
    }
```

5. Como clonar formulários                                                      <a name="clone-form"></a>
==========================

[▲](#summary) Qualquer formulário pode ter os valores do parâmetro *input:* mesclados 
com os valores de outro formulário desde que os formulários estejam no mesmo arquivo 
YAML, esta alternava é muito útil quando todos os *inputs* são exatamente iguais 
ou a grande maioria dos inputs são iguais entre dois ou mais formulários, muitas 
vezes o formulário de inserção, atualização, exclusão, API (bridge) são iguais ou 
ao menos parecidos, nestes casos é possível criar clones de formulários utilizando 
a propriedade *merge-form*. 

Em uma situação específica onde é preciso copiar exatamente os valores do de um 
outro formulário podemos fazer o seguinte:

````yaml
…

forms:
    insert:
        title: inserindo
        inputs:
            - type: meioMask
              label: Nome
              column: name
              parameter:
                size: 20
                
            - type: fk
              label: Grupo
              column: group_id
              parameter:
                column: id
                table: _m_group
                label: name 
    update:
    	title: atualizando
    	merge-form: [ update, insert ]
    	inputs: []
    	
…

```

Note a propriedade *merge-form* no formulário update, esta proriedade recebeu o 
array *[ update, insert ]* que informa que o formulário update tem prioridade ao 
ser mesclado com o formulário insert, o que significa que caso exista algum valor 
no parâmetro inputs do formulário update e este conter o mesmo alias nos parâmetros 
*inputs* do formulário insert, o valor que será considera é o valor do parâmetro 
iputs do formulário update, no exemplo a seguir veremos como isso pode ser feito:


```
…

forms:
    insert:
        title: inserindo
        inputs:
            nome:                 # alias/apelido para o input   
              type: meioMask
              label: Nome
              column: name
              parameter:
                size: 20
             
            gurpo:               # alias/apelido para o input
              type: fk
              label: Grupo
              column: group_id
              parameter:
                column: id
                table: _m_group
                label: name 
    update:
    	title: atualizando
    	merge-form: [ update, insert ]
    	inputs:
            nome:                 # alias/apelido para o input   
              type: mask
              label: Nome (atualizando)
              column: name
              parameter:
                size: 200
    	
…

```

Note que no caso a cima foi adicionado um apelido para cada tipo de input adicionado 
a lista, este apelido é utilizado posteriormente para alterarmos os valores do formulário 
que esta sendo clonado, no nosso caso o update tem prioridade sobre o insert porque 
o valor de *merge-forms* é *[ update, insert ]* o que faz com que o update sobrescreva 
o que foi mesclado do formulário insert, porém caso o valor de merge-forms* fosse 
o contrário *[ insert, update ]*, nada ocorreria pois quem passaria a ter prioridade 
superior seria o insert e consequentemente ele iria sobre escreve os valores contidos 
no parâmetro *inputs* do formulário update.

Também é possível adicionar mais valores ao *inputs*, basta que sejam adicionados 
alias não contidos no *inputs* que serão clonados, veja o exemplo a seguir:

```yaml
…

forms:
    insert:
        title: inserindo
        inputs:
            nome:                 # alias/apelido para o input   
              type: meioMask
              label: Nome
              column: name
              parameter:
                size: 20
             
            gurpo:               # alias/apelido para o input
              type: fk
              label: Grupo
              column: group_id
              parameter:
                column: id
                table: _m_group
                label: name 
    update:
    	title: atualizando
    	merge-form: [ update, insert ]
    	inputs:
            nome2:                 # alias/apelido para o input   
              type: mask
              label: Nome (dois)
              column: name2
              parameter:
                size: 200
    	
…

```
Resumindo as regras para mesclagem são: 

- para o parâmetro *merge-forms* o valor mais a esquerda tem prioridade no processo de mesclagem. 

- *inputs* como o mesmo alias são sobrescritos segundo o critério de prioridade. 

- *inputs* com alias diferentes não são mesclados e sim incluídos.

6. Exemplo de formulário completo <a name="complete-form"></a>
=================================

```yaml
header:
    title: 'Cadastro de Usuários'
    table: _m_user
    p-key: id
forms:
    insert:
        title: 'Inserindo Usuário'
        input:
            - type: fk
              label: Grupo
              column: group_id
              parameter:
                column: id
                table: _m_group
                label: name 
                insert-form  : "user/forms/group" 
              
            - type: meioMask
              label: 'Primeiro Nome'
              column: first_name
              parameter:
                size: 20
              
            - type: meioMask
              label: 'Nome do Meio'
              column: middle_name
              parameter:
                size: 20
              
            - type: meioMask
              label: Sobrenome
              column: last_name
              parameter:
                size: 20
              
            - type: meioMask
              label: Login
              column: login
              parameter:
                size: 20
              
            - type: password
              label: Senha
              column: password
              
            - type: textarea
              label: Permissões
              column: permissions
              
            - type: meioMask
              label: email
              column: Email
              parameter:
                size: 100

    update:
        title: 'Atualizando Usuário'
        merge-form: [ update, insert ]
        input: []

    list:
        title: 'Listagm de Usuários'
        rows-per-page: 200
        input:
            - type: example
              label: Cod.
              column: id

            - type: example
              label: 'Nome'
              column: first_name
              
            - type: example
              label: 'Nome do Meio'
              column: middle_name
              
            - type: example
              label: Sobrenome
              column: last_name
              
            - type: example
              label: Login
              column: login
              
            - type: example
              label: Email
              column: Email
              
            - type: example
              label: Grupo
              column: (SELECT `name` FROM _m_group WHERE id = _m_user.group_id)

    dummy:
        php: user-dummy.php
        merge-form: [ update, list ]
        input: []

    delete:
        merge-form: [ update, list ]
        input: []

    bridge:
        merge-form: [ update, list ]
        input: []
```
