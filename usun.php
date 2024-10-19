<?php 
if (isset($_SESSION['login'])) {
    try {
        require_once('config.php');
        $conn = mysqli_connect($host, $user, $pass, $db);
        if (isset($_POST['usunKonto'])) {
            $zapytanie3 = "UPDATE uzytkownicy SET banned = 'Zablokowano' WHERE userid = '" . $_POST['usunKonto'] . "'";
            $wynik3 = mysqli_query($conn, $zapytanie3);
        }
        if (isset($_POST['odblokujKonto'])) {
            $zapytanie3 = "UPDATE uzytkownicy SET banned = 'Nigdy' WHERE userid = '" . $_POST['odblokujKonto'] . "'";
            $wynik3 = mysqli_query($conn, $zapytanie3);
        }
        if (isset($_POST['usunADM'])) {
            $zapytanie3 = "UPDATE uzytkownicy SET rola = 'uzytkownik' WHERE userid = '" . $_POST['usunADM'] . "'";
            $wynik3 = mysqli_query($conn, $zapytanie3);
        }
        if (isset($_POST['nadajADM'])) {
            $zapytanie3 = "UPDATE uzytkownicy SET rola = 'administrator' WHERE userid = '" . $_POST['nadajADM'] . "'";
            $wynik3 = mysqli_query($conn, $zapytanie3);
        }
        if (isset($_POST['usunPOST'])) {
            $zapytanie3 = "DELETE FROM posty WHERE id = '" . $_POST['usunPOST'] . "'";
            $wynik3 = mysqli_query($conn, $zapytanie3);
            echo "<div class=\"alert alert-success\"><span class=\"glyphicon glyphicon-ok\"></span>
            <strong>Gratulacje!<br> </strong> Pomyślnie usunięto post o ID " . $_POST['usunPOST'] . " </div>";
        }
        mysqli_close($conn);
    } catch (Exception $e) {
        echo "Operacja nieudana: " . $e -> getMessage() . "";
    }
} 
?>