<?php
require_once __DIR__.'/includes/funcoes.php'; exigir_login();$user=usuario_atual($pdo);$id=(int)($_GET['id']??0);
$st=$pdo->prepare("SELECT * FROM midias WHERE id_midia=:id AND status='aprovado' LIMIT 1");$st->execute([':id'=>$id]);$m=$st->fetch();
if(!$m){http_response_code(404);exit('Mídia não encontrada.');}
$wait=segundos_cooldown($pdo,(int)$user['id_usuario'],(int)$user['cooldown_horas']);
if($wait>0){http_response_code(429);exit('Download bloqueado pelo cooldown. Aguarde '.formatar_tempo($wait).'.');}
$file=__DIR__.'/'.$m['caminho_arquivo'];
if(!is_file($file)){http_response_code(404);exit('Arquivo da mídia não encontrado no servidor.');}
$st=$pdo->prepare('INSERT INTO downloads(id_midia,id_usuario) VALUES(:m,:u)');$st->execute([':m'=>$id,':u'=>$user['id_usuario']]);
header('Content-Type: '.$m['tipo_arquivo']);header('Content-Length: '.filesize($file));header('Content-Disposition: attachment; filename="'.basename($file).'"');readfile($file);exit;
