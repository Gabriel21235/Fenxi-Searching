<?php
require_once __DIR__.'/includes/funcoes.php'; exigir_login();exigir_csrf();$user=usuario_atual($pdo);$id=(int)($_POST['id_midia']??0);$data=(string)($_POST['imagem']??'');
$st=$pdo->prepare("SELECT * FROM midias WHERE id_midia=:id AND status='aprovado' AND tipo_arquivo LIKE 'image/%'");$st->execute([':id'=>$id]);$m=$st->fetch();
if(!$m)exit('Mídia inválida.');
$usadas=edicoes_hoje($pdo,(int)$user['id_usuario']);if($user['limite_edicoes_dia']!==null && $usadas >= (int)$user['limite_edicoes_dia'])exit('Limite diário atingido.');
if(!preg_match('#^data:image/png;base64,(.+)$#',$data,$match))exit('Imagem de edição inválida.');
$bin=base64_decode($match[1],true);if($bin===false||strlen($bin)>15*1024*1024)exit('Imagem inválida ou muito grande.');
$nome='edit_'.bin2hex(random_bytes(12)).'.png';$dir=__DIR__.'/uploads';if(!is_dir($dir))mkdir($dir,0775,true);
file_put_contents($dir.'/'.$nome,$bin);
$st=$pdo->prepare('INSERT INTO edicoes(id_usuario,id_midia,caminho_editado) VALUES(:u,:m,:p)');$st->execute([':u'=>$user['id_usuario'],':m'=>$id,':p'=>'uploads/'.$nome]);
header('Location: dashboard.php?edicao=ok');exit;
