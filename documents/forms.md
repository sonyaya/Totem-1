Sumário
=======

- [Introdução](#intro)
- [Cabeçalho do Arquivo](#head)
- [Tipos de Formulários](#form-types)
    - [Inserção (insert)](#save-form)
    - [Atualização (update)](#save-form)
    - [Boneco (dummy)](#dummy-form)
    - [Listagem (list)](#list-form)
    - [Exclusão (delete)](#delete-form)
    - [Formulário para Rest API (bridge)](#rest-form)
- [Eventos de formulários](#events)
- [Como clonar formulários](#clone-form)


<a id="intro">&nbsp;</a>
Formulários
===========

Os formulários são configurados por um arquivo YAML preferencialmente devem ser gravados na pasta "system/backend/modules/seu modulo/forms/arquivo.yml_", este único arquivo deve conter a configuração para os seguintes formulários:

- [Formulário de Inserção](#save-form)
- [Formulário de Atualização](#save-form)
- [Formulário de Boneco (dummy)](#dummy-form)
- [Formulário para telas de listagem (list)](#list-form)
- [Formulário de exclusão (delete)](#delete-form)
- [E formulário para Rest API (bridge)](#rest-form)

São basicamente utilizados para informar ao módulo **backend** quais são as rotinas de inserção, atualização, exclusão além de rotinas mais específicas, porém também podem prover informações para outros módulos como o **bridge (api)** e consequentemente para o **frontend**.

<a id="save-form">&nbsp;</a>
## Cabeçalho

Este é responsável por informar qual é o título principal dos formulários, além de descrever qual é a tabela e chave primária do banco de dados que serão  utilizados por estes formulários, segue exemplo de cabeçalho do arquivo YAML:

```
header:
    title: Nome pricipal de seus formulários
    table: nome_da_tabela_no_banco_de_dados
    p-key: nome_da_chave_primaria_da_tabela
```

<a id="save-form">&nbsp;</a>
Tipos de Formulários
====================

Existem diversos tipos de formulários cada um com uma funcionalidade específica… … 

<a id="save-form">&nbsp;</a>
## Formulário de Inserção ou Atualização

Os dois formulários são criados exatamente iguais, portanto esta descrição funciona tanto pra um quanto pra outro diferenciando apenas no fato que a chave para referência de configuração são distintas, no caso do formulário de inserção o caminho da chave de referência é *forms/insert* e o formulário de atualização tem o caminho *forms/update*.

Estes formulários possuem três propriedades, são elas:

- title
    - refere-se ao titulo secundário do formulário, para uma descrição pouco mais aprimorada
- input
    - é responsável de informar quais os tipos e as propriedades dos tipos o formulário ira conter, para intender melhor o que são e como funcionam estes tipos leia [a documentação de tipos](types.md).
- merge-form
    - não é uma propriedade obrigatória, é utilizado quando existe a necessidade de copiar os inputs de um outro formulário, isso é útil quando temos dois formulários que irão utilizar exatamente os mesmo ou grande maiorias das propriedades de outro formulário, veja mais informações em [como clonar formulários](#clone-form).

```
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


<a id="dummy-form">&nbsp;</a>
## Boneco (dummy)

É o tipo de formulário utilizado para criação de qualquer processo que não possa ser feito com os outros formulários, também é o mais indicado para criação de relatórios.

Seus parâmetros são muito semelhantes aos parâmetros dos formulários de inserção e atualização além de funcionarem basicamente igual, porém com o diferencial que por padrão este formulário não executa diretamente inserção, atualização ou qualquer outro processo, ele apenas irá executar o arquivo *PHP* que for indicado pelo parâmetro *php:*, veja como este formulário é configurado:
   

```
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
   
<a id="list-form">&nbsp;</a>
## Formulário de Listagem
 
O formulário de listagem é exatamente igual ao formulário de inserção e ao formulário de atualização, exceto pelo fato de possuir uma propriedade extra, utilizada especificamente para informar quantos itens serão apresentados em cada página listagem, esta propriedade não é obrigatória, porém caso não informada o sistema passará a considerar o valor informados no arquivo de configuração config.uni.php na propriedade backend/rows-per-page.

Veja um exemplo de como este trecho do formulário pode ser:

```
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
   
<a id="delete-form">&nbsp;</a>
## Formulário de Exclusão

O formulário de exclusão funciona exatamente como os formulários de edição e inserção, porém com o diferencial que neste formulário os tipos (inputs) não são usados para entrada de dados, eles são utilizados apenas para validar se os dados que serão removidos realmente podem ser eliminados ou executar alguma ação específica antes ou após a exclusão, por não se tratar de um formulário visual, este formulário dispensa a necessidade do parametro *title*. 

Veja um exemplo de copo pode ser o trecho deste tipo de formulário no arquivo YAML:

```
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

<a id="rest-form">&nbsp;</a>
## Formulário Rest API (bridge)

Este formulário é reponsável por fornecer ao módulo **bridge** quais serão os tipos de inputs que serão executados, assim como o formulário de exclusão ele não é  um formulário visual, porém a api pode retornar conteúdos paginados o que faz com que este formulário possa ter o parâmetro *rows-per-page*.

Veja como pode ser este trecho do YAML:

```
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

<a id="events">&nbsp;</a>
## Eventos de formulários

…
<a id="clone-form">&nbsp;</a>
## Como clonar formulários

…
