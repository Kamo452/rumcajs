<?php 
    session_start();
    if (!isset($_SESSION['login'])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    } else {
        require_once('usun.php');
        if ($_SESSION['rola'] != "administrator" ) {
            header("HTTP/1.1 403 Forbidden");
            exit;
        }

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
    <title>Rumcajs / Panel Administratora </title>
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
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Panel Administratora</h3>
    </div>
    <section id="komunikat">

    <?php 
        require_once('usun.php');
    ?>
    </section>
    <section id="posty">
    <div id="uzytkownicyBok" align="center">
        <?php
            try {
                require_once('config.php');
                $conn = mysqli_connect($host, $user, $pass, $db);

                $zapytanie = "SELECT * FROM uzytkownicy";
                $wynik = mysqli_query($conn, $zapytanie);
                $rekord = mysqli_fetch_assoc($wynik);
                if (mysqli_num_rows($wynik) == 0) {
                    echo "Brak użytkowników";
                } else {
                echo "<table class='table table-striped' align='center'>";
                echo "<thead><tr><th scope=\"col\">ID</th><th scope=\"col\">Imię i nazwisko</th><th scope=\"col\">E-mail</th>";
                echo "<th scope=\"col\">Ostatnie logowanie</th><th scope=\"col\">Rola</th><th scope=\"col\">Data rejestracji</th><th scope=\"col\">Operacje</th></thead>";
                while ($rekord != null) {
                    echo "<tr><td>" . $rekord['userid'] . "</td><td>" . $rekord['imie'] . " " . $rekord['nazwisko'] . "</td><td>" . $rekord['email'] . "</td>";
                    echo "<td>" . $rekord['ostatniaAktywnosc'] . "</td><td>" . $rekord['rola'] . "</td><td>" . $rekord['dataRejestracji'] . "</td><td>";
                    if ($FORCEADM != $rekord['userid']) {
                        if ($rekord['rola'] != "administrator") {

                            if ($rekord['banned'] == "Nigdy") {
                                echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz zablokować to konto?')\" action='" . htmlspecialchars('panelAdministratora.php') . "'>";
                                echo "<input type='text' name='usunKonto' value='" . $rekord['userid'] . "' hidden>";
                                echo "<input type='submit' style='background-color: red;' value='ZABLOKUJ KONTO'></form>";
                            } else {
                                echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz odblokkować to konto?')\" action='" . htmlspecialchars('panelAdministratora.php') . "'>";
                                echo "<input type='text' name='odblokujKonto' value='" . $rekord['userid'] . "' hidden>";
                                echo "<input type='submit' style='background-color: lime; color: black;' value='ODBLOKUJ KONTO'></form>";
                            }
                            echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz nadać uprawnienia administratora?')\" action='" . htmlspecialchars('panelAdministratora.php') . "'>";
                            echo "<input type='text' name='nadajADM' value='" . $rekord['userid'] . "' hidden>";
                            echo "<input type='submit' style='background-color: blue;' value='NADAJ UPRAWNIENIA'></form>";
                        } else {
                            echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz odebrać uprawnienia administratora?')\" action='" . htmlspecialchars('panelAdministratora.php') . "'>";
                            echo "<input type='text' name='usunADM' value='" . $rekord['userid'] . "' hidden>";
                            echo "<input type='submit' style='background-color: red;' value='ODBIERZ UPRAWNIENIA'></form>";
                        }
                    } else {
                        echo "GŁÓWNY ADMINISTRATOR (config.php)";
                    }
                    echo "</td></tr>";
                    $rekord = mysqli_fetch_assoc($wynik);
                }
                }
                echo "</table>";
            } catch (Exception $e) {
                echo "Operacja nieudana: " . $e -> getMessage() . "";
            }
        ?>
    </section>
    <section id="reklama">
        <img width="400px" height="100px" src="img/reklama.jpg">
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