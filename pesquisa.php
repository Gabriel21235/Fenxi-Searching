<?php
require_once __DIR__.'/includes/funcoes.php';
$q=trim((string)($_GET['q']??''));$categoria=trim((string)($_GET['categoria']??''));
$sql="SELECT m.*,c.cat_nome FROM midias m JOIN categorias c ON c.id_categoria=m.id_categoria WHERE m.status='aprovado'";
$params=[];
if($q!==''){ $sql.=" AND (m.titulo LIKE :q OR m.descricao LIKE :q)";$params[':q']='%'.$q.'%';}
if($categoria!==''){ $sql.=" AND c.cat_nome=:cat";$params[':cat']=$categoria;}
$sql.=" ORDER BY m.data_upload DESC LIMIT 60";
$st=$pdo->prepare($sql);$st->execute($params);$midias=$st->fetchAll();
$user=usuario_atual($pdo);
render_header('Explorar',$user);
?>
<div class="container section"><div class="page-heading"><p class="eyebrow">EXPLORAR</p><h1>Encontre uma mídia</h1><p class="muted">Pesquise no acervo aprovado.</p></div>
<form class="search-bar" method="get"><input name="q" value="<?=e($q)?>" placeholder="Buscar memes, GIFs, vídeos..."><select name="categoria"><option value="">Todas as categorias</option><?php foreach(['Meme','GIF','Video','Imagem','Emoji','Texto Viral'] as $c):?><option <?= $categoria===$c?'selected':''?>><?=e($c)?></option><?php endforeach;?></select><button class="btn btn-primary">Buscar</button></form>
<div class="media-grid"><?php foreach($midias as $m):?><article class="media-card"><div class="media-thumb"><?php if(str_starts_with($m['tipo_arquivo'],'image/')):?><img src="<?=e($m['caminho_arquivo'])?>" alt="<?=e($m['titulo'])?>"><?php else:?><div class="media-placeholder">▶ <?=e(tipo_generico($m['tipo_arquivo']))?></div><?php endif;?></div><div class="media-body"><span class="tag"><?=e($m['cat_nome'])?></span><h3><?=e($m['titulo'])?></h3><p><?=e($m['descricao'])?></p><?php if($user):?><a class="btn btn-small btn-primary" href="download.php?id=<?=(int)$m['id_midia']?>">Baixar</a> <a class="btn btn-small btn-outline" href="editar.php?id=<?=(int)$m['id_midia']?>">Editar</a><?php else:?><a class="btn btn-small btn-outline" href="login.php">Entrar para usar</a><?php endif;?></div></article><?php endforeach;?></div>
<?php if(!$midias):?><div class="card empty">Nenhuma mídia encontrada.</div><?php endif;?></div>
<?php render_footer(); ?>
