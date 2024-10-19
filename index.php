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
    <title>Rumcajs / Strona Główna </title>
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
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Strona Główna</h3>
    </div>
    <section id="komunikat">

    <?php 
        require_once('usun.php');
    ?>
    </section>
    <section id="przywitanie">
        <h5>
            <?php 
                if (date('H') >= 20 || date('H') < 3) {
                    echo "Dobry wieczór, ";
                } else {
                    echo "Dzień dobry, ";
                }
                try {
                    require_once('config.php');
                    $conn = mysqli_connect($host, $user, $pass, $db);
                    $zapytanie = "SELECT * FROM uzytkownicy WHERE email = '" . $_SESSION['login'] . "'";
                    $rekord = mysqli_query($conn, $zapytanie);
                    $wynik = mysqli_fetch_assoc($rekord);
                    echo ucFirst($wynik['imie']) . ' ' . ucFirst($wynik['nazwisko']);
                    $_SESSION['rola'] = $wynik['rola']; 
                    if ($wynik['banned'] != "Nigdy") {
                        header("Location: wyloguj.php");
                        exit;
                    }
                } catch (Exception $e) { 
                    echo "Wystąpił błąd: " . $e->getMessage();
                }
            ?>
        </h5>
    </section>
    <section id='dodajPost'>
        <a href='dodajPost.php' title="Dodaj post"><button type='button'><span class='glyphicon glyphicon-plus'></span> Dodaj post</button></a>
    </section>
    <?php 
        try {
            require_once('config.php');
            $conn = mysqli_connect($host, $user, $pass, $db);
            $zapytanie = "SELECT * FROM uzytkownicy, posty WHERE posty.autor = uzytkownicy.email ORDER BY posty.id DESC";
            $wynik = mysqli_query($conn, $zapytanie);
            $rekord = mysqli_fetch_assoc($wynik);
            echo "<section id='posty'>";
            while ($rekord != null) {
                echo "<section class='post' style='border: 3px solid "; 
                if ($rekord['rola'] == "uzytkownik") {
                    echo "#006256";
                } else {
                    echo "red";
                }
                echo "'><br>";
                if ($rekord['email'] == $_SESSION['login'] || $_SESSION['rola'] == "administrator") {
                    echo "<table style='width: 100%'><td>";
                    echo "<form method='post' onsubmit=\"return confirm('Czy na pewno chcesz usunąć post? (ID: " . $rekord['id'] . ")')\" action='" . htmlspecialchars('index.php') . "'>";
                    echo "<input type='text' name='usunPOST' value='" . $rekord['id'] . "' hidden>";
                    echo "<button type='submit' style=\"width: 35px; background-color: #ff3636\">";
                    echo "<span class=\"glyphicon glyphicon-trash\"></span>";
                    echo "</button></form>";
                    echo "</td><td>";
                    echo "<h2>" . $rekord['tytul'] . "</h2>";
                    echo "</td><td>";
                    echo "<form method='post' action='" . htmlspecialchars('edytujPost.php') . "'>";
                    echo "<input type='text' name='edytujPOST' value='" . $rekord['id'] . "' hidden>";
                    echo "<button type='submit' style=\"width: 35px; background-color: yellow\">";
                    echo "<span class=\"glyphicon glyphicon-pencil\"></span>";
                    echo "</button></form>";
                    echo "</td></tr></table>";
                } else {
                    echo "<h2>" . $rekord['tytul'] . "</h2>";
                }
                echo "<a href='profile.php?userid=" . $rekord['userid'] . "' title='Zobacz profil'>";
                echo "Autor: <b>";
                if ($rekord['rola'] == 'administrator') { 
                    echo "<font style='background-color: red;'>(ADMIN)</font> "; 
                }
                echo ucFirst($rekord['imie']) . " " . ucFirst($rekord['nazwisko']) . "</b>";
                echo "</a>";
                if ($rekord['banned'] != 'Nigdy') {
                    echo "<br><i style='color: red'>[Konto zostało zablokowane]</i>";
                }
                echo "<br>Czas utworzenia: <b>" . $rekord['czasDodania'] . "</b><br>";
                if ($rekord['edytowano'] != "") {
                    echo "<i>Edytowano: " . $rekord['edytowano'] . "</i>";
                }
                echo "<hr>";
                echo $rekord['tekst'];
                echo "<hr>";
                if ($rekord['obraz'] != "") {
                    echo "<img width='50%' height='25%' src='" . $rekord['obraz'] . "'>"; 
                    echo "<hr>";
                }
                echo "</section>";
                $rekord = mysqli_fetch_assoc($wynik);
            }
            mysqli_close($conn);
        } catch (Exception $e) {
            echo "Wystąpił błąd: " . $e->getMessage();
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