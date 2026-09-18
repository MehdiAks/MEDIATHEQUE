<?php
function audio_url(?string $path): ?string {
    return $path && preg_match('~^uploads/audio/[a-f0-9]{32}\.(mp3|wav|ogg)$~D',$path) ? BASE_URL.'/'.$path : null;
}
function remove_audio(?string $path): void {
    if (audio_url($path) && is_file(ROOT.'/'.$path) && !unlink(ROOT.'/'.$path)) error_log('Suppression audio impossible : '.$path);
}
function upload_audio(): ?string {
    $file=$_FILES['audioTit'] ?? null;
    if ($file===null) return null;
    if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) throw new InvalidArgumentException('Fichier audio invalide.');
    if ($file['error']===UPLOAD_ERR_NO_FILE) return null;
    if ($file['error']!==UPLOAD_ERR_OK) throw new InvalidArgumentException('Envoi audio impossible. Vérifiez les limites du serveur et la taille du fichier (20 Mo maximum).');
    if (!is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) throw new InvalidArgumentException('Envoi audio invalide.');
    if (filesize($file['tmp_name'])>20*1024*1024 || filesize($file['tmp_name'])===0) throw new InvalidArgumentException('Le fichier audio doit peser entre 1 octet et 20 Mo.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extension=['audio/mpeg'=>'mp3','audio/wav'=>'wav','audio/x-wav'=>'wav','audio/vnd.wave'=>'wav','audio/ogg'=>'ogg','application/ogg'=>'ogg'][$mime] ?? null;
    if (!$extension) throw new InvalidArgumentException('Format audio invalide. Choisissez un fichier MP3, WAV ou OGG.');
    $path='uploads/audio/'.bin2hex(random_bytes(16)).'.'.$extension;
    if (!move_uploaded_file($file['tmp_name'],ROOT.'/'.$path)) throw new RuntimeException('Stockage audio indisponible.',500);
    return $path;
}
