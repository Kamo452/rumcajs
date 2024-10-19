<?php 
    session_start();
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");  
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
    <title>Rumcajs / Zarządzaj kontem </title>
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
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Zarządzaj kontem</h3>
    </div>
    <?php 
        require_once('usun.php');
    ?>
    <?php 
        try {
            require_once('config.php');
            $conn = mysqli_connect($host, $user, $pass, $db);
            $zapytanie = "SELECT * FROM uzytkownicy WHERE uzytkownicy.email = '" . $_SESSION['login'] . "'";
            $wynik = mysqli_query($conn, $zapytanie);
            $rekord = mysqli_fetch_assoc($wynik);
            echo "<section id='posty'>";
                echo "<section class='post' style='border: 3px solid "; 
                if ($rekord['rola'] == "uzytkownik") {
                    echo "#006256";
                } else {
                    echo "red";
                }
                echo "'><br>";
                echo "<img width='128px' height='128px' src='img/logoProfil.png'><br><br><h2>";
                echo ucFirst($rekord['imie']) . " " . ucFirst($rekord['nazwisko']) . "</h2>";
                echo "<h5> ID: " . $rekord['userid'] . "</h5>";
                if ($rekord['banned'] != 'Nigdy') {
                    echo "<i style='color: red'>[Konto zostało zablokowane]</i>";
                    header("Location: wyloguj.php");
                    exit;
                }
                echo "<br><b>Adres e-mail:</b> ";
                echo $rekord['email'];
                echo "<br><b>Rola:</b> ";
                echo $rekord['rola'];
                echo "<br><b>Data rejestracji:</b> ";
                echo $rekord['dataRejestracji'];
                echo "<br><b>Ostatnia aktywność:</b> ";
                echo $rekord['ostatniaAktywnosc'];
                echo "<br><b>Płeć:</b> ";
                echo $rekord['plec'];
                echo "<br><b>Data urodzenia:</b> ";
                if ($rekord['dataUrodzenia'] == "0000-00-00") {
                    echo "niezdefiniowano";
                } else {
                    $rekord['dataUrodzenia'];
                }
                if ($FORCEADM != $rekord['userid']) {
                    echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz zablokować swoje konto?')\" action='" . htmlspecialchars('zarzadzaj.php') . "'>";
                    echo "<input type='text' name='usunKonto' value='" . $rekord['userid'] . "' hidden>";
                    echo "<input type='submit' style='background-color: red;' value='ZABLOKUJ KONTO'></form>";

                }
                echo "</section>";
            mysqli_close($conn);
        } catch (Exception $e) {
            echo "Wystąpił błąd: " . $e->getMessage();
        }
    ?>
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