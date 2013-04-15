Sumário
=======

- [Introdução](#intro)
- [Cabeçalho do Arquivo](#head)
- [Tipos de Formulários](#form-types)
    - [Inserção (insert)](#save-form)
    - [Atualização (update)](#save-form)
    - [Listagem (list)](#list-form)
    - [Exclusão (delete)](#delete-form)
    - [Formulário para Rest API (bridge)](#rest-form)
- ???
- [como clonar formulários](#clone-form)


<a id="intro"></a>
Formulários
===========

Os formulários são configurados por um arquivo YAML que de preferencia deve ser gravado na pasta "system/backend/modules/seu modulo/forms/arquivo.yml_", este único arquivo deve conter a configuração para os seguintes formulários:

- [Formulário de Inserção](#save-form)
- [Formulário de Atualização](#save-form)
- [Formulário para telas de listagem (list)](#list-form)
- [Formulário de exclusão (delete)](#delete-form)
- [E formulário para Rest API (bridge)](#rest-form)

<a id="save-form"></a>
## Cabeçalho

Este é responsavel por informar qual é o título principal dos formulários, além de descrever qual é a tabela e chave primária do banco de dados que seram utilizados por estes formulários, segue exemplo de cabeçalho do arquivo YAML:

    header:
        title: Nome pricipal de seus formulários
        table: nome_da_tabela_no_banco_de_dados
        p-key: nome_da_chave_primaria_da_tabela

<a id="save-form"></a>
Tipos de Formulários
====================

Existem diversos tipos de formulários cada um com uma funcionalidade específica… … 

<a id="save-form"></a>
## Formulário de Inserção ou Atualização

Os dois formulários são criados exatamente iguais, portanto esta descrição funciona tanto pra um quanto pra outro diferenciando apenas no fato que a chave para referencia para configuração são distintas.

Estes formulários possuem três propriedades, são elas:

- title
    - refere-se ao titulo secundario do formuláio, para uma descrição pouco mais aprimorada
- input
    - é responsavel de informar quais os tipos e as propriedades dos tipos o formulário ira conter, para intender melhor o que são e como funcionam estes tipos leia [a documentação de tipos](types.md).
- merge-form
    - não é uma propriedade obrigatória, é utilizado quando existe a necessidade de copiar os inputs de um outro formulário, isso é útil quando temos dois formulários que irão utilizar exatamente os mesmo ou grande maiorias das propriedades de outro formulário, veja mais informações em [como clonar formulários](#clone-form).

    forms:
      insert:
        title: TÍTULO-MAIS-ESPECIFICO-PARA-O-FORMULÁRIO
        input:
        
          - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
            label: ETIQUETA-PARA-ESTE-INPUT
            column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
            parameter : 
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
          - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
            label: ETIQUETA-PARA-ESTE-INPUT
            column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
            parameter : 
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
          - ...
              
<a id="list-form"></a>
## Formulário de Listagem
 
O formulário de listagem é exatamente igual ao formulário de inserção e ao formulário de atualização, exceto pelo fato de possuir uma propriedade extra, utilizada especificamente para informar quantos itens serão apresentados em cada página listagem, esta propriedade não é obrigatória, porém caso não informada o sistema passará a considerar o valor informados no arquivo de configuração config.uni.php na propriedade backend/rows-per-page.

Veja um exemplo de como este trecho do formulário pode ser:

    forms:
      list:
        title: TÍTULO-MAIS-ESPECIFICO-PARA-O-FORMULÁRIO
        rows-per-page: 200
        input:
        
          - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
            label: ETIQUETA-PARA-ESTE-INPUT
            column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
            parameter : 
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
          - type: TIPO-ESPECIFICO-PARA-ESTE-INPUT
            label: ETIQUETA-PARA-ESTE-INPUT
            column: COLUNA-NO-BANCO-DE-DADOS-A-QUAL-ESSE-INPUT-IRÁ-REFERIR-SE
            parameter : 
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              PARAMETRO-PARA-O-TIPO : VALOR-PARA-O-PARAMETRO
              
          - ...

<a id="delete-form"></a>
## Formulário de Exclusão

O formulário de exclusão funciona exatamente como os formulários de edição e inserção, porém com o diferencial que neste formulário os tipos (inputs) não são usados para entrada de dados, eles são utilizados apenas para validar se os dados que serão apagados realmente podem ser eliminados ou executar alguma ação específica antes ou após a exclusão. 

Veja um exemplo de copo pode ser o trecho deste tipo de formulário no arquivo YAML:


<a id="rest-form"></a>
## Formulário Rest API

…

<a id="clone-form"></a>
## Como clonar formulários

…
