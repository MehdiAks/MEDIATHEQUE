<?php
const FLASH_MESSAGE_ERROR = 'Une erreur est survenue.';

function flash_success(string $message = 'Opération réalisée avec succès.'): void
{
    $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
}

function flash_error(string $message = FLASH_MESSAGE_ERROR): void
{
    $_SESSION['flash'] = ['type' => 'danger', 'message' => $message];
}

function flash_delete_impossible(string $message): void
{
    flash_error($message);
}
