<?php
require_once __DIR__.'/includes/funcoes.php'; exigir_login();$user=usuario_atual($pdo);$id=(int)($_GET['id']??0);
$st=$pdo->prepare("SELECT * FROM midias WHERE id_midia=:id AND status='aprovado' AND tipo_arquivo LIKE 'image/%' LIMIT 1");$st->execute([':id'=>$id]);$m=$st->fetch();
if(!$m){http_response_code(404);exit('A edição está disponível para imagens aprovadas.');}
$usadas=edicoes_hoje($pdo,(int)$user['id_usuario']);$limite=$user['limite_edicoes_dia'];
if($limite!==null && $usadas >= (int)$limite){render_header('Editor',$user);echo '<div class="container section"><div class="card"><h1>Limite diário atingido</h1><p>Seu plano permite '.$limite.' edições por dia.</p></div></div>';render_footer();exit;}
render_header('Editor',$user);
?>
<div class="container section"><div class="editor-layout"><div class="card form-card"><p class="eyebrow">EDITOR</p><h1><?=e($m['titulo'])?></h1><label>Legenda<input id="txt" maxlength="80" placeholder="Digite uma legenda"></label><label>Escala<input id="scale" type="range" min=".5" max="1.5" step=".1" value="1"></label><button class="btn btn-primary" id="save">Salvar edição</button></div><div class="card canvas-card"><canvas id="canvas"></canvas></div></div>
<form id="saveForm" method="post" action="salvar_edicao.php"><?=csrf_input()?><input type="hidden" name="id_midia" value="<?=$m['id_midia']?>"><input type="hidden" name="imagem" id="imagem"></form></div>
<script>
const img=new Image(), c=document.getElementById('canvas'),ctx=c.getContext('2d');
img.onload=()=>{c.width=img.naturalWidth;c.height=img.naturalHeight;draw()}; img.src=<?=json_encode($m['caminho_arquivo'])?>;
function draw(){const s=parseFloat(scale.value);ctx.clearRect(0,0,c.width,c.height);ctx.save();ctx.translate(c.width/2,c.height/2);ctx.scale(s,s);ctx.drawImage(img,-img.width/2,-img.height/2);ctx.restore();const t=txt.value.trim();if(t){ctx.font='bold 36px Arial';ctx.textAlign='center';ctx.strokeStyle='#000';ctx.lineWidth=7;ctx.strokeText(t,c.width/2,c.height-35);ctx.fillStyle='#fff';ctx.fillText(t,c.width/2,c.height-35)}}
txt.addEventListener('input',draw);scale.addEventListener('input',draw);
document.getElementById('save').onclick=()=>{document.getElementById('imagem').value=c.toDataURL('image/png');document.getElementById('saveForm').submit();}
</script>
<?php render_footer(); ?>
