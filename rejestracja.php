<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/style.css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="js/bootstrap.min.js"></script>
    <title>Rumcajs / Rejestracja </title>
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
                <li><a href="">Nie zalogowano</a></li>
                <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Zaloguj się</a></li>
                <li><a href="rejestracja.php"><span class="glyphicon glyphicon-log-in"></span> Zarejestruj się</a></li>
            </ul>
        </div>
    </div>
  </nav>

  <?php 
        session_start();
        if (isset($_SESSION['login'])) { 
            header("Location: index.php");
        }
    ?>
    <?php
        // define variables and set to empty values
        $pass = $email = "";
        $komunikat = null;
        $pomyslnie = null;

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["imie"])) {
                $komunikat .= "Imię jest wymagane<br>";
            } else {
                $imie = test_input($_POST["imie"]);
            }
            if (empty($_POST["nazwisko"])) {
                $komunikat .= "Nazwisko jest wymagane<br>";
            } else {
                $nazwisko = test_input($_POST["nazwisko"]);
            }
            if (empty($_POST["email"])) {
                $komunikat .= "Email jest wymagany<br>";
            } else {
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $komunikat = "Proszę sprawdzić poprawność e-maila<br>";
                }
            }
            if (empty($_POST["haslo"])) {
                $komunikat .= "Hasło jest wymagane<br>";
            } else {
                $haslo = $_POST["haslo"];
                if (strlen($haslo) <= 7 || !preg_match('@[A-Z]@', $haslo) || !preg_match('@[0-9]@', $haslo) || !preg_match('@[^\w]@', $haslo)) {
                    $komunikat .= "Hasło musi mieć minimum 8 znaków, jedną wielką literę, jeden znak specjalny oraz jedną cyfrę<br>";
                    $_POST['wynikAkcji'] = "2";
                }
            }
            if ($komunikat == null) {
                try {
                    require_once("config.php");
                    $plec = "niezdefiniowano";
                    $conn = mysqli_connect($host, $user, $pass, $db);
                    $sha1Haslo = sha1($haslo);
                    $zapytanie = "SELECT email FROM uzytkownicy WHERE email = '$email'";
                    $zapytanie2 = "INSERT INTO uzytkownicy VALUES (NULL, '$imie', '$nazwisko', '$email', '$sha1Haslo', 'TAK', 'Nigdy', 'Nigdy', '$plec', '0000-00-00', 'uzytkownik', '" .  date("Y-m-d H:i:s") . "')";
                    $wynik = mysqli_query($conn, $zapytanie);
                    $rekord = mysqli_fetch_assoc($wynik);

                    if (isset($rekord['email']) && $rekord['email'] == $email) {
                        $komunikat .= "Konto na ten adres e-mail już istnieje."; 
                        $_POST['wynikAkcji'] = "3";
                    } else {
                        $wynik2 = mysqli_query($conn, $zapytanie2);
                        $komunikat = "Pomyślnie utworzono konto! Przekierowanie za 3 sekundy...<br>";
                        echo "<script>setTimeout(\"location.href=\'login.php\';\",3000)</script>";
                        $_POST['wynikAkcji'] = "1";
                    }
                    mysqli_close($conn);

                } catch (Exception $e) {
                    echo "Operacja nieudana: " . $e -> getMessage() . "";
                }
            } else {
                $_POST['wynikAkcji'] = "2";
            }
        }
    ?>

    <div id="naglowek">
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Rejestracja</h3>
    </div>
    <section id="komunikat">

    <?php 
        if (isset($_SESSION['wylogowano']) && $_SESSION['wylogowano'] == "wylogowano") { 
            echo "<div class=\"alert alert-success\"><span class=\"glyphicon glyphicon-ok\"></span>
            <strong>Gratulacje!<br> </strong> Pomyślnie wylogowano. </div>";
            unset($_SESSION['wylogowano']);
        }
    ?>
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
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        <table class="table-responsive" id="formularzTabela" style="margin: auto; border: '1px solid gray'; width: 30%;">
            <tr>
                <td>
                    <label for="imie">Imię: </label>
                </td>
                <td>
                    <input class="form-control" type="imie" autocomplete="off" value="<?php echo isset($_POST['imie']) ? $_POST['imie'] : '' ?>" id="imie" name="imie" placeholder="Podaj swoje imię...">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="nazwisko">Nazwisko: </label>
                </td>
                <td>
                    <input class="form-control" type="nazwisko" autocomplete="off" value="<?php echo isset($_POST['nazwisko']) ? $_POST['nazwisko'] : '' ?>" id="nazwisko" name="nazwisko" placeholder="Podaj swoje nazwisko...">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="email">Adres e-mail: </label>
                </td>
                <td>
                    <input class="form-control" type="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" id="email" name="email" placeholder="Podaj adres e-mail...">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="haslo">Hasło: </label>
                </td>
                <td>
                    <input class="form-control" type="password" id="haslo" name="haslo" placeholder="Podaj hasło...">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="pokazHaslo">Pokaż hasło: </label>
                </td>
                <td align="left">
                <input class="form-control" style="width: 20px; height: 20px;" type="checkbox" id="pokazHaslo" onclick="wyswietlHaslo()">

                </td>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Utwórz konto" name="submit">
                </td>
            </tr>
        </form>
        </table>
        <p style="margin-top: 10px;">Posiadasz konto?</p> 
        <a href="login.php"> 
            <button type="button" id="rejestracja">Zaloguj się</button> 
        </a>
    </section>
    <hr>
    <section id="reklama">
        <img width="400px" height="100px" src="img/reklama.jpg">
    </section>
    <section id="stopka">
    <?php 
        require_once("stopka.php");
    ?>
    </section>
    <script type="text/javascript">
        function wyswietlHaslo() {
            var haslo = document.getElementById("haslo");
            if (haslo.type === "password") {
                haslo.type = "text";
            } else {
                haslo.type = "password";
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>
</html>
</body>
</html>


