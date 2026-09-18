<?php
function album_image_url(?string $path): ?string {
    return $path && preg_match('~^uploads/albums/[a-f0-9]{32}\.(jpg|png|webp)$~D', $path) ? BASE_URL.'/'.$path : null;
}
function remove_album_image(?string $path): void {
    if (album_image_url($path) && is_file(ROOT.'/'.$path) && !unlink(ROOT.'/'.$path)) error_log('Impossible de supprimer la pochette : '.$path);
}
function upload_album_image(): ?string {
    $file = $_FILES['imageA'] ?? null;
    if ($file === null) return null;
    if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) throw new InvalidArgumentException('Fichier invalide.');
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new InvalidArgumentException('Envoi impossible. Vérifiez la taille du fichier (5 Mo maximum).');
    if (!is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) throw new InvalidArgumentException('Fichier invalide.');
    if (filesize($file['tmp_name']) > 5 * 1024 * 1024) throw new InvalidArgumentException('La pochette ne doit pas dépasser 5 Mo.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $size = @getimagesize($file['tmp_name']);
    if (!isset($extensions[$mime]) || !$size || ($size['mime'] ?? '') !== $mime) throw new InvalidArgumentException('Choisissez une image JPEG, PNG ou WebP valide.');
    if ($size[0] > 6000 || $size[1] > 6000 || $size[0]*$size[1] > 16000000) throw new InvalidArgumentException('Image trop grande : 6 000 pixels par côté et 16 millions de pixels maximum.');
    // Re-encode pixels to discard metadata and any appended executable content.
    $image = @imagecreatefromstring(file_get_contents($file['tmp_name']));
    if (!$image) throw new InvalidArgumentException('Cette image ne peut pas être décodée.');
    $path = 'uploads/albums/'.bin2hex(random_bytes(16)).'.'.$extensions[$mime];
    try {
        imagealphablending($image, false); imagesavealpha($image, true);
        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($image, ROOT.'/'.$path, 88),
            'image/png' => imagepng($image, ROOT.'/'.$path),
            'image/webp' => imagewebp($image, ROOT.'/'.$path, 88),
        };
        if (!$ok) { remove_album_image($path); throw new RuntimeException('Stockage de la pochette indisponible.', 500); }
    } finally { imagedestroy($image); }
    return $path;
}
