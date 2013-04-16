<a name="summary" id="summary"></a>
Sumário
=======

1. [Introdução](#intro)
2. [Criando um tipo personalizado](#creating)


<a name="intro" id="intro"></a>
1. Tipos de Inputs
==================

[▲](#summary) *Tipos*, *types*, *tipos de inputs* ou até mesmo *inputs* são possíveis nomeclaturas para apresentar a base de qualquer formulário do módulo *backend*, estes objetos são reponsaveis por criar a aparencia e funcionamento para entrada de dados no sistema, criar validadores de interface, ajax e server side além de tratar os valores que são inseridos, editados ou mesmo excluidos do banco de dados.

É possivel criar os mais diversos *tipos de inputs*, seja uma simples entrada de texto até mesmo um relacionamento entre duas ou mais tabélas, por padrão o **totem** possui os seguintes tipos:

- ### CKEditor
    - Adiciona um tipo baseado na biblioteca [CKEditor](http://ckeditor.com/).
    - Parâmetros
        - …

- ### combobox
    - Cria um objeto com a tag select do HTML, ele permite que o usuário decida quais serão os valores inseridos no banco de dados e as etiquetas que são apresentadas para o usuário.
    - Parâmetros
        - **valor-1 : etiqueta-1**
        - **valor-2 : etiqueta-2**
        - **valor-3 : etiqueta-3**
        - *adicione quantos valores achar necessário*

- ### dateBr
    - Cria três objetos do tipo select do HTML, o primeiro com os anos, o segundo com os meses e o terceiro com os dias. Quando enviado para o banco o formato é o padrão do MySQL (Y-m-d).
    - Parâmetros
        - **nullable** *pode ser nulo?*
        - **year**
            - **start** *ano de inicio da listagem*
            - **stop**  *ano máximo da listagem*

- ### dateTimeBr
    - Parecido com o dateBr com a diferença que o *dateTimeBr* possui selects do HTML a mais, o primeiro é a hora, o segundo refere-se aos minutos e o tereiro aos segundos.
    - Parâmetros
        - **nullable** *pode ser nulo?*
        - **year**
            - **start** *ano de inicio da listagem*
            - **stop**  *ano máximo da listagem*

- ### example
    - É um tipo utilizado para demonstrar como são feitos os tipos.
    - Parâmetros
        - ...

- ### fk
    - Parecido com o combobox, porém neste tipo os valores são buscados de uma tabela no banco de dados, criando assim um relação simples entre as tabelas.
    - Parâmetros
        - **table**       *tabela no banco de dados*
        - **column**      *coluna do banco de dados que será utilizada para os valores do select*
        - **label**       *coluna do banco de dados que erá utilizada para as etiquetas do select*
        - **insert-form** *caminho do formulário para inserção de novos valores*

- ### imagesUpload
    - Permite envio de imagens para o sistema, estas imagens são salvas em um caminho informado nos parâmetros e em uma sub-pasta com o nome do valor da chave primária, além de permitir ordenação e adição de informação diversas na imagem, atente-se que ele insere um jSon no banco de dados.
    - Parâmetros
       - **folder** *caminho da pasta que serão gravadas as imagens*
       - **inputs**
          - **label/chave : \<textarea name="data"\>\</textarea\>** *informação adicional para imagem*
          - **label/chave : \<input name="data"\>**                 *informação adicional para imagem*
          - *adicione quantos valores achar necessário*
- ### jsonArray
    - Cria uma lista ordenada, utilizada para gravar diversos valores em uma única coluna no banco de dados, apropriado para criação de campos do tipo TAG ou lista de telefones.
    - Parâmetros
        - **nullable** *pode ser nulo?*
- ### manyToMany
    - ...
    - Parâmetros
        - **nullable**     *pode ser nulo?
        - **middle-table** *nome da tabela intermediária*
        - **middle-fk**    *coluna chave estrangeira da tabela intermediária*
        - **middle-pk**    *coluna chave primária da tabela intermediária*
        - **right-table**  *nome da tabela da direita*
        - **right-fk**     *coluna chave estrangeira da tabela da direita*
        - **right-label**  *coluna chave primária da tabela da direita*
        - **insert-form**  *caminho do formulário para inserção de novos valores*

- ### meioMask
    - Tipo baseado na famosa mascara jQuery [meioMask](http://www.meiocodigo.com/projects/meiomask/).
    - Parâmetros
        - **nullable**     *pode ser nulo?*
        - **placeholder**  *texto placeholder*
        - **size**         *quantidade máxima de caracteres aceita*
        - **mask**         *mascara para o campo, mascaras preconfiguradas: phone, phone-us, cpf, cnpj, date, date-us, cep, time e cc*

- ### number
    - Campo de entrada que aceita somente números
    - Parâmetros
        - **min**  *valor mínimo aceito*
        - **max**  *valor máximo aceito*
        - **step** *multiplos aceitos, 2 em 2, 3 em 3 etc.*

- ### password
    - Campo de senha com confirmação de senha.

- ### rawText
    - Texto integro (exatamente como foi gravado no banco) somente para visualização, sem permissões para alterar.

- ### textarea
    - Este tipo adiciona um textarea do HTML.
    - Parâmetros
        - **nullable**     *pode ser nulo?*

<a name="intro" id="intro"></a>
1. Tipos de Inputs
==================