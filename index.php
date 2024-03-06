<?php
require_once('db.php');
require_once('db_pdo.php');
require_once('user.php');
$config = require_once('config.php');

use DB\DB_PDO as DB;

$PDOConn = DB::getInstance($config);
$conn = $PDOConn->getConnection();

$userDTO = new UserDTO($conn);
$res = $userDTO->getAll();

$isadmin = isset($_REQUEST['isadmin']) ? 1 : 0;

if (isset($_REQUEST['firstname'])) {
    $firstname = $_REQUEST['firstname'];
    $lastname = $_REQUEST['lastname'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $res = $userDTO->saveUser([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => $password,
        'isadmin' => $isadmin
    ]);

    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['firstname'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $isadmin = isset($_POST['adminUp']) ? $_POST['adminUp'] : 0;

        $res = $userDTO->saveUser([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $password,
            'isadmin' => $isadmin
        ]);
    }
}

// Funzione per eliminare un utente
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deletedRows = $userDTO->deleteUser($delete_id);
    header("Location: index.php");
    exit();
}

if (isset($_REQUEST['id']) && $_REQUEST['action'] == 'update') {
    $id = intval($_REQUEST['id']);
    // Recupera i dati dell'utente dalla richiesta POST
    $firstname = $_POST['firstnameUp'];
    $lastname = $_POST['lastnameUp'];
    $email = $_POST['emailUp'];
    $password = $_POST['passwordUp'];
    $isadmin = isset($_POST['adminUp']) ? $_POST['adminUp'] : 0;

    $res = $userDTO->updateUser([
        'id' => $id,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => $password,
        'isadmin' => $isadmin
    ]);

    header('Location: index.php');
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progetto Settimana 16</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>

<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary px-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sett16</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <div>
                        <button class="btn btn-danger" type="logout">Esci</button>
                    </div>
            </div>
        </div>
    </nav>

    <h1 class="text-center my-4">Amministrazione</h1>

    <div class="d-flex justify-content-center my-4">

        <a href="create.php" class="btn btn-success w-25" data-bs-toggle="modal" data-bs-target="#creaUtente">
            Aggiungi utenti
        </a>

    </div>

    <div class="container">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Mail</th>
                    <th scope="col">Password</th>
                    <th scope="col">Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res) {
                    foreach ($res as $record) {
                        ?>
                        <tr>
                            <td>
                                <?= $record["id"] ?>
                            </td>
                            <td>
                                <?= $record["firstname"] ?>
                            </td>
                            <td>
                                <?= $record["lastname"] ?>
                            </td>
                            <td>
                                <?= $record["email"] ?>
                            </td>
                            <td>
                                <?= $record["password"] ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if ($record["isadmin"] == 0): ?>
                                    <i class="fas fa-circle text-danger"></i>
                                <?php else: ?>
                                    <i class="fas fa-circle text-success"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modificaUtente_<?= $record["id"] ?>">Modifica</a>
                                <a href="index.php?delete_id=<?= $record["id"] ?>" class="btn btn-danger"
                                    onclick="return confirm('Sei sicuro di voler eliminare questo utente?')">Elimina</a>
                            </td>
                        </tr>
                        <!-- Modal per la modifica -->
                        <div class="modal fade" id="modificaUtente_<?= $record["id"] ?>" tabindex="-1"
                            aria-labelledby="modificaUtenteLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modificaUtenteLabel">Gestione Utenti</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="index.php">
                                            <input name="id" type="hidden" class="form-control" id="id" aria-describedby="id"
                                                value="<?= $record["id"] ?>">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">Nome</label>
                                                <input name="firstnameUp" type="text" class="form-control" id="firstname"
                                                    aria-describedby="firstname" value="<?= $record["firstname"] ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">Cognome</label>
                                                <input name="lastnameUp" type="text" class="form-control" id="lastname"
                                                    aria-describedby="lastname" value="<?= $record["lastname"] ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input name="emailUp" type="email" class="form-control" id="email"
                                                    aria-describedby="emailHelp" value="<?= $record["email"] ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input name="passwordUp" type="password" class="form-control" id="password"
                                                    value="<?= $record["password"] ?>">
                                            </div>
                                            <div class="mb-3 d-flex align-items-center">
                                                <label for="isadmin" class="form-label me-3">Admin</label>
                                                <div class="form-check form-switch form-check-lg">
                                                    <input name="adminUp" type="checkbox" class="form-check-input" id="isadmin"
                                                        aria-describedby="isadmin" value="<?= $record["isadmin"] ?>">
                                                    <label class="form-check-label" for="isadmin"></label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Chiudi</button>
                                                <button name="action" value="update" type="submit"
                                                    class="btn btn-primary">Modifica</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>

<!-- Modal -->
<div class="modal fade" id="creaUtente" tabindex="-1" aria-labelledby="creaUtenteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="creaUtenteLabel">Gestione Utenti</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">Nome</label>
                        <input name="firstname" type="text" class="form-control" id="firstname"
                            aria-describedby="firstname">
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Cognome</label>
                        <input name="lastname" type="text" class="form-control" id="lastname"
                            aria-describedby="lastname">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" id="password">
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label for="isadmin" class="form-label me-3">Admin</label>
                        <div class="form-check form-switch form-check-lg">
                            <input name="isadmin" type="checkbox" class="form-check-input" id="isadmin"
                                aria-describedby="isadmin">
                            <label class="form-check-label" for="isadmin"></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary">Crea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
