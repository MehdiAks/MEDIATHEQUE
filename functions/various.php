<?php
// Fonction utilitaire cURL pour appeler une URL distante.
function curl($url, $type, $data = null, $headers = null){
    // Initialise une session cURL.
    $ch = curl_init();
    // Définit l'URL cible.
    curl_setopt($ch, CURLOPT_URL, $url);
    // Demande à cURL de retourner le résultat sous forme de chaîne.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Définit la méthode HTTP (GET, POST, PUT, DELETE, etc.).
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    // Désactive la vérification du nom d'hôte SSL (à utiliser prudemment).
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // Désactive la vérification du certificat SSL (à utiliser prudemment).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // Si des données sont fournies, les envoie en body.
    if($data){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    // Si des headers sont fournis, les ajoute à la requête.
    if($headers){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    // Exécute l'appel HTTP.
    $ba_bec_result = curl_exec($ch);
    // Si une erreur cURL survient, on l'affiche.
    if(curl_errno($ch)){
        echo 'Error:' . curl_error($ch);
    }
    // Ferme la session cURL pour libérer les ressources.
    curl_close($ch);
    // Retourne la réponse brute.
    return $ba_bec_result;
}

// Vérifie si une URL est autorisée dans le BBCode (http, https, ancre ou URL relative).
function isAllowedBbcodeUrl($url) {
    // Nettoie la valeur en entrée.
    $url = trim((string) $url);
    // Si vide, ce n'est pas valide.
    if ($url === '') {
        return false;
    }

    // Autorise les ancres (#) et les chemins relatifs (/...).
    if (str_starts_with($url, '#') || str_starts_with($url, '/')) {
        return true;
    }

    // Parse l'URL pour récupérer le schéma (http/https).
    $parsed = parse_url($url);
    // Si l'URL est invalide ou sans schéma, on refuse.
    if ($parsed === false || empty($parsed['scheme'])) {
        return false;
    }

    // N'autorise que http et https.
    return in_array(strtolower($parsed['scheme']), ['http', 'https'], true);
}

// Valide le contenu BBCode pour éviter les balises inconnues ou mal formées.
function isValidBbcodeContent($text) {
    // Si le texte est vide ou null, il est considéré valide.
    if ($text === null || $text === '') {
        return true;
    }

    // Liste des tags BBCode autorisés.
    $allowedTags = ['b', 'i', 'u', 's', 'quote', 'code', 'url', 'emoji'];
    // Extrait toutes les balises BBCode avec une regex.
    preg_match_all('/\\[(\\/)?([^\\]=\\s]+)(?:=([^\\]]*))?\\]/', $text, $matches, PREG_SET_ORDER);

    // Parcourt chaque balise trouvée.
    foreach ($matches as $match) {
        // Indique si la balise est fermante (ex : [/b]).
        $isClosing = $match[1] === '/';
        // Récupère le nom de la balise en minuscule.
        $tag = strtolower($match[2]);
        // Récupère un paramètre éventuel (ex : [url=...]).
        $param = $match[3] ?? null;

        // Si la balise n'est pas dans la liste autorisée, on invalide le contenu.
        if (!in_array($tag, $allowedTags, true)) {
            return false;
        }

        // Règle spécifique pour [emoji=...]
        if ($tag === 'emoji') {
            // Une balise emoji ne doit pas être fermante et doit avoir un paramètre.
            if ($isClosing || $param === null || trim($param) === '') {
                return false;
            }
            continue;
        }

        // Règles spécifiques pour [url] et [url=...]
        if ($tag === 'url') {
            // Une balise fermante ne doit pas porter de paramètre.
            if ($isClosing && $param !== null && $param !== '') {
                return false;
            }
            // Une balise ouvrante avec paramètre vide est invalide.
            if (!$isClosing && $param !== null && trim($param) === '') {
                return false;
            }
            continue;
        }

        // Pour les autres tags, aucun paramètre ne doit être présent.
        if ($param !== null && $param !== '') {
            return false;
        }
    }

    // Si toutes les balises sont valides, on retourne true.
    return true;
}

// Rend le BBCode en HTML sécurisé.
function renderBbcode($text) {
    // Échappe d'abord tout le texte pour éviter l'injection HTML.
    $safeText = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');

    // Transforme les balises [url=...]texte[/url] en liens HTML.
    $safeText = preg_replace_callback('/\\[url=(.*?)\\](.*?)\\[\\/url\\]/is', function ($matches) {
        $url = trim($matches[1]);
        $label = trim($matches[2]);

        // Si l'URL est interdite, on laisse la balise telle quelle.
        if (!isAllowedBbcodeUrl($url)) {
            return $matches[0];
        }

        // Si le label est vide, on affiche l'URL.
        $label = $label === '' ? $url : $label;

        // Construit un lien HTML sécurisé.
        return sprintf('<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>', $url, $label);
    }, $safeText);

    // Transforme les balises [url]texte[/url] en liens HTML.
    $safeText = preg_replace_callback('/\\[url\\](.*?)\\[\\/url\\]/is', function ($matches) {
        $url = trim($matches[1]);

        // Si l'URL est interdite, on laisse la balise telle quelle.
        if (!isAllowedBbcodeUrl($url)) {
            return $matches[0];
        }

        // Construit un lien HTML sécurisé.
        return sprintf('<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>', $url, $url);
    }, $safeText);

    // Remplace les balises simples par leurs équivalents HTML.
    $safeText = preg_replace('/\\[b\\](.*?)\\[\\/b\\]/is', '<strong>$1</strong>', $safeText);
    $safeText = preg_replace('/\\[i\\](.*?)\\[\\/i\\]/is', '<em>$1</em>', $safeText);
    $safeText = preg_replace('/\\[u\\](.*?)\\[\\/u\\]/is', '<span style="text-decoration: underline;">$1</span>', $safeText);
    $safeText = preg_replace('/\\[s\\](.*?)\\[\\/s\\]/is', '<span style="text-decoration: line-through;">$1</span>', $safeText);
    $safeText = preg_replace('/\\[quote\\](.*?)\\[\\/quote\\]/is', '<blockquote>$1</blockquote>', $safeText);
    $safeText = preg_replace('/\\[code\\](.*?)\\[\\/code\\]/is', '<pre><code>$1</code></pre>', $safeText);

    // Table de correspondance des emojis autorisés.
    $emojiMap = [
        'smile' => '😊',
        'heart' => '❤️',
        'wink' => '😉',
        'thumbsup' => '👍',
        'clap' => '👏',
        'fire' => '🔥',
    ];

    // Remplace les balises [emoji=nom] par l'emoji correspondant.
    $safeText = preg_replace_callback('/\\[emoji=(.*?)\\]/i', function ($matches) use ($emojiMap) {
        $key = strtolower(trim($matches[1]));
        return $emojiMap[$key] ?? $matches[0];
    }, $safeText);

    // Convertit les retours à la ligne en balises <br>.
    return nl2br($safeText);
}
?>
