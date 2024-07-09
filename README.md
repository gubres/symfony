CRUD SYMFONY

Una webapp desarrollada para practicar CRUD.

PASO A PASO

    Clonar la rama FAMOSOS del proyecto .
    Instalar Dependencias del proyecto Abre una terminal y navega al directorio donde has clonado el repositorio. Una vez dentro del directorio del proyecto, ejecuta: composer install Este comando instalará todas las dependencias necesarias definidas en el archivo composer.json del proyecto.
    Confirma que tienes el servicio de MySQL ejecutando en vuestro ordenador.
    En el archivo .env esta definido como debe ser la conexion a la base de datos, igual que en el archivo .env.local (si no lo tienes basta con hacer un cp .env .env.local y tendras el archivo)
    Crear la Base de Datos: Crea la base de datos que el proyecto usará, basándote en las configuraciones definidas en el archivo .env. Para eso basta usar el comando php bin/console doctrine:schema:create Si el comando anterior fallar, eso quiere decir que teneis que ir a vuestro gestor de Base de Datos y crear una base de datos llamada saludata, manualmente.
    Despues teneis que ejecutar ese comando: "php bin/console make:migration" y después "php bin/console doctrine:migrations:migrate" 6.1. Si por alguna razón teneis algun error con los dos comandos anteriores, podeis usar el siguiente: php bin/console doctrine:schema:update --force eso en teoria irá formar la migration y actualizar la base de datos.
    Si surge algun ERROR en el paso anterior: ir a la carpeta migrations y borrar el archivo que hay de migraciones (Version2024 ....extension php). Si no ha habito ningun error, saltar esa parte.
    Hecho eso, comprobar que se ha creado la base de datos y sus tablas
    Cargar en la base de datos el usuario que esta creado en la fixture usando el comando: php bin/console doctrine:fixtures:load --no-interaction (para saber los datos de login basta acceder a appfixtures en los archivos)
    Si todo correcto, ejecutar el servidor: symfony server:start
    Abrir el navegador en la direccion que informa la consola: 127.0.0.1:8000
