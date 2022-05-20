<?php
require 'auth.inc.php';

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";


$_SESSION['user_id'] = 25;
$_SESSION['user_name'] = "Juan Antonio";

// Ejemplo de conexión a diferentes tipos de bases de datos.
# Conectamos a la base de datos
$host='localhost';
$dbname='universidad';
$user='root';
$pass='';

// Recogida de filtros
$localidad = isset($_POST["localidad"])? $_POST["localidad"]:null;
$nombre = isset($_POST["nombre"])? $_POST["nombre"]:null;
$pagina = isset($_POST["pagina"])? $_POST["pagina"]:1;
$num_registros=15;


try {
    # MySQL con PDO_MYSQL
    # Para que la conexion al mysql utilice las collation UTF-8 añadir charset=utf8 al string de la conexion.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    # Para que genere excepciones a la hora de reportar errores.
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    // Total registros
    $sql_count = 'SELECT count(*) as total from alumno where true';
    $sql_where = "";

    $filters = [];
    if (!empty($localidad)) {
        $sql_where .= " and localidad=:localidad";
        $filters[":localidad"] = $localidad;
    }
    if (!empty($nombre)) {
        $sql_where .= " and nombre like :nombre";
        $filters[":nombre"] = "%".$nombre."%";
    }

    $stmt = $pdo->prepare($sql_count.$sql_where);
    $stmt->execute($filters);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_registros = $row["total"];
    $total_paginas = ceil($total_registros/$num_registros);

    // Paginador
    if (isset($_POST["primera"]) && $pagina>1)
        $pagina = 1;
    if (isset($_POST["anterior"]) && $pagina>1)
        $pagina--;
    if (isset($_POST["siguiente"]) && $pagina<=$total_paginas)
        $pagina++;
    if (isset($_POST["ultima"]) && $pagina<=$total_paginas)
        $pagina = $total_paginas;

    $sql = 'SELECT * from alumno where true';
    $sql .= $sql_where;
    $sql .= " limit ".($pagina-1)*$num_registros.", $num_registros";

    //echo $sql;exit();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filters);


    //$stmt->setFetchMode(PDO::FETCH_ASSOC);
    // $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo "<pre>";
    //print_r($rows);
    //echo "</pre>";

    ?>
    <form method="post" action="ejemploConexion.php">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $nombre?>">

        <label for="localidad">Localidad:</label>
        <input type="text" name="localidad" pattern="[a-z]{,10}" value="<?php echo $localidad?>">

        <input type="submit" value="Buscar" name="buscar">



    <br>

    <table border="1px solid">
        <thead>
            <tr>
                <th>DNI</th>
                <th>NOMBRE</th>
                <th>APELLIDO 1</th>
                <th>LOCALIDAD</th>
            </tr>
        </thead>
        <tbody>
    <?php

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //echo "<pre>";
        //print_r($row);
        //echo "</pre>";
        //echo $row['DNI'] . " - ";
        //echo $row['NOMBRE'] . " - ";
        //echo $row['APELLIDO_1'] . "<br/>";

        echo "<tr>";
        echo "<td>".$row['DNI']."</td>";
        echo "<td>".$row['NOMBRE']."</td>";
        echo "<td>".$row['APELLIDO_1']."</td>";
        echo "<td>".$row['LOCALIDAD']."</td>";
        echo "</tr>";
    }
    ?>
        </tbody>
    </table>
        <input type="submit" name="primera" value="<<">
        <input type="submit" name="anterior" value="<">
        <input type="text" name="pagina" value="<?php echo $pagina ?>">
        <input type="submit" name="siguiente" value=">">
        <input type="submit" name="ultima" value=">>">

    </form>
    <?php

    # Para liberar los recursos utilizados en la consulta SELECT
    $stmt = null;
    $pdo = null;
}
catch(PDOException $e) {
    echo $e->getMessage();

    $stmt = null;
    $pdo = null;
}
