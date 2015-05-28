Componente: IntegratorBundle
==========================


1. Descripción general
========================

Está orientado a exponer y gestionar los recursos REST del sistema que pueden ser configurados en cada bundle.
Garantiza la creación de servicios y dependencias ya sea a partir de entidades de la Base de datos manejadas por Doctrine, como recursos personalizados. Brinda a demás funcionalidades para que el sistema se comporte como un resolver de dependencias y servicios entre un conjunto de sistemas previamente identificados.


2. Especificación funcional
============================

2.1 Requisitos funcionales
------------------------------


2.1.1 Etiquetar entidades como servicios o dependencias.
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	Para etiquetar entidades como servicios o dependencias el componente utiliza las anotaciones del ORM doctrine 2. En la carpeta Annotation se encuentran las clases RestService y RestServiceConsume las cuales constituyen anotaciones y pueden ser utilizadas para etiquetar Servicios y Dependencias Respectivamente.
	Para ello se debe especificar en el bloque de anotaciones de la entidad los parámetros necesarios de la siguiente forma:

	.. code-block:: php

	    ...
	    use UCI\Boson\IntegratorBundle\Annotation\RestService;

	    /**
	     * Foo
	     *
	     * @RestService(name="foo",allow={"POST","GET","PUT","DELETE"},domain="foo_dominio")
 	    */
	    class Foo
	    {
	    ....

    El parámetro name se utiliza para definir el nombre que va a tomar el recurso, si no se especifica obtiene el mismo que el de la entidad.
	El parámetro allow constituye un arreglo de los verbos o métodos http que va a permitir realizarse sobre el recurso. Si se especifica el GET se podrán realizar operación de obtener instancias del recurso, si se especifica POST se podrán crear o registrar nuevas instancias del recurso, si se especifica PUT se podrá modificar una instancia de un recurso y finalmente si se especifica DELETE se podrá eliminar una instancia de un recurso.
	El parámetro domain se utiliza para identificar un recuso en un dominio determinado, pueden existir recursos con el mismo nombre y diferentes dominios, muy importante para que el resolvedor pueda proveer una respuesta más parecida al recurso que se necesita.
	El parámetro version es opcional y se utiliza para identificar con mayor claridad al recurso solicitado, pudieran coexistir varios sistemas sobre el mismo servidor y poseer servicios iguales o diferentes y este podría ser un método de identificación.

	Si se desea exponer una clase como dependencia se debe utilizar la anotación RestServiceConsume de la siguiente forma:

	.. code-block:: php

	    ...
	    use UCI\Boson\IntegratorBundle\Annotation\RestService;

	    /**
	     * Foo
	     *
	     * @RestServiceConsume(name="foo",domain="foo_dominio",version="1.0", optional=true)
	     * @ORM\Entity
 	    */
	    class Foo
	    {
	    ....

    Los parámetros name, domain, y version se utilizan con los mismos fines antes expuestos.
    El parámetro opcional no es obligatorio y posee valor true por defecto, se utiliza para definir si una dependencia es opcional o no para el sistema.

    Si se desea agregar hipermedia a un recurso descrito se debe utilizar la anotación RestRelationField. Como su nombre lo indica esta no se especifica sobre la clase sino sobre el método de la relación inversa entre dos entidades.
    Un ejemplo seria poseer una entidad Foo que posee una relación OneToMany con Foo2, en atributo de relación que se puede especificar en la Clase Foo se puede agregar esta configuración si se desea mostrar los datos de las tuplas de Foo2 relacionadas
	Para ello se especifica la anotación de la siguiente forma:
	.. code-block:: php

	    ...
	    use UCI\Boson\IntegratorBundle\Annotation\RestService;
	    use UCI\Boson\IntegratorBundle\Annotation\RestRelationField;

	    /**
	     * Foo
	     *
	     * @RestServiceConsume(name="foo",domain="foo_dominio",version="1.0", optional=true)
	     * @ORM\Entity
 	    */
	    class Foo
	    {
	    ....
	    /**
	    * @RestRelationField(type="IDENTIFIER")
	    * @ORM\OneToMany(targetEntity="Acme\FooBundle\Entity\Foo2", mappedBy="foo")
	    */
	    private $foo2s;

	Como se observa solo se tiene que especificar el parámetro type de la anotación el cual simboliza la forma en la que se va a proveer la información:

    Si se especifica el type IDENTIFIER la información se obtendrá mostrando solo los identificadores de las tuplas de relación.
    Si se especifica el type OBJECT la información se obtendrá mostrando completamente la información de las tuplas de relación.
    Si se especifica el type HYPERMEDIA la información se obtendrá mostrando las URL a partir de las cuales pueden ser accedidas cada una de las tuplas de relación.

2.1.2 Definir otros recursos personalizados para exponer como servicios.
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	Para exponer otros recursos personalizados los cuales no constituyen entidades se deben utilizar la misma anotación RestService, solo que en este caso el desarrollador es quien debe implementar la lógica de cada uno de los métodos permitidos sobre un recurso o los que desee brindar.
	Para esto el recurso debe extender de la clase Model/AbstractResource y sobrescribir los métodos get, getList, put, post y delete.
	Los recursos que van a ser expuestos deben ser descritos como clases en el directorio Model.


2.1.3 Acceder a los servicios de un sistema:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


2.1.3.1 Acceder a los recursos mediante una petición GET
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    Para acceder a los recursos que brinda un sistema se debe especificar la URL de la siguiente forma:

	Dirección url del servidor/api/dominio del servicio/Nombre del servicio, ejemplo ** http://localhost/domainfoos/foo**
	De esta forma se accede a todos los recursos foo registrados, si se especifica el identificador de objeto se obtendría el recurso con el identificador especificado.
	Otra de las ventajas que brinda para obtener información es mediante los parámetros:

	**start** especifica a partir de que numero de resultados debe empezar a devolver los datos, útil para la paginación.
    **limit** especifica a cuál debe ser la cantidad límite de objetos a obtener del servidor
    **nombre_de_un_campo** especificando el nombre de un campo y el valor se realiza una búsqueda por ese campo y devuelve todos los resultados que coincidan con lo escrito.
    **sortBy unido_a_un_nombre_de_campo** como llave y especificando el tipo como valor (**ASC o DESC**) se realiza un ordenamiento por cada parámetro a ordenar especificando de este forma.

2.1.3.2 Crear nuevos objetos mediante la petición POST:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	 Para crear una nueva instancia de un recurso se debe realizar una petición POST cuyos parámetros deben ser como llaves los nombres de los atributos del servicio y como valor el que se desea especificar.
	 La url especificada debe no debe contener el identificador al final, solo la ruta hasta el recurso, el sistema asigna el identificador de forma secuencial y autogenerado,

2.1.3.3 Modificar recursos mediante la petición PUT:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	 Para modificar una instancia de un recurso se debe realizar una petición PUT cuyos parámetros deben ser como llaves los nombres de los atributos del servicio y como valores los nuevos que se desean especificar.
	 No es necesario definir todos los valores si solo van a ser modificados algunos, pero todos los especificados van a ser modificados. La ruta debe ser la misma hasta el recurso, sin identificador al final. El parámetro del identificador debe ser uno de los parámetros de la petición.

2.1.3.4 Eliminar recursos mediante la petición DELETE:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    Para eliminar una instancia de un recurso se debe realizar una petición DELETE, se debe especificar como parámetros el identificador del recurso teniendo como clave el nombre del identificador en la entidad y como valor un entero identificativo del recurso a eliminar.


2.1.4 Generación de rutas de los recursos:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    El componente genera las rutas necesarias para cada recurso de forma transparente a la acción del usuario, las rutas son generadas una sola vez y agregadas al sistema de rutas que utiliza symfony, mediante la clase Loader/RouterLoader.php, estas son escritas también en el fichero ubicado en Resources/config/rest_global_routing.yml.
    Las rutas comienzan con el prefijo **restRoute_** seguido del nombre del recurso y tienen configuradas los métodos http que se permiten sobre cada una de ellas según la especificación de la anotación de la entidad o clase.


2.2 Requisitos no funcionales
------------------------------

2.2.1 Disponibilidad
---------------------

	La aplicación debe contar con una conexión a alguno de los sistemas gestores de base de datos (preferiblemente postgres o mysql) pre establecido y haberse  generado las tablas de cada uno de los recursos a gestionar.

2.2.2 Eficiencia
-----------------

	El rendimiento del componente y su consiguiente afectación al rendimiento general del sistema en que se utiliza, está condicionado a los recursos de hardware que posea el servidor  donde se despliega el sistema.

2.2.2 Reusabilidad
--------------------

	El componente puede ser utilizado en cualquier sistema implementado sobre versiones de Symfony 2.*.
	Aun no se ha verificado los cambios que pueden ser necesarios para la versión 3 del framework.
	El componente depende del uso de ORM Doctrine2 el cuan viene incluido por defecto para las versiones de Symfony2, si usted utiliza otro ORM no es posible utilizar este componente.

3 Servicios que brinda
------------------------------

    Ver descripción de los siguientes en la clase ServiceRest/RestServicesVerbs.php:
        readAction
        createAction
        updateAction
        deleteAction
    Ver descripción de los siguientes en la clase ServiceRest/RestServicesDiscover.php:
        getApi
        getRutas

4 Servicios de los que depende
--------------------------------

    Depende del siguiente listado de componentes:

        "symfony/symfony": "2.3.*",
        "sensio/distribution-bundle": "2.3.*",
        "sensio/framework-extra-bundle": "2.3.*",
        "sensio/generator-bundle": "2.3.*",
        "friendsofsymfony/user-bundle": "1.3.5",
        "friendsofsymfony/rest-bundle": "0.12",
        "nelmio/api-doc-bundle": "2.2.*",
        "jms/serializer-bundle": "0.12.*"

5 Eventos generados
-----------------------



6 Eventos observados
----------------------


7 Otras características
----------------------

---------------------------------------------


:Versión: 1.0 1/5/2015

:Autores: Félix Iván Romero Rodríguez firomero@uci.cu
:Autores: Daniel Arturo Casals Amat dacasals@uci.cu

Contribuidores
--------------

Entidad
-------
Universidad de las Ciencias Informáticas, Centro CEIGE.

Licencia
---------
