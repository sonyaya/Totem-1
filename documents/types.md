Sumário
=======

- [Introdução](#intro)
- [Cabeçalho do Arquivo](#head)
- Tipos de Formulários
    - [Inserção (insert)](#save-form)
    - [Atualização (update)](#save-form)
    - [Listagem (list)](#list-form)
    - [Exclusão (delete)](#list-form)
    - Formulário para Rest API (bridge)
- Eventos de Formulários
- [como clonar formulários](#clone-form)


<a id="intro"></a>
Formulários
===========

Os formulários são configurados por um arquivo YAML que de preferencia deve ser gravado na pasta "system/backend/modules/seu modulo/forms/arquivo.yml_", este único arquivo deve conter a configuração para os seguintes formulários:

- [Formulário de Inserção](#save-form)
- [Formulário de Atualização](#save-form)
- Formulário para telas de listagem
- Formulário de exclusão
- E formulário para Rest API (bridge)

<a id="save-form"></a>
Cabeçalho
=========

Este é responsavel por informar qual é o título principal dos formulários, além de descrever qual é a tabela e chave primária do banco de dados que seram utilizados por estes formulários, segue exemplo de cabeçalho do arquivo YAML:

    header:
        title: Nome pricipal de seus formulários
        table: nome_da_tabela_no_banco_de_dados
        p-key: nome_da_chave_primaria_da_tabela

Tipos de Formulários
====================

Existem diversos tipos de formulários … … 

<a id="save-form"></a>
## Formulário de Inserção ou Atualização

Os dois formulários, o formulário de inserção e o formulário de atualização, são criados exatamente iguais, portanto esta descrição funciona tanto pra um quanto pra outro diferenciando apenas no fato que a chave para referência para configuração são distintas; o formulário de inserção fica em *forms/insert*, já o formulário de atualização fica em *forms/update*.

Como os nomes destes formulários já sugerem, o formulário de inserção é utilizado para inserir novas informações no banco de dados e o formulário de atualização é utilizado para modificar informações previamentes cadastradas no banco de dados.

Estes formulários possuem três propriedades, são elas:

- title
  - refere-se ao título secundario do formuláio, para uma descrição pouco mais aprimorada
- input
  - é responsavel de informar quais os tipos e as propriedades dos tipos o formulário ira conter, para intender melhor o que são e como funcionam estes tipos leia a [documentação de tipos](types.mk).
- merge-form
  - não é uma propriedade obrigatória, é utilizado quando existe a necessidade de copiar os inputs de um outro formulário, isso é útil quando temos dois formulários que irão utilizar exatamente os mesmo ou grande maiorias das propriedades de outro formulário, veja mais informações em [como clonar formulários](#clone-form).
    
Veja um exemplo de como pode ser este trecho do arquivo YAML:

    forms:
        insert:
          title: Título um pouco mais especifico
          input:
            - type: um_tipo_qualquer
              label: Etiqueta deste tipo
              column: coluna_de_referencia_para_o_banco_de_dados
              parameter : 
                param01: parametro_de_configuração_do_tipo
                param02: parametro_de_configuração_do_tipo

            - type: um_tipo_qualquer
              label: Etiqueta deste tipo
              column: coluna_de_referencia_para_o_banco_de_dados
              parameter : 
                param01: parametro_de_configuração_do_tipo
                param02: parametro_de_configuração_do_tipo
            
<a id="list-form"></a>
## Formulário de Listagem

Os formulários de listage seguem a mesma lógica dos formulários de inserção e atualização porém com um único item a mais, que trata-se da propriedade *row-per-page*, que é utilizada para informar qual a quantidade máxima de iténs que serão exibidos pora página, vale resaltar que este campo não é obrigatório e que em caso de omissão do mesmo o sistema ira considerar a quantidade por pagina informada no arquivo *config.ini.php* na propriedade *backend/rows-per-page*.

<a id="delete-form"></a>
## Formulário de Exclusão

