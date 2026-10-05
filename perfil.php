<?php
require_once __DIR__.'/includes/funcoes.php';exigir_login();$user=usuario_atual($pdo);$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){exigir_csrf();$nome=trim((string)($_POST['nome']??''));$tel=trim((string)($_POST['telefone']??''));if(mb_strlen($nome)<3)$msg='Nome inválido.';else{$st=$pdo->prepare('UPDATE usuarios SET usu_nome=:n,usu_telefone=:t WHERE id_usuario=:id');$st->execute([':n'=>$nome,':t'=>$tel?:null,':id'=>$user['id_usuario']]);$_SESSION['usuario_nome']=$nome;$msg='Perfil atualizado.';$user=usuario_atual($pdo);}}
render_header('Perfil',$user);
?>
<div class="container narrow section"><div class="card form-card"><p class="eyebrow">CONTA</p><h1>Meu perfil</h1><?php if($msg):?><div class="alert success"><?=e($msg)?></div><?php endif;?><form method="post"><?=csrf_input()?><label>Nome<input name="nome" value="<?=e($user['usu_nome'])?>" required></label><label>E-mail<input value="<?=e($user['usu_email'])?>" disabled></label><label>Telefone<input name="telefone" value="<?=e($user['usu_telefone']??'')?>"></label><button class="btn btn-primary">Salvar</button></form><hr><p class="muted">Plano atual: <strong><?=e($user['nome_plano'])?></strong></p><a class="btn btn-outline" href="upgrade.php">Ver planos</a></div></div>
<?php render_footer(); ?>
