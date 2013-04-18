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
    - [Arquivo de configuração de tipo (config.yml)](#config)
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
        - [ajax: Ao executar uma requisição ajax para o tipo](#event-ajax)
    - [Arquivo de interface](#interface)
        - [Como acessar valores do sistema na interface](#interface-acess-values)
            - [Como acessar valores do totem](#interface-acess-totem-values)
            - [Como acessar valores do tipo](#interface-acess-type-values)
4. Configurando os parâmetros de tipos

<a name="intro" id="intro"></a>
1. Tipos de Inputs
==================

[▲](#summary) *Tipos*, *types*, *tipos de inputs* ou até mesmo *inputs* são possíveis nomeclaturas para apresentar a base de qualquer formulário do módulo *backend*, estes objetos são reponsaveis por criar a aparencia e funcionamento para entrada de dados no sistema, criar validadores de interface, ajax e server side além de tratar os valores que são inseridos, editados ou mesmo excluidos do banco de dados.

É possivel criar os mais diversos *tipos de inputs*, seja uma simples entrada de texto até mesmo um relacionamento entre duas ou mais tabélas, por padrão o **totem** possui os seguintes tipos:

<a name="default-types" id="default-types"></a>
2. Tipos padrões
================

[▲](#summary) O totem possui alguns tipos padrões, estes tipos podem ajudar novos usuários a personalizar seus formulários conforme sua necessidade, porém em casos que os tipos padrões não suprem as necessidades de desenvolvimento é possível criar tipos para ações específicas, leia o tópico [Criando um tipo personalizado](#creating) e veja como criar seu próprio tipo, a seguir veja a lista de tipos padrões e suas especificações:

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
    - **nullable**        *pode ser nulo?
    - **middle-table**    *nome da tabela intermediária*
    - **middle-fk**       *coluna chave estrangeira da tabela intermediária*
    - **middle-pk**       *coluna chave primária da tabela intermediária*
    - **right-table**     *nome da tabela da direita*
    - **right-fk**        *coluna chave estrangeira da tabela da direita*
    - **right-label**     *coluna chave primária da tabela da direita*
    - **insert-form**     *caminho do formulário para inserção de novos valores*

<a name="type-meioMask" id="type-meioMask"></a>
## meioMask
- [▲](#default-types) Tipo baseado na famosa mascara jQuery [meioMask](http://www.meiocodigo.com/projects/meiomask/).
- Parâmetros
    - **nullable**        *pode ser nulo?*
    - **placeholder**     *texto placeholder*
    - **size**            *quantidade máxima de caracteres aceita*
    - **mask**            *mascara para o campo, mascaras preconfiguradas: phone, phone-us, cpf, cnpj, date, date-us, cep, time e cc*

<a name="type-number" id="type-number"></a>
## number
- [▲](#default-types) Campo de entrada que aceita somente números
- Parâmetros
    - **min**             *valor mínimo aceito*
    - **max**             *valor máximo aceito*
    - **step**            *multiplos aceitos, 2 em 2, 3 em 3 etc.*

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
    - **nullable**        *pode ser nulo?*

<a name="creating" id="creating"></a>
3. Criando tipos personalizados
==============================

[▲](#summary) O totem possui muitos tipos padrões, você pode saber mais sobre eles no capítulo [Tipos de Inputs](#default-types), porém o totem dispões da possibilidade de desenvolvimento de tipos customizados, para isso é preciso ter noções de javascript, html, php e yaml.

Para iniciar a criação de um novo tipo é necessário primeiro criar uma pasta dentro da pasta types com o nome do tipo que você deseja criar, imagine que iremos criar um novo tipo com o nome *example*, logo para iniciar o processo de desenvolvimento é preciso criar a pasta *types/example* e dentro desta pasta devem ter três arquivos essenciais [config.yml](#config) e [config-events.php](#events), no caso do tipo ser um tipo que necessita ser apresentado para o usuário em formulários de listagem, inserção, exclusão ou boneco, é necessário ter mais um arquivo, responsavel por gerar a [interface gráfica](#interface) do tipo, este arquivo pode ter nome variado pois ele é definido no [config.yml](#config).

É possível criar arquivos CSS que serão carregados por referência no cabeçalho assim como é possível carregar arquivos Javascript da mesma maneira, porém para os arquivos Javascript existe uma outra possíbilidade que é a de incorporar diretamente no corpo do HTML, para executar estas ações basta informar no [config.yml](#config) os caminhos que deseja adicionar ao arquivo de interface, nos arquivos incoporados é possível carregar valores do totem, sejam eles nativos do sistema ou valores epecíficos do tipo ou ainda valores passados por parâmetros. **(atenção isso é válido somente para os arquivos de interface e os arquivos Javascript incorporados)**.

<a name="config" id="config"></a>
## 3.1 Arquivo de configuração de tipo (config.yml)

[▲](#creating) Este arquivo é responsavel por informar ao sistema quais arquivos serão utilizados para interpretação do tipo que você esta criando, por padrão o nome deste arquivo deve sempre ser *config.yml* e deve estar sempre dentro de uma pasta com o nome do tipo e por sua vez esta pasta deve estar dentro da pasta *types* do módulo *backend*, este arquivo YAML indica quais são os arquivos javascript, css e html que serão utilizados na interface gráfica, além dos parâmetros padrões do tipo que você esta criando, veja a seguir um exemplo comentado deste arquivo:

```yaml
interface:
  html:
    list   : list.html                        # carrega arquivo HTML para ser mostrado na tela de listagem, não é obrigatório e caso não seja informado deixa o sistema mais rápido
    insert : insert.html                      # carrega o arquivo HTML para formulários de inserção
    update : update.html                      # carrega o arquivo HTML para formulários de atualização
    dummy  : dummy.html                       # carrega o arquivo HTML para formulários de boneco
 
  css:       
    list   : [ arq1.css, arq2.css, arq3.css ] # carrega arquivos CSS no head do formulário de listagem, não é obrigatório
    insert : [ arq1.css, arq2.css, arq3.css ] # carrega arquivos CSS no head do formulário de insert, não é obrigatório
    update : [ arq1.css, arq2.css, arq3.css ] # carrega arquivos CSS no head do formulário de update, não é obrigatório
    dummy  : [ arq1.css, arq2.css, arq3.css ] # carrega arquivos CSS no head do formulário de dummy, não é obrigatório
 
  javascript:
    head:
      list   : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript no head do formulário de listagem, não é obrigatório
      insert : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript no head do formulário de insert, não é obrigatório
      update : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript no head do formulário de update, não é obrigatório
      dummy  : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript no head do formulário de dummy, não é obrigatório
 
    body:
      list   : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript antes do fechamento do body do formulário de listagem, não é obrigatório
      insert : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript antes do fechamento do body do formulário de insert, não é obrigatório
      update : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript antes do fechamento do body do formulário de update, não é obrigatório
      dummy  : [ arq1.js, arq2.js, arq3.js ]  # carrega arquivo javascript antes do fechamento do body do formulário de dummy, não é obrigatório
 
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

<a name="events" id="events"></a>
## 3.2 Arquivo de eventos (config-events.php)

[▲](#creating) Este arquivo deve ter mesmo nome *config-events.php* e deve conter uma classe com o mesmo nome do tipo, é importante ressaltar que o arquivo com a classe de eventos **não é definida** em *config.ini* e por isso deve seguir este padrão de nomeclatura.

Esta classe PHP será responsave manipular os eventos do tipo que esta sendo criado, veja a seguir os eventos que ele deve controlar com exemplos e explicações:

- [validate: Verifica se o campo não possui nenhum restrição para ser inserido, atualizado ou deletado.](#event-validate)
- [beforeInsert: Antes de executar *insert* no banco de dados](#event-beforeInsert)
- [beforeUpdate: Antes de executar *update* no banco de dados](#event-beforeUpdate)
- [beforeList: Antes do formulário de listagem mostrar os valores na interface](#event-beforeList)
- [beforeDelete: Antes de executar *delete* no banco de dados](#event-beforeDelete)
- [beforeLoadDataToForm: Antes de mostrar os valores que serão editados no formulário de atualização](#event-beforeLoadDataToForm)
- [afterInsert: Após executar *insert* no banco de dados](#event-afterInsert)
- [afterUpdate: Após executar *update* no banco de dados](#event-afterUpdate)
- [afterDelete: Após executar *delete* no banco de dados](#event-afterDelete)
- [ajax: Ao executar uma requisição ajax para o tipo](#event-ajax)

Para facilitar o entendimento dos parâmetros recebidos pelos métodos de eventos, nós padrnizamos os nomes das variaveis, segue uma lista com as nomeclaruras que criamos

<a name="$thisData" id="$thisData"></a>
### $thisData

    Contém o valor do campo atual.

<a name="$thisColumn" id="$thisColumn"></a>
### $thisColumn

    Contém o nome da coluna que esta sendo editada, alterada ou removida no momento.

<a name="$allData" id="$allData"></a>
### $allData

    Contém um array com todos os valores e etiquetas de todos os campos do formulário que esta 
    sendo submetido.

<a name="$thisLabel" id="$thisLabel"></a>
### $thisLabel

    Contém o valor da etiqueta do campo que esta sendo editado, alterado ou removido no momento.

<a name="$parameters" id="$parameters"></a>
### $parameters

    Contém um array com os parâmetros informados no fomulário para o campo atual.

<a name="$pKey" id="$pKey"></a>
### $pkey 

    Contém o array com a chave primária do valor que esta sendo inserido, atualizado, deletado 
    ou listado, com nome do campo e valor.

<a name="$thisRow" id="$thisRow"></a>
### $thisRow

    Utilizado somente nas listagens, contém um array com os nomes e valores de todas as colunas 
    da linha atual que está sendo listada.

<a name="$toTypeLayout" id="$toTypeLayout"></a>
### $toTypeLayout

    Contém um array com valores que podem ser enviados para o arquivo de interface, ou javascript de 
    rodapé informados no arquivo [config.yaml](#config).

<a name="event-validate" id="event-validate"></a>
### 3.2.1 validate: Verifica se o campo não possui nenhum restrição para ser inserido, atualizado ou deletado.

[▲](#events) Antes de executar qualquer evento no formulário todos os campos são válidados, o evento resposavel por validar cada campo é o *validate* dos tipos de input, este evento responsáveis por validar os valores contidos em seu campo na interface, caso este método não for criado ou seu retorno for nulo, os campos sempre serão válidos.

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

<a name="event-beforeInsert" id="event-beforeInsert"></a>
### 3.2.2 beforeInsert: Antes de executar *insert* no banco de dados

[▲](#events) …

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

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

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

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

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisRow](#thisRow)
- [$thisColumn](#thisColumn)
- [$allData](#allData)

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

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

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

<a name="event-afterInsert" id="event-afterInsert"></a>
### 3.2.7 afterInsert: Após executar *insert* no banco de dados

[▲](#events) …

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

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

Este evento possui os seguintes parâmetros:

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

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

- [$thisData](#thisData)
- [$thisColumn](#thisColumn)
- [$allData](#allData)
- [$parameters](#parameters)
- [$pKey](#pKey)

[▲](#events) …

Este evento possui os seguintes parâmetros:

```php
<?php
    class example{
        public function afterDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            // code here
        }
    }
```

<a name="event-ajax" id="event-ajax"></a>
### 3.2.10 ajax: Ao executar uma requisição ajax para o tipo

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

<a name="interface" id="interface"></a>
## 2.3 Arquivo de interface

[▲](#creating) …

<a name="interface-acess-values" id="interface-acess-values"></a>
### 2.3.1 Como acessar valores do sistema na interface](#interface-acess-values)

[▲](#interface) …

<a name="" id=""></a>
#### 2.3.1.1 Como acessar valores do totem

[▲](#interface-acess-values) …

<a name="" id=""></a>
#### 2.3.1.2 Como acessar valores do tipo

[▲](#interface-acess-values) …
