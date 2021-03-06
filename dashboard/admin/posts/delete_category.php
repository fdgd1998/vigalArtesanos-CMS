<?php
    session_start();
    require_once '../../../modules/connection.php';

    // Redirecting to 403 page is session does not exist.
    if (!isset($_SESSION['loggedin'])) {
        header("Location: ../../../../403.php");
        exit();
    }

    if ($_POST) {

        $conn = new mysqli($DB_host, $DB_user, $DB_pass, $DB_name);

        if ($conn->connect_error) {
            echo "No se ha podido conectar a la base de datos.";
            exit();
        } else {
            // checking if there are posts of the category to be deleted
            $stmt = "select count(id) as id from posts where category = ".$_POST["cat_id"];
            if ($conn->query($stmt)->fetch_assoc()['id'] > 0) {
                echo "Existen posts pertenecientes a esta categoría. La categoría no se puede eliminar. Para borrarla, comprueba que no existen posts de dicha categoría e inténtalo de nuevo.";
                exit();
            }
            // getting filename and deleting it
            $stmt = "select image from categories where id = ".$_POST["cat_id"];
            if ($res = $conn->query($stmt)) {
                $rows = $res->fetch_assoc();
                unlink($_SERVER["DOCUMENT_ROOT"]."/uploads/categories/".$rows['image']); // deleting the file
            } else {
                echo "Ha ocurrido un error.";
            }

            // deleting entry from database
            $stmt = "delete from categories where id = ".$_POST['cat_id']."";
            if ($conn->query($stmt) === TRUE) {
                echo "La categoría se ha eliminado correctamente.";
            } else {
                echo "No se ha podido borrar la categoría.";
            }
        }
    }
?>