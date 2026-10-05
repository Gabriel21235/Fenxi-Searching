<?php
require_once __DIR__.'/includes/funcoes.php'; exigir_login();
$user=usuario_atual($pdo); if(!$user){header('Location: logout.php');exit;}
$ed=edicoes_hoje($pdo,(int)$user['id_usuario']);$wait=segundos_cooldown($pdo,(int)$user['id_usuario'],(int)$user['cooldown_horas']);
$st=$pdo->prepare("SELECT COUNT(*) FROM midias WHERE status='aprovado'");$st->execute();$total=(int)$st->fetchColumn();
$st=$pdo->prepare("SELECT COUNT(*) FROM midias WHERE id_usuario=:u");$st->execute([':u'=>$user['id_usuario']]);$uploads=(int)$st->fetchColumn();
render_header('Dashboard',$user);
?>
<div class="container app-shell"><aside class="sidebar">
<div class="sidebar-brand">✦ FÊNIX SEARCHING</div>
<a class="active" href="dashboard.php">⌂ Início</a><a href="pesquisa.php">⌕ Explorar</a><a href="upload.php">⇧ Meus Uploads</a><a href="editar.php">✎ Editor</a><a href="ia.php">✦ IA - Explicador</a><a href="perfil.php">⚙ Perfil / Plano</a><a href="logout.php">↪ Sair</a>
</aside><section class="app-content">
<div class="page-heading"><div><p class="eyebrow">DASHBOARD</p><h1>Olá, <?=e($user['usu_nome'])?>! 👋</h1><p class="muted">Bem-vindo ao seu dashboard.</p></div></div>
<div class="plan-banner"><div><span class="muted">Seu plano atual</span><strong><?=e($user['nome_plano'])?></strong></div><a class="btn btn-primary" href="upgrade.php">Upgrade para Pro</a></div>
<div class="stats">
<div class="stat-card"><span>Mídias aprovadas</span><strong><?=$total?></strong></div>
<div class="stat-card"><span>Edições hoje</span><strong><?=$user['limite_edicoes_dia']===null?'Ilimitado':$ed.' / '.$user['limite_edicoes_dia']?></strong></div>
<div class="stat-card"><span>Download</span><strong><?=$wait>0?e(formatar_tempo($wait)):'Disponível'?></strong></div>
<div class="stat-card"><span>Uploads</span><strong><?=$uploads?></strong></div>
</div>
<div class="quick-grid"><a class="card action-card" href="pesquisa.php"><b>⌕ Pesquisar</b><span>Encontre mídias aprovadas.</span></a><a class="card action-card" href="upload.php"><b>⇧ Enviar mídia</b><span>Envie conteúdo para moderação.</span></a><a class="card action-card" href="ia.php"><b>✦ Explica-Meme</b><span>Use a IA para entender conteúdos.</span></a></div>
</section></div>
<?php render_footer(); ?>
