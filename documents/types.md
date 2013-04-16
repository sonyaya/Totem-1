<a name="summary" id="summary"></a>
Sumário
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
    - [O arquivo de configuração (config.yml)](#config)
    - [Arquivo de eventos](#events)
        - [validate: Verifica se o campo não possui nenhum restrição para ser inserido, atualizado ou deletado.](#event-validate)
        - [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
        - [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
        - [beforeList: Antes do formulário de listagem mostrar os valores na interface](#event-beforeList)
        - [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
        - [beforeLoadDataToForm: Antes de mostrar os valores que serão editados no formulário de atualização](#event-beforeLoadDataToForm)
        - [afterInsert: Após executar *insert* no banco de dados](#event-afterInsert)
        - [afterUpdate: Após executar *update* no banco de dados](#event-afterUpdate)
        - [afterDelete: Após executar *delete* no banco de dados](#event-afterDelete)
        - [ajax: Quando for executa uma requisição ajax para o tipo](#event-ajax)
    - [Arquivo de interface](#interface)
4. Configurando os parâmetros de tipos

<a name="intro" id="intro"></a>
1. Tipos de Inputs
==================

[▲](#summary) *Tipos*, *types*, *tipos de inputs* ou até mesmo *inputs* são possíveis nomeclaturas para apresentar a base de qualquer formulário do módulo *backend*, estes objetos são reponsaveis por criar a aparencia e funcionamento para entrada de dados no sistema, criar validadores de interface, ajax e server side além de tratar os valores que são inseridos, editados ou mesmo excluidos do banco de dados.

É possivel criar os mais diversos *tipos de inputs*, seja uma simples entrada de texto até mesmo um relacionamento entre duas ou mais tabélas, por padrão o **totem** possui os seguintes tipos:

<a name="default-types" id="default-types"></a>
2. Tipos padrões
================

[▲](#summary) …

<a name="type-CKEditor" id="type-CKEditor"></a>
## CKEditor
- [▲](#default-types) Adiciona um tipo baseado na biblioteca [CKEditor](http://ckeditor.com/).
- Parâmetros
    - …

<a name="type-combobox" id="type-combobox"></a>
## combobox
- [▲](#default-types) Cria um objeto com a tag select do HTML, ele permite que o usuário decida quais serão os valores inseridos no banco de dados e as etiquetas que são apresentadas para o usuário.
- Parâmetros
    - **valor-1 : etiqueta-1**
    - **valor-2 : etiqueta-2**
    - **valor-3 : etiqueta-3**
      - *adicione quantos valores achar necessário*

<a name="type-dateBr" id="type-dateBr"></a>
## dateBr
- [▲](#default-types) Cria três objetos do tipo select do HTML, o primeiro com os anos, o segundo com os meses e o terceiro com os dias. Quando enviado para o banco o formato é o padrão do MySQL (Y-m-d).
- Parâmetros
    - **nullable** *pode ser nulo?*
    - **year**
        - **start** *ano de inicio da listagem*
        - **stop**  *ano máximo da listagem*

<a name="type-dateTimeBr" id="type-dateTimeBr"></a>
## dateTimeBr
- [▲](#default-types) Parecido com o dateBr com a diferença que o *dateTimeBr* possui selects do HTML a mais, o primeiro é a hora, o segundo refere-se aos minutos e o tereiro aos segundos.
- Parâmetros
    - **nullable** *pode ser nulo?*
    - **year**
        - **start** *ano de inicio da listagem*
        - **stop**  *ano máximo da listagem*

<a name="type-example" id="type-example"></a>
## example
- [▲](#default-types) É um tipo utilizado para demonstrar como são feitos os tipos.

<a name="type-fk" id="type-fk"></a>
## fk
- [▲](#default-types) Parecido com o combobox, porém neste tipo os valores são buscados de uma tabela no banco de dados, criando assim um relação simples entre as tabelas.
- Parâmetros
    - **table**       *tabela no banco de dados*
    - **column**      *coluna do banco de dados que será utilizada para os valores do select*
    - **label**       *coluna do banco de dados que erá utilizada para as etiquetas do select*
    - **insert-form** *caminho do formulário para inserção de novos valores*

<a name="type-imagesUpload" id="type-imagesUpload"></a>
## imagesUpload
- [▲](#default-types) Permite envio de imagens para o sistema, estas imagens são salvas em um caminho informado nos parâmetros e em uma sub-pasta com o nome do valor da chave primária, além de permitir ordenação e adição de informação diversas na imagem, atente-se que ele insere um jSon no banco de dados.
- Parâmetros
   - **folder** *caminho da pasta que serão gravadas as imagens*
   - **inputs**
      - **label/chave : \<textarea name="data"\>\</textarea\>** *informação adicional para imagem*
      - **label/chave : \<input name="data"\>**                 *informação adicional para imagem*
      - *adicione quantos valores achar necessário*

<a name="type-jsonArray" id="type-jsonArray"></a>
## jsonArray
- [▲](#default-types) Cria uma lista ordenada, utilizada para gravar diversos valores em uma única coluna no banco de dados, apropriado para criação de campos do tipo TAG ou lista de telefones.
- Parâmetros
    - **nullable** *pode ser nulo?*

<a name="type-manyToMany" id="type-manyToMany"></a>
## manyToMany
- [▲](#default-types) Permite fazer relacionamento entre três tabelas, um relacionamento muitos para muitos.
- Parâmetros
    - **nullable**     *pode ser nulo?
    - **middle-table** *nome da tabela intermediária*
    - **middle-fk**    *coluna chave estrangeira da tabela intermediária*
    - **middle-pk**    *coluna chave primária da tabela intermediária*
    - **right-table**  *nome da tabela da direita*
    - **right-fk**     *coluna chave estrangeira da tabela da direita*
    - **right-label**  *coluna chave primária da tabela da direita*
    - **insert-form**  *caminho do formulário para inserção de novos valores*

<a name="type-meioMask" id="type-meioMask"></a>
## meioMask
- [▲](#default-types) Tipo baseado na famosa mascara jQuery [meioMask](http://www.meiocodigo.com/projects/meiomask/).
- Parâmetros
    - **nullable**     *pode ser nulo?*
    - **placeholder**  *texto placeholder*
    - **size**         *quantidade máxima de caracteres aceita*
    - **mask**         *mascara para o campo, mascaras preconfiguradas: phone, phone-us, cpf, cnpj, date, date-us, cep, time e cc*

<a name="type-number" id="type-number"></a>
## number
- [▲](#default-types) Campo de entrada que aceita somente números
- Parâmetros
    - **min**  *valor mínimo aceito*
    - **max**  *valor máximo aceito*
    - **step** *multiplos aceitos, 2 em 2, 3 em 3 etc.*

<a name="type-password" id="type-password"></a>
## password
- [▲](#default-types) Campo de senha com confirmação de senha.

<a name="type-rawText" id="type-rawText"></a>
## rawText
- [▲](#default-types) Texto integro (exatamente como foi gravado no banco) somente para visualização, sem permissões para alterar.

<a name="type-textarea" id="type-textarea"></a>
## textarea
- [▲](#default-types) Este tipo adiciona um textarea do HTML.
- Parâmetros
    - **nullable**     *pode ser nulo?*

<a name="creating" id="creating"></a>
3. Criando tipos personalizados
==============================

[▲](#summary) …

<a name="config" id="config"></a>
## 3.1 config.yml

[▲](#creating) Este arquivo é responsavel por informar ao sistema quais arquivos serão utilizados para criação do tipo, ele indica quais são os arquivos javascript, css e html que serão utilizados na interface gráfica, além dos parâmetros padrões do tipo que você esta criando, veja a seguir um exemplo comentado:

```
interface:
  html:
    list   : list.html   # carrega arquivo HTML para ser mostrado na tela de listagem, não é obrigatório e caso não seja informado deixa o sistema mais rápido
    insert : insert.html # carrega o arquivo HTML para formulários de inserção
    update : update.html # carrega o arquivo HTML para formulários de atualização
    dummy  : dummy.html  # carrega o arquivo HTML para formulários de boneco

  css:         
    list   : []          # carrega arquivos CSS no head do formulário de listagem, não é obrigatório
    insert : []          # carrega arquivos CSS no head do formulário de insert, não é obrigatório
    update : []          # carrega arquivos CSS no head do formulário de update, não é obrigatório
    dummy  : []          # carrega arquivos CSS no head do formulário de dummy, não é obrigatório

  javascript:
    head:
      list   : []        # carrega arquivo javascript no head do formulário de listagem, não é obrigatório
      insert : []        # carrega arquivo javascript no head do formulário de insert, não é obrigatório
      update : []        # carrega arquivo javascript no head do formulário de update, não é obrigatório
      dummy  : []        # carrega arquivo javascript no head do formulário de dummy, não é obrigatório

    body:
      list   : []        # carrega arquivo javascript antes do fechamento do body do formulário de listagem, não é obrigatório
      insert : []        # carrega arquivo javascript antes do fechamento do body do formulário de insert, não é obrigatório
      update : []        # carrega arquivo javascript antes do fechamento do body do formulário de update, não é obrigatório
      dummy  : []        # carrega arquivo javascript antes do fechamento do body do formulário de dummy, não é obrigatório

default:
  parameter : 
    parâmetro-padrão : valor-padrão # define valores e parâmetros padrões
```

<a name="events" id="events"></a>
## 3.2 Arquivo de eventos

[▲](#creating) Este arquivo deve ter mesmo nome da sua pasta do tipo porém com a extensão PHP, este arquivo deve conter uma classe com o mesmo nome do tipo, e será responsave manipular os eventos do tipo, os eventos que ele irá controlar são os seguintes:

<a name="event-validate" id="event-validate"></a>
### 3.2.1 validate: Verifica se o campo não possui nenhum restrição para ser inserido, atualizado ou deletado.

[▲](#events) …

```php
<?php
    class example{
        public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){
            // code here
        }
    }
```

<a name="event-beforeInsert" id="event-beforeInsert"></a>
### 3.2.2 beforeInsert: Antes de executar *insert* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-beforeUpdate" id="event-beforeUpdate"></a>
### 3.2.3 beforeUpdate: Antes de executar *update* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function beforeUpdate(&$thisData, $thisColumn, &$allData, $parameters,  $pKey){
            // code here
        }
    }
```

<a name="event-beforeList" id="event-beforeList"></a>
### 3.2.4 beforeList: Antes do formulário de listagem mostrar os valores na interface

[▲](#events) …

```php
<?php
    class example{
        public function beforeList(&$thisData, $thisRow, $thisColumn, &$allData){
            // code here
        }
    }
```

<a name="event-beforeDelete" id="event-beforeDelete"></a>
### 3.2.5 beforeDelete: Antes de executar *delete* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function beforeDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-beforeLoadDataToForm" id="event-beforeLoadDataToForm"></a>
### 3.2.6 beforeLoadDataToForm: Antes de mostrar os valores que serão editados no formulário de atualização

[▲](#events) …

```php
<?php
    class example{
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){ 
            // code here 
        }
    }
```

<a name="event-afterInsert" id="event-afterInsert"></a>
### 3.2.7 afterInsert: Após executar *insert* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function afterInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-afterUpdate" id="event-afterUpdate"></a>
### 3.2.8 afterUpdate: Após executar *update* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function afterUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-afterDelete" id="event-afterDelete"></a>
### 3.2.9 afterDelete: Após executar *delete* no banco de dados

[▲](#events) …

```php
<?php
    class example{
        public function afterDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-ajax" id="event-ajax"></a>
### 3.2.10 ajax:  Quando for executa uma requisição ajax para o tipo

[▲](#events) …
```php
<?php
    class example{
        public function ajax(){
            // code here
        }
    }
```

<a name="interface" id="interface"></a>
## 2.3 Arquivo de interface

[▲](#creating) …