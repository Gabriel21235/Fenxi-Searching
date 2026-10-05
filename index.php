<?php
require_once __DIR__.'/includes/funcoes.php';
$categorias=['Meme','GIF','Video','Imagem','Emoji','Texto Viral'];
$st=$pdo->query("SELECT m.*, c.cat_nome FROM midias m JOIN categorias c ON c.id_categoria=m.id_categoria WHERE m.status='aprovado' ORDER BY m.data_upload DESC LIMIT 8");
$midias=$st->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Fênix Searching</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="home">
<header class="topbar">
<div class="container topbar-inner">
<a class="brand" href="index.php"><span class="phoenix">✦</span> FÊNIX <span>SEARCHING</span></a>
<nav class="nav-links"><a href="index.php">Início</a><a href="pesquisa.php">Explorar</a><a href="#recursos">Recursos</a></nav>
<div class="top-actions"><a class="btn btn-small btn-outline" href="login.php">Entrar</a><a class="btn btn-small btn-primary" href="cadastro.php">Cadastrar</a></div>
</div></header>
<section class="hero-home">
<div class="container hero-content">
<p class="eyebrow">ACERVO DIGITAL DE MÍDIAS</p>
<h1>Encontre as melhores<br><span>mídias da internet</span></h1>
<p class="hero-sub">Memes, GIFs, vídeos, imagens e muito mais em um só lugar.</p>
<form class="search-hero" action="pesquisa.php" method="get">
<input name="q" placeholder="Buscar memes, GIFs, vídeos, imagens..." aria-label="Buscar">
<button class="btn btn-primary">Buscar</button>
</form>
<div class="chips"><?php foreach($categorias as $c): ?><a href="pesquisa.php?categoria=<?=urlencode($c)?>"><?=e($c)?></a><?php endforeach;?></div>
</div>
</section>
<section class="section container" id="recursos">
<div class="section-head"><div><p class="eyebrow">ACERVO</p><h2>Populares agora 🔥</h2></div><a href="pesquisa.php">Ver todos →</a></div>
<div class="media-grid">
<?php foreach($midias as $m): ?>
<article class="media-card">
<div class="media-thumb">
<?php if(str_starts_with($m['tipo_arquivo'],'image/')): ?><img src="<?=e($m['caminho_arquivo'])?>" alt="<?=e($m['titulo'])?>"><?php else: ?><div class="media-placeholder">▶</div><?php endif;?>
</div>
<div class="media-body"><span class="tag"><?=e($m['cat_nome'])?></span><h3><?=e($m['titulo'])?></h3><p><?=e(mb_strimwidth((string)$m['descricao'],0,70,'...'))?></p></div>
</article>
<?php endforeach;?>
<?php if(!$midias): ?><div class="empty card">Nenhuma mídia aprovada ainda. Faça upload pelo painel após o cadastro.</div><?php endif;?>
</div>
</section>
<section class="feature-strip container">
<div><strong>✦</strong><span>Acervo centralizado</span></div><div><strong>⌕</strong><span>Busca rápida</span></div><div><strong>⚡</strong><span>Edição</span></div><div><strong>✦</strong><span>IA integrada</span></div>
</section>
<footer class="footer"><div class="container">Fênix Searching · TCC 2026</div></footer>
</body></html>
