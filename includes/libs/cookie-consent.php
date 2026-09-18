<?php
function getCookieConsent($pdo = null): ?int {
    return match ($_COOKIE['mediatheque_consent'] ?? null) { 'accepted'=>1, 'refused'=>0, default=>null };
}
