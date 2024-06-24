# Genera comprobante de abono con PHP y MySQL
 
Este proyecto muestra la generación de un comprobante de abono en PDF con información de una base de datos en MySQL.

# Modulos

+ cliente
+ asesor
+ banco
+ operacion
+ comprobante

# Uso

1. Configuración
   + Descarga el proyecto y copia la carpeta en el htdocs o www de tu servidor web.
   + Asegúrate de tener una base de datos MySQL llamada 'test_db' con las tablas y datos adecuados. Puedes importar el archivo `test_db.sql` proporcionado.
   + Abre `db_conn.php` y verifica que los datos de conexión a la base de datos (host, usuario, contraseña y nombre de la base de datos) sean correctos.
   + Inicia tu servidor web y abre index.php en tu navegador. `http://localhost/nombre_de_tu_carpeta/index.php`
