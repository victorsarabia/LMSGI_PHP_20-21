<?php
// Recogida de parámetros
$dni = isset($_POST['dni'])? $_POST['dni']:null;
$nombre = isset($_POST['nombre'])? $_POST['nombre']:null;;
$apellido1 = isset($_POST['apellido_1'])? $_POST['apellido_1']:null;
$submit_insert = isset($_POST['insert'])? true:false;

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

// Operación de inserción
if ($submit_insert && !empty($dni)) {
    $host='localhost';
    $dbname='universidad';
    $user='root';
    $pass='';

    try {
        # MySQL con PDO_MYSQL
        # Para que la conexion al mysql utilice las collation UTF-8 añadir charset=utf8 al string de la conexion.
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

        # Para que genere excepciones a la hora de reportar errores.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'INSERT INTO alumno (dni, nombre, apellido_1) values (:dni, :nombre, :apellido_1)';
        $values = [
            ":dni" => $dni,
            ":nombre" => $nombre,
            ":apellido_1" => $apellido1
        ];
        $stmt = $pdo->prepare($sql);
        $is_insert = $stmt->execute($values);

        if ($is_insert) {
            echo "Alumno insertado correctamente.";
        } else {
            echo "Error al insertar el usuario.";
        }

    } catch(PDOException $e) {
        echo $e->getMessage();

        $stmt = null;
        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insert Alumno</title>
</head>
<body>

<h1>Formulario alumno</h1>
<form method="post" action="insert.php">
    <label for="dni">DNI:</label>
    <input type="text" name="dni" value="<?php echo $dni?>">
    <br>

    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" pattern="[a-zA-Z]{1,20}" value="<?php echo $nombre?>">
    <br>

    <label for="apellido_1">Apellido 1:</label>
    <input type="text" name="apellido_1" pattern="[a-zA-Z]{1,20}" value="<?php echo $apellido1?>">
    <br><br>

    <input type="submit" value="Enviar" name="insert">
</form>

</body>
</html>

