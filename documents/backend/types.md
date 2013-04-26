Sumário                                                                                                                                    <a name="summary"></a>
=======

1. [Introdução](#intro)
2. [Tipos padrões](#default-types)
    - [CKEditor](#type-CKEditor)
    - [combobox](#type-combobox)
    - [dateBr](#type-dateBr)
    - [dateTimeBr](#type-dateTimeBr)
    - [example](#type-example)
    - [fk](#type-fk)
    - [imagesUpload](#type-imagesUpload)
    - [jsonArray](#type-jsonArray)
    - [manyToMany](#type-manyToMany)
    - [meioMask](#type-meioMask)
    - [number](#type-number)
    - [password](#type-password)
    - [rawText](#type-rawText)
    - [textarea](#type-textarea)
3. [Criando um tipo personalizado](#creating)
    - [Arquivo de configuração de tipo (config.yml)](#config)
    - [Arquivo de eventos](#events)
        - [validate: Verifica se o campo não possui nenhum restrição para ser inserido,
           atualizado ou deletado.](#event-validate)
        - [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
        - [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
        - [beforeList: Antes do formulário de listagem mostrar os valores na
           interface](#event-beforeList)
        - [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
        - [beforeLoadDataToForm: Antes de mostrar os valores que serão editados
           no formulário de atualização](#event-beforeLoadDataToForm)
        - [afterInsert: Após executar *insert* no banco de dados](#event-afterInsert)
        - [afterUpdate: Após executar *update* no banco de dados](#event-afterUpdate)
        - [afterDelete: Após executar *delete* no banco de dados](#event-afterDelete)
        - [ajax: Ao executar uma requisição ajax para o tipo](#event-ajax)
    - [Arquivo de interface](#interface)
        - [Como acessar valores do sistema na interface](#interface-access-values)
            - [Como acessar valores do totem](#interface-access-totem-values)
            - [Como acessar valores do tipo](#interface-access-type-values)
4. Configurando os parâmetros de tipos

1. Tipos de Inputs                                                                                                                         <a name="intro"></a>
==================

[▲](#summary) *Tipos*, *types*, *tipos de inputs* ou até mesmo *inputs* são possíveis
nomeclaturas para apresentar a base de qualquer formulário do módulo *backend*,
estes objetos são reponsaveis por criar a aparencia e funcionamento para entrada
de dados no sistema, criar validadores de interface, ajax e server side além de
tratar os valores que são inseridos, editados ou mesmo excluidos do banco de
dados.

É possivel criar os mais diversos *tipos de inputs*, seja uma simples entrada de
texto até mesmo um relacionamento entre duas ou mais tabélas, por padrão o **totem**
possui os seguintes tipos:

2. Tipos padrões                                                                                                                           <a name="default-types"></a>
================

[▲](#summary) O totem possui alguns tipos padrões, estes tipos podem ajudar novos
usuários a personalizar seus formulários conforme sua necessidade, porém em casos
que os tipos padrões não suprem as necessidades de desenvolvimento é possível criar
tipos para ações específicas, leia o tópico [Criando um tipo personalizado](#creating)
e veja como criar seu próprio tipo, a seguir veja a lista de tipos padrões e suas
especificações:


CKEditor                                                                                                                                   <a name="type-CKEditor"></a>
--------

[▲](#default-types) Adiciona um tipo baseado na biblioteca [CKEditor](http://ckeditor.com/).

- Parâmetros
    - …


combobox                                                                                                                                   <a name="type-combobox"></a>
--------

[▲](#default-types) Cria um objeto com a tag select do HTML, ele permite que o
usuário decida quais serão os valores inseridos no banco de dados e as etiquetas
que são apresentadas para o usuário.

- Parâmetros
    - **valor-1 : etiqueta-1**
    - **valor-2 : etiqueta-2**
    - **valor-3 : etiqueta-3**
      - *adicione quantos valores achar necessário*

dateBr                                                                                                                                     <a name="type-dateBr"></a>
------

[▲](#default-types) Cria três objetos do tipo select do HTML, o primeiro com os
anos, o segundo com os meses e o terceiro com os dias. Quando enviado para o banco
o formato é o padrão do MySQL (Y-m-d).

- Parâmetros
    - **nullable** *pode ser nulo?*
    - **year**
        - **start** *ano de inicio da listagem*
        - **stop**  *ano máximo da listagem*


dateTimeBr                                                                                                                                 <a name="type-dateTimeBr"></a>
----------

[▲](#default-types) Parecido com o dateBr com a diferença que o *dateTimeBr* possui
selects do HTML a mais, o primeiro é a hora, o segundo refere-se aos minutos e o
tereiro aos segundos.

- Parâmetros
    - **nullable** *pode ser nulo?*
    - **year**
        - **start** *ano de inicio da listagem*
        - **stop**  *ano máximo da listagem*

example                                                                                                                                    <a name="type-example"></a>
-------

[▲](#default-types) É um tipo utilizado para demonstrar como são feitos os tipos.

fk                                                                                                                                         <a name="type-fk"></a>
--

[▲](#default-types) Parecido com o combobox, porém neste tipo os valores são buscados
de uma tabela no banco de dados, criando assim um relação simples entre as tabelas.

- Parâmetros
    - **table**       *tabela no banco de dados*
    - **column**      *coluna do banco de dados que será utilizada para os valores
                       do select*
    - **label**       *coluna do banco de dados que erá utilizada para as etiquetas
                       do select*
    - **insert-form** *caminho do formulário para inserção de novos valores*

imagesUpload                                                                                                                               <a name="type-imagesUpload"></a>
------------

[▲](#default-types) Permite envio de imagens para o sistema, estas imagens são salvas
em um caminho informado nos parâmetros e em uma sub-pasta com o nome do valor da chave
primária, além de permitir ordenação e adição de informação diversas na imagem, atente-se
que ele insere um jSon no banco de dados.

- Parâmetros
   - **folder** *caminho da pasta que serão gravadas as imagens*
   - **inputs**
      - **label/chave : \<textarea name="data"\>\</textarea\>** *informação adicional
                                                                 para imagem*
      - **label/chave : \<input name="data"\>**                 *informação adicional
                                                                 para imagem*
      - *adicione quantos valores achar necessário*

jsonArray                                                                                                                                  <a name="type-jsonArray"></a>
---------

[▲](#default-types) Cria uma lista ordenada, utilizada para gravar diversos valores
em uma única coluna no banco de dados, apropriado para criação de campos do tipo TAG
ou lista de telefones.

- Parâmetros
    - **nullable** *pode ser nulo?*

manyToMany                                                                                                                                 <a name="type-manyToMany"></a>
----------

[▲](#default-types) Permite fazer relacionamento entre três tabelas, um relacionamento
muitos para muitos.

- Parâmetros
    - **nullable**        *pode ser nulo?
    - **middle-table**    *nome da tabela intermediária*
    - **middle-fk**       *coluna chave estrangeira da tabela intermediária*
    - **middle-pk**       *coluna chave primária da tabela intermediária*
    - **right-table**     *nome da tabela da direita*
    - **right-fk**        *coluna chave estrangeira da tabela da direita*
    - **right-label**     *coluna chave primária da tabela da direita*
    - **insert-form**     *caminho do formulário para inserção de novos valores*

meioMask                                                                                                                                   <a name="type-meioMask"></a>
--------

[▲](#default-types) Tipo baseado na famosa mascara jQuery
[meioMask](http://www.meiocodigo.com/projects/meiomask/).

- Parâmetros`
    - **nullable**        *pode ser nulo?*
    - **placeholder**     *texto placeholder*
    - **size**            *quantidade máxima de caracteres aceita*
    - **mask**            *mascara para o campo, mascaras preconfiguradas: phone,
                           phone-us, cpf, cnpj, date, date-us, cep, time e cc*

number                                                                                                                                     <a name="type-number"></a>
------

[▲](#default-types) Campo de entrada que aceita somente números
- Parâmetros
    - **min**             *valor mínimo aceito*
    - **max**             *valor máximo aceito*
    - **step**            *multiplos aceitos, 2 em 2, 3 em 3 etc.*

password                                                                                                                                   <a name="type-password"></a>
--------

[▲](#default-types) Campo de senha com confirmação de senha.

rawText                                                                                                                                    <a name="type-rawText"></a>
-------

[▲](#default-types) Texto integro (exatamente como foi gravado no banco) somente
para visualização, sem permissões para alterar.

textarea                                                                                                                                   <a name="type-textarea"></a>
--------

[▲](#default-types) Este tipo adiciona um textarea do HTML.

- Parâmetros
    - **nullable**        *pode ser nulo?*

3. Criando tipos personalizados                                                                                                            <a name="creating"></a>
===============================

[▲](#summary) O totem possui muitos tipos padrões, você pode saber mais sobre eles
no capítulo [Tipos de Inputs](#default-types), porém o totem dispões da possibilidade
de desenvolvimento de tipos customizados, para isso é preciso ter noções de javascript,
html, php e yaml.

Para iniciar a criação de um novo tipo é necessário primeiro criar uma pasta dentro
da pasta types com o nome do tipo que você deseja criar, imagine que iremos criar
um novo tipo com o nome *example*, logo para iniciar o processo de desenvolvimento
é preciso criar a pasta *types/example* e dentro desta pasta devem ter três arquivos
essenciais [config.yml](#config) e [config-events.php](#events), no caso do tipo
ser um tipo que necessita ser apresentado para o usuário em formulários de listagem,
inserção, exclusão ou boneco, é necessário ter mais um arquivo, responsavel por gerar
a [interface gráfica](#interface) do tipo, este arquivo pode ter nome variado pois
ele é definido no [config.yml](#config).

É possível criar arquivos CSS que serão carregados por referência no cabeçalho assim
como é possível carregar arquivos Javascript da mesma maneira, porém para os arquivos
Javascript existe uma outra possíbilidade que é a de incorporar diretamente no corpo
do HTML, para executar estas ações basta informar no [config.yml](#config) os caminhos
que deseja adicionar ao arquivo de interface, nos arquivos incoporados é possível
carregar valores do totem, sejam eles nativos do sistema ou valores epecíficos do
tipo ou ainda valores passados por parâmetros. **(atenção isso é válido somente
para os arquivos de interface e os arquivos Javascript incorporados)**.

3.1 Arquivo de configuração de tipo (config.yml)                                                                                           <a name="config"></a>
------------------------------------------------

[▲](#creating) Este arquivo é responsavel por informar ao sistema quais arquivos
serão utilizados para interpretação do tipo que você esta criando, por padrão o
nome deste arquivo deve sempre ser *config.yml* e deve estar sempre dentro de uma
pasta com o nome do tipo e por sua vez esta pasta deve estar dentro da pasta *types*
do módulo *backend*, este arquivo YAML indica quais são os arquivos javascript,
css e html que serão utilizados na interface gráfica, além dos parâmetros padrões
do tipo que você esta criando, veja a seguir um exemplo comentado deste arquivo:

```yaml
interface:
  html:
    # carrega arquivo HTML para ser mostrado na tela de listagem, não
    # é obrigatório e caso não seja informado deixa o sistema mais rápido
    list   : list.html

    # carrega o arquivo HTML para formulários de inserção
    insert : insert.html

    # carrega o arquivo HTML para formulários de atualização
    update : update.html

    # carrega o arquivo HTML para formulários de boneco
    dummy  : dummy.html

  css:
    # carrega arquivos CSS no head do formulário de
    # listagem, não é obrigatório
    list   : [ arq1.css, arq2.css, arq3.css ]

    # carrega arquivos CSS no head do formulário de
    # insert, não é obrigatório
    insert : [ arq1.css, arq2.css, arq3.css ]

    # carrega arquivos CSS no head do formulário de
    # update, não é obrigatório
    update : [ arq1.css, arq2.css, arq3.css ]

    # carrega arquivos CSS no head do formulário de
    # dummy, não é obrigatório
    dummy  : [ arq1.css, arq2.css, arq3.css ]

  javascript:
    head:
      # carrega arquivo javascript no head do formulário de
      # listagem, não é obrigatório
      list   : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript no head do formulário de
      # insert, não é obrigatório
      insert : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript no head do formulário de
      # update, não é obrigatório
      update : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript no head do formulário de
      # dummy, não é obrigatório
      dummy  : [ arq1.js, arq2.js, arq3.js ]

    body:
      # carrega arquivo javascript antes do fechamento do body do
      # formulário de listagem, não é obrigatório
      list   : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript antes do fechamento do body do
      # formulário de insert, não é obrigatório
      insert : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript antes do fechamento do body do
      # formulário de update, não é obrigatório
      update : [ arq1.js, arq2.js, arq3.js ]

      # carrega arquivo javascript antes do fechamento do body do
      # formulário de dummy, não é obrigatório
      dummy  : [ arq1.js, arq2.js, arq3.js ]

default:
  parameter:
    parâmetro-padrão1 : valor-padrão1         # define valores e parâmetros padrões
    parâmetro-padrão2 : valor-padrão2         # define valores e parâmetros padrões
    parâmetro-padrão3 : valor-padrão3         # define valores e parâmetros padrões
    parâmetro-padrão4 : valor-padrão4         # define valores e parâmetros padrões
    parâmetro-padrão 5:
        parâmetro-padrão1 : valor-padrão1
        parâmetro-padrão2 : valor-padrão2
        parâmetro-padrão3 : valor-padrão3
        parâmetro-padrão4 : valor-padrão4
  # adicione quantos parametros achar necessário
```


3.2 Arquivo de eventos (config-events.php)                                                                                                 <a name="events"></a>
------------------------------------------

[▲](#creating) Este arquivo deve ter mesmo nome *config-events.php* e deve conter
uma classe com o mesmo nome do tipo, é importante ressaltar que o arquivo com a
classe de eventos **não é definida** em *config.ini* e por isso deve seguir este
padrão de nomeclatura.

Esta classe PHP será responsave manipular os eventos do tipo que esta sendo criado,
veja a seguir os eventos que ele deve controlar com exemplos e explicações:

- [validate: Verifica se o campo não possui nenhum restrição para ser inserido,
   atualizado ou deletado.](#event-validate)
- [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
- [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
- [beforeList: Antes do formulário de listagem mostrar os valores na interface](#event-beforeList)
- [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
- [beforeLoadDataToForm: Antes de mostrar os valores que serão editados no formulário
   de atualização](#event-beforeLoadDataToForm)
- [afterInsert: Após executar *insert* no banco de dados](#event-afterInsert)
- [afterUpdate: Após executar *update* no banco de dados](#event-afterUpdate)
- [afterDelete: Após executar *delete* no banco de dados](#event-afterDelete)
- [ajax: Ao executar uma requisição ajax para o tipo](#event-ajax)

Para facilitar o entendimento dos parâmetros recebidos pelos métodos de eventos,
nós padrnizamos os nomes das variaveis, segue uma lista com as nomeclaruras que
criamos:

#### $thisData                                                                                                                             <a name="$thisData"></a>

> Contém o valor do campo atual.

#### $thisColumn                                                                                                                           <a name="$thisColumn"></a>

> Contém o nome da coluna que esta sendo editada, alterada ou removida no momento.

#### $allData                                                                                                                              <a name="$allData"></a>

> Contém um array com todos os valores e etiquetas de todos os campos do formulário
> que esta sendo submetido.

#### $thisLabel                                                                                                                            <a name="$thisLabel"></a>

> Contém o valor da etiqueta do campo que esta sendo editado, alterado ou removido
> no momento.

#### $parameters                                                                                                                           <a name="$parameters"></a>

> Contém um array com os parâmetros informados no formulário para o campo atual.

#### $pkey                                                                                                                                 <a name="$pKey"></a>

> Contém o array com a chave primária do valor que esta sendo inserido, atualizado,
> deletado ou listado, com nome do campo e valor.

#### $thisRow                                                                                                                              <a name="$thisRow"></a>

> Utilizado somente nas listagens, contém um array com os nomes e valores de todas
> as colunas da linha atual que está sendo listada.

#### $toTypeLayout <a name="$toTypeLayout"></a>

> Contém um array com valores que podem ser enviados para o arquivo de interface,
> ou javascript de rodapé informados no arquivo [config.yaml](#config).

### 3.2.1 validate: Verifica se o campo não possui nenhum restrição para ser inserido, atualizado ou deletado.                             <a name="event-validate"></a>

[▲](#events) Antes de executar qualquer evento no formulário todos os campos são
válidados, o evento responsável por validar cada campo é o *validate* dos tipos de
input, este evento responsáveis por verificar os valor contido no campo na interface,
caso este método não for criado ou seu retorno for nulo, os campos sempre serão válidos.

Para retornar uma mensagem de erro é preciso que este método retorne um array com
duas chaves a primeira é *error* e seu valor dever ser um boleano verdadeiro e a
segunda chave deve ser *message* e contém o texto para a mensagem de erro para o
campo atual.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$thisLabel](#thisLabel)

Veja a seguir um exemplo de um tipo que não permite valores nulos em seu campo:

```php
<?php
    class example{
        public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){
            if( empty( $thisData ) ){
                return Array( "error" => true, "message" => "O campo $thisLabel, não pode ser vazio." );
            }else{
                return Array( "error" => false );
            }
        }
    }
```

### 3.2.2 beforeInsert: Antes de executar *insert* no banco de dados                                                                       <a name="event-beforeInsert"></a>

[▲](#events) Antes de executar insert no banco de dados este método é chamado para
cada campo do formulário, isto permite que seja executada alguma ação para qualquer
campo antes que ele seja inserido no banco de dados, dentre estas ações é possível
até mesmo remover a inserção de um determinado campo até mesmo a alteração ou formatação
do campo que deverá ser inserido no banco de dados.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

Veja a seguir como funciona a inserção de um campo do tipo dateBr, os campos dataBr
formatam a varivel do tipo data para o formato compativel com o MySQL:

```php
<?php
    class example{
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            $thisData = "{$thisData['year']}-{$thisData['month']}-{$thisData['day']}";
        }
    }
```

O HTML deste tipo não renderizado é o seguite:

```HTML
<div class="input-holder &m.var:type;">
  <label>&m.var:label;</label>
  <div class="inner-holder">

    <div class="box year">
      <label>Ano</label>
      <select name="&m.var:name;[year]" class="input-year" required>
          <m.if cond="&m.var:bool:parameter.nullable;">
            <option val="--">--</option>
          </m.if>
          <m.repeat start="toLayout.year.start" stop="toLayout.year.stop" key="YEAR">
            <option>__YEAR__</option>
          </m.repeat>
      </select>
    </div>

    <div class="box month">
      <label>Mês</label>
      <select name="&m.var:name;[month]" class="input-month" required>
        <m.if cond="&m.var:bool:parameter.nullable;">
          <option val="--">--</option>
        </m.if>
        <m.repeat start="01" stop="12" key="MONTH">
          <option>__MONTH__</option>
        </m.repeat>
      </select>
    </div>

    <div class="box day">
      <label>Dia</label>
      <select name="&m.var:name;[day]" class="input-day" required>
        <option>1</option>
      </select>
    </div>

    <div class="clear"></div>
  </div>
</div>
```

### 3.2.3 beforeUpdate: Antes de executar *update* no banco de dados                                                                       <a name="event-beforeUpdate"></a>

[▲](#events) Tem o mesmo funcionamento do [beforeInsert](#event-beforeInsert),
porém este evento é executado antes de ser executado o insert no banco de dados.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

> Veja o exemplo do tópico [beforeInsert](#event-beforeInsert) e considere apenas
> trocar o nome do método de *beforeInsert* para *beforeUpdate*.

### 3.2.4 beforeList: Antes do formulário de listagem mostrar os valores na interface                                                      <a name="event-beforeList"></a>

[▲](#events) Este evento é executado logo após ser feita a busca de informações
no banco de dados e antes destas informações serem apresentadas em tela, o que permite 
a execução de qualquer processo antes mesmo que os dados sejam impressos na tela, 
tornando viável também a formatação/manipulação dos dados apresentados.

> Note que se caso não seja extremamente necessário **deve ser evitado o uso deste
> evento**, pelo simples fato de que ele é executado para cada um dos dados apresentados
> em tela o que pode tornar a listagem muito mais lenta.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisRow](#thisRow)
- [$thisColumn](#thisColumn)
- [$allData](#allData)

Imagine uma situação onde é preciso formatar uma coluna com mascara de telefone,
veja o exemplo a seguir para esta situação:

```php
<?php
    class example{
        public function beforeList(&$thisData, $thisRow, $thisColumn, &$allData){
            $mascara = "(##)####-####";
            $thisData = str_replace(" ","",$thisData);
            for($i=0;$i<strlen($thisData);$i++){
               $mascara[strpos($mascara,"#")] = $thisData[$i];
            }
            $thisData = $mascara;
        }
    }
```

### 3.2.5 beforeDelete: Antes de executar *delete* no banco de dados                                                                       <a name="event-beforeDelete"></a>

[▲](#events) Antes de executar delete no banco de dados este método é chamado para
cada campo do formulário, assim é possível executar quaisquer processos relacionados 
a exclusão de determinado campo do formulário.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

Imagine que um arquivo deve ser excluido antes que os valores que possuem algum
tipo de relação com este arquivo seja removido, neste exemplo imagine que um arquivo 
de imagem JPG com o mesmo nome do campo deverá ser excluido.

```php
<?php
    class example{
        public function beforeDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            if( file_exists("{$thisData}.jpg") ){
                unlink("{$thisData}.jpg"):
            }else{
                return Array( "error" => true, "message" => "O arquivo {$thisData}.jpg, não foi encontrado." );
            }
        }
    }
```

### 3.2.6 beforeLoadDataToForm: Antes de mostrar os valores que serão editados no formulário de atualização                                <a name="event-beforeLoadDataToForm"></a>

[▲](#events) …

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$toTypeLayout](#toTypeLayout)
- [$pKey](#pKey)

```php
<?php
    class example{
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){
            // code here
        }
    }
```

### 3.2.7 afterInsert: Após executar *insert* no banco de dados                                                                            <a name="event-afterInsert"></a>

[▲](#events) Tem o mesmo funcionamento do [beforeInsert](#event-beforeInsert),
porém este evento é executado após de ser executado o insert no banco de dados.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

> Veja o exemplo do tópico [beforeInsert](#event-beforeInsert) e considere apenas
> trocar o nome do método de *beforeInsert* para *beforeUpdate*.
```

### 3.2.8 afterUpdate: Após executar *update* no banco de dados                                                                            <a name="event-afterUpdate"></a>

[▲](#events) Tem o mesmo funcionamento do [beforeInsert](#event-beforeInsert),
porém este evento é executado após ser executado o update no banco de dados.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

> Veja o exemplo do tópico [beforeInsert](#event-beforeInsert) e considere apenas
> trocar o nome do método de *beforeInsert* para *beforeUpdate*.
```

### 3.2.9 afterDelete: Após executar *delete* no banco de dados                                                                            <a name="event-afterDelete"></a>

[▲](#events) Este evento é muito semelhante ao evento [beforeDelete](#event-beforeDelete),
somente se diferenciando no memento em que ele é executado, este evento é executado
logo após a execução do delete no banco de dados, enquanto o [beforeDelete](#event-beforeDelete)
é executado antes da execução do delete.

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

> Veja o exemplo do tópico [beforeDelete](#event-beforeDelete) e considere apenas
> trocar o nome do método de *beforeDelete* para *afterDelete*.

### 3.2.10 ajax: Ao executar uma requisição ajax para o tipo                                                                               <a name="event-ajax"></a>

[▲](#events) …

Este evento possui os seguintes parâmetros:

```php
<?php
    class example{
        public function ajax(){
            // code here
        }
    }
```

3.3 Arquivo de interface                                                                                                                   <a name="interface"></a>
------------------------

[▲](#creating) …

### 3.3.1 Como acessar valores do sistema na interface                                                                                     <a name="interface-access-values"></a>

[▲](#interface) …

#### 3.3.1.1 Como acessar valores do totem                                                                                                 <a name=""></a>

[▲](#interface-access-values) …

#### 3.3.1.2 Como acessar valores do tipo                                                                                                  <a name=""></a>

[▲](#interface-access-values) …
