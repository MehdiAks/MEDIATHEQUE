<?php
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success'=>false,'error'=>'Les statuts du modèle Blogart ne font pas partie de la médiathèque.']);
