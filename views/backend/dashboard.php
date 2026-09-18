<?php
include '../../header.php';

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
                            <td>Users</td>
                            <td>
                                <a href="/views/backend/users/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/users/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/users/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/users/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Albums</td>
                            <td>
                                <a href="/views/backend/albums/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/albums/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/albums/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/albums/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Artistes</td>
                            <td>
                                <a href="/views/backend/artistes/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/artistes/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/artistes/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/artistes/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Groupes</td>
                            <td>
                                <a href="/views/backend/groupes/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/groupes/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/groupes/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/groupes/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Likes</td>
                            <td>
                                <a href="/views/backend/likes/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/likes/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/likes/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/likes/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Titres</td>
                            <td>
                                <a href="/views/backend/titres/list.php" class="btn btn-primary disabled">List</a>
                                <a href="/views/backend/titres/create.php" class="btn btn-success disabled">Create</a>
                                <a href="/views/backend/titres/edit.php" class="btn btn-warning disabled">Edit</a>
                                <a href="/views/backend/titres/delete.php" class="btn btn-danger disabled">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
