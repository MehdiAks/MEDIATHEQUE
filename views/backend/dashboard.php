<?php
require_once dirname(__DIR__, 2) . '/config.php';
check_auth();

$database = sql_connect();
$tables = [
    'users' => 'USER',
    'albums' => 'ALBUM',
    'artistes' => 'ARTISTE',
    'groupes' => 'GROUPE',
    'likes' => 'LIKES',
    'titres' => 'TITRE',
];
$counts = [];
foreach ($tables as $key => $table) {
    // The table names come exclusively from the fixed allow-list above.
    $statement = $database->prepare("SELECT COUNT(*) FROM `$table`");
    $statement->execute();
    $counts[$key] = (int) $statement->fetchColumn();
}

include dirname(__DIR__, 2) . '/header.php';
?>

<!-- Bootstrap admin dashboard template -->
<div>
    <hr class="my-3">
    <div style="color: black; font-size: 30px; font-family: Montserrat; font-weight: 400; padding-left: 3rem ;word-wrap: break-word">Liens permettant d'administrer le Blog Médiathèque</div>    
    <hr class="my-3">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Bienvenue sur le dashboard !</p>
            </div>
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Objets</th>
                            <th>Actions</th>
                            <th>Commentaires</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Users <span class="badge bg-secondary"><?= $counts['users'] ?></span></td>
                            <td>
                                <a href="/views/backend/users/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/users/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/users/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/users/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Albums <span class="badge bg-secondary"><?= $counts['albums'] ?></span></td>
                            <td>
                                <a href="/views/backend/albums/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/albums/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/albums/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/albums/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Artistes <span class="badge bg-secondary"><?= $counts['artistes'] ?></span></td>
                            <td>
                                <a href="/views/backend/artistes/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/artistes/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/artistes/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/artistes/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Groupes <span class="badge bg-secondary"><?= $counts['groupes'] ?></span></td>
                            <td>
                                <a href="/views/backend/groupes/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/groupes/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/groupes/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/groupes/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Likes <span class="badge bg-secondary"><?= $counts['likes'] ?></span></td>
                            <td>
                                <a href="/views/backend/likes/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/likes/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/likes/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/likes/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Titres <span class="badge bg-secondary"><?= $counts['titres'] ?></span></td>
                            <td>
                                <a href="/views/backend/titres/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/titres/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/titres/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/titres/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td></td>
                        </tr>
<!--                         <tr>
                            <td>Statuts</td>
                            <td>
                                <a href="/views/backend/statutsCC/list.php" class="btn btn-primary">List</a>
                                <a href="/views/backend/statutsCC/create.php" class="btn btn-success">Create</a>
                                <a href="/views/backend/statutsCC/edit.php" class="btn btn-warning">Edit</a>
                                <a href="/views/backend/statutsCC/delete.php" class="btn btn-danger">Delete</a>
                            </td>
                            <td>
                                <p>CC S2 : Exemple CRUD fourni</p>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
