<?php 
    session_start();
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");  
        exit;
    } else if (!isset($_POST['edytujPOST'])) {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/style.css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="js/bootstrap.min.js"></script>
    <title>Rumcajs / Edytuj post </title>
</head>
<body>
<nav class="navbar navbar-inverse" style="margin-bottom: 0;">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Rumcajs</a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?userid=<?php echo $_SESSION['logid']; ?>" title="Zobacz profil"><span class="glyphicon glyphicon-user"></span> <?php if ($_SESSION['rola'] == 'administrator') { echo "<font style='background-color: #880000;'>(ADMIN)</font> "; } echo $_SESSION['login']; ?></a></li>
                <li><a href="index.php" title="Strona główna"><span class="glyphicon glyphicon-home"></span> Strona główna</a></li>
                <li><a href="zarzadzaj.php" title="Zarządzaj kontem"><span class="glyphicon glyphicon-cog"></span> Zarządzaj kontem</a></li>
                <?php if ($_SESSION['rola'] == 'administrator') { echo "<li><a href='panelAdministratora.php' title='Panel administratora'><span class='glyphicon glyphicon-lock'></span> Panel administratora</a></li>"; } ?>
                <li><a href="wyloguj.php"><span class="	glyphicon glyphicon-off"></span> Wyloguj się</a></li>
        </div>
    </div>
  </nav>




    <div id="naglowek">
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Edytuj Post</h3>
    </div>

    <?php 
        try {
            require_once("config.php");
            $conn = mysqli_connect($host, $user, $pass, $db);
            $zapytanie = "SELECT * FROM posty WHERE id = '" . $_POST['edytujPOST'] . "'";
            $wynik = mysqli_query($conn, $zapytanie);
            $rekord = mysqli_fetch_assoc($wynik);
            $tytul = $rekord['tytul'];
            $opis = $rekord['tekst'];
            mysqli_close($conn);
            } catch (Exception $e) {
                echo "Operacja nieudana: " . $e -> getMessage() . "";
            }
    ?>

    <?php
        $komunikat = null;
        $pomyslnie = null;

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tytul"])) {
            if (empty($_POST["tytul"])) {
                $komunikat .= "Tytuł jest wymagany<br>";
            } else {
                $tytul = test_input($_POST["tytul"]);
            }
            if (empty($_POST["opis"])) {
                $komunikat .= "Opis jest wymagany<br>";
            } else {
                $opis = test_input($_POST["opis"]);
            }
            
            if ($komunikat == null) {
                try {
                    require_once("config.php");
                    $conn = mysqli_connect($host, $user, $pass, $db);
                    $zapytanie = "UPDATE posty SET tekst = '" . $opis . "', tytul = '" . $tytul . "', edytowano = '" . date("Y-m-d H:i:s") . "' WHERE id = '" . $_POST['edytujPOST'] . "'";
                    $wynik = mysqli_query($conn, $zapytanie);
                    $komunikat = "Pomyślnie zedytowano post! Przekierowanie na stronę główną...<br>";
                    echo "<script>setTimeout(\"location.href=\'index.php\';\",2000)</script>";
                    $_POST['wynikAkcji'] = "1";
                    mysqli_close($conn);

                } catch (Exception $e) {
                    echo "Operacja nieudana: " . $e -> getMessage() . "";
                }
            } else {
                $_POST['wynikAkcji'] = "2";
            }
        } 
    ?>


    <section id="komunikat">
    <?php 
    if (isset($_POST['wynikAkcji'])) {
        if ($_POST['wynikAkcji'] == "1") {
            echo "<div class=\"alert alert-success\"><span class=\"glyphicon glyphicon-ok\"></span>
            <strong>Gratulacje!<br> </strong> $komunikat </div>";
        } else if ($_POST['wynikAkcji'] == "2") {
            echo "<div class=\"alert alert-warning\"><span class=\"glyphicon glyphicon-warning-sign\"></span>
            <strong>
            Uwaga! <br></strong> $komunikat </div>";
        } else if ($_POST['wynikAkcji'] == "3") {
            echo "<div class=\"alert alert-danger\"><span class=\"glyphicon glyphicon-remove\"></span>
            <strong>Wystąpił błąd: <br></strong> $komunikat </div>";
        }
    }
    ?>
    </section>
    <section id="formularz">
    <?php 
    // Dane do formularza
        try {
            require_once('config.php');
            $conn = mysqli_connect($host, $user, $pass, $db);
            $zapytanie = "SELECT * FROM posty, uzytkownicy WHERE posty.id = '" . $_POST['edytujPOST'] . "' AND uzytkownicy.email = posty.autor;";
            $wynik = mysqli_query($conn, $zapytanie);
            $rekord = mysqli_fetch_assoc($wynik);
            $imie = $rekord['imie'];
            $nazwisko = $rekord['nazwisko'];
            $email = $rekord['email'];
            $rekord = mysqli_fetch_assoc($wynik);
            mysqli_close($conn);
        } catch (Exception $e) {
            echo "Wystąpił błąd: " . $e->getMessage();
        }
    ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">  
        <table class="table-responsive" id="formularzTabela" style="margin: auto; border: '1px solid gray'; width: 30%;">
            <tr>
                <td colspan="2">
                    <label>Imię i nazwisko: </label>
                    <?php echo ucFirst($imie) . " ". ucFirst($nazwisko); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label>E-mail: </label>
                    <?php echo $email ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="tytul">Tytuł posta (max 40 znaków): </label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input style="width: 100%" class="form-control" type="text" maxlength="40" value="<?php echo isset($tytul) ? $tytul : '' ?>" id="tytul" name="tytul" placeholder="Wpisz tytuł posta...">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opis">Treść (max 1000 znaków): </label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea rows="7" maxlength="1000" class="form-control" type="text" id="opis" name="opis" placeholder="Wpisz treść posta..."><?php echo isset($opis) ? $opis : '' ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="obraz">Zmiana obrazu jest niedozwolona </label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type='text' name='edytujPOST' value='<?php echo $_POST['edytujPOST'] ?>' hidden>
                    <input type="submit" value="Edytuj post" name="submit">
                </td>
            </tr>
        </form>
        </table>
    </section>
    <section id="stopka">
    <?php 
        require_once("stopka.php");
    ?>
    </section>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>
</html>
</body>
</html>
