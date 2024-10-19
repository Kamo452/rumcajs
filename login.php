<?php 
    session_start();
    if (isset($_SESSION['login'])) { 
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
    <title>Rumcajs / Logowanie </title>
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
            if (empty($_POST["email"])) {
                $komunikat .= "Email jest wymagany<br>";
            } else {
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $komunikat .= "Proszę sprawdzić poprawność e-maila<br>";
                }
            }
            if (empty($_POST["haslo"])) {
                $komunikat .= "Hasło jest wymagane<br>";
            } else {
                $haslo = test_input($_POST["haslo"]);
                if (strlen($haslo) <= 7) {
                    $komunikat .= "Hasło musi mieć minimum 8 znaków<br>";
                }
            }
            if ($komunikat == null) {
                require_once("config.php");
                try {
                    $conn = mysqli_connect($host, $user, $pass, $db);
                    $zapytanie = "SELECT * FROM uzytkownicy WHERE email = '" . $email . "'";
                    $zapytanie2 = "UPDATE uzytkownicy SET ostatniaAktywnosc = '" . date("Y-m-d H:i:s") . "' WHERE email = '" . $email . "'";
                    $wynik = mysqli_query($conn, $zapytanie);
                    $rekord = mysqli_fetch_assoc($wynik);

                    if ($rekord == null) {
                        $komunikat .= "Nieprawidłowe dane logowania.<br>Nie pamiętasz hasła? <br>Skontaktuj się z administratorem";
                        $_POST['wynikAkcji'] = "3";
                    } else {
                        if(isset($rekord['haslo']) && $rekord['haslo'] != sha1($haslo)) {
                            $komunikat .= "Nieprawidłowe dane logowania.<br>Nie pamiętasz hasła? <br>Skontaktuj się z administratorem";
                            $_POST['wynikAkcji'] = "3";
                        } else {
                            if ($rekord['banned'] != "Nigdy") {
                                $_POST['wynikAkcji'] = "3";
                                $komunikat .= "Twoje konto zostało zablokowane.<br>";
                            } else {
                                $wynik = mysqli_query($conn, $zapytanie2);
                                $_SESSION['rola'] = $rekord['rola']; 
                                $_SESSION['logid'] = $rekord['userid'];
                                $komunikat = "Pomyślnie zalogowano. Przekierowanie za 3 sekundy...<br>";
                                $_POST['wynikAkcji'] = "1";
                                echo "<script>setTimeout(\"location.href=\'index.php\';\",3000)</script>";
                                $_SESSION['login'] = "$email";
                            }
                        }
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
        <img src="img/logo.png" width="64px" height="64px" ><h3>Rumcajs / Logowanie</h3>
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
    <section>
    <div id="formularz">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        <table class="table-responsive" id="formularzTabela" style="margin: auto; border: '1px solid gray'; width: 30%;">
                <tr>
                    <td colspan="2">
                        <label name for="email">Adres e-mail: </label>

                    <input class="form-control" type="email" autocomplete="off" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" id="email" name="email" placeholder="Podaj adres e-mail...">
                        <font style="color: red;"><?php echo isset($_POST['bladImKier']) ? htmlspecialchars($_POST['bladImKier']) : "" ?> </font>
                    </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="haslo">Hasło: </label>

                    <input class="form-control" type="password" id="haslo" name="haslo" placeholder="Podaj hasło...">
                </td>
            </tr>
            <tr>
                <td align="right" style="width: '30%';">
                    <label for="pokazHaslo">Pokaż hasło: &nbsp</label>
                </td>
                <td align="left">
                    <input class="form-control" style="width: 20px; height: 20px;" type="checkbox" id="pokazHaslo" onclick="wyswietlHaslo()">
                </td>
</tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Zaloguj" name="submit">
                </td>
            </tr>
        </form>
        </table>
        <p style="margin-top: 10px;">Nie posiadasz konta?</p> 
        <a href="rejestracja.php"> 
            <button type="button" id="rejestracja">Zarejestruj się</button> 
        </a>
    </div>
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