<?php
require_once __DIR__.'/includes/funcoes.php'; exigir_login();$user=usuario_atual($pdo);$erros=[];$ok='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    exigir_csrf();
    $titulo=trim((string)($_POST['titulo']??''));$descricao=trim((string)($_POST['descricao']??''));$cat=(int)($_POST['id_categoria']??0);
    if(mb_strlen($titulo)<3)$erros[]='Informe um título com pelo menos 3 caracteres.';
    $st=$pdo->prepare('SELECT id_categoria FROM categorias WHERE id_categoria=:id');$st->execute([':id'=>$cat]);if(!$st->fetch())$erros[]='Categoria inválida.';
    if(!isset($_FILES['arquivo'])||$_FILES['arquivo']['error']!==UPLOAD_ERR_OK)$erros[]='Selecione um arquivo.';
    if(!$erros){
        $f=$_FILES['arquivo'];$limite=(int)$user['limite_upload_mb']*1024*1024;
        if($f['size']>$limite)$erros[]='O arquivo ultrapassa o limite do seu plano.';
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $permitidos=['image/jpeg','image/png','image/gif','image/webp','video/mp4','video/webm'];
        if(!in_array($mime,$permitidos,true))$erros[]='Tipo de arquivo não permitido.';
        if(!$erros){
            $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));$nome=bin2hex(random_bytes(12)).'.'.$ext;
            $dir=__DIR__.'/uploads';if(!is_dir($dir))mkdir($dir,0775,true);
            if(move_uploaded_file($f['tmp_name'],$dir.'/'.$nome)){
                $caminho='uploads/'.$nome;
                $st=$pdo->prepare('INSERT INTO midias(titulo,descricao,id_categoria,caminho_arquivo,tipo_arquivo,id_usuario,status) VALUES(:t,:d,:c,:p,:m,:u,"pendente")');
                $st->execute([':t'=>$titulo,':d'=>$descricao,':c'=>$cat,':p'=>$caminho,':m'=>$mime,':u'=>$user['id_usuario']]);
                $ok='Upload enviado para moderação.';
            }else $erros[]='Não foi possível salvar o arquivo.';
        }
    }
}
$cats=$pdo->query('SELECT * FROM categorias ORDER BY id_categoria')->fetchAll();
render_header('Upload',$user);
?>
<div class="container narrow section"><div class="card form-card"><p class="eyebrow">UPLOAD DE MÍDIA</p><h1>Compartilhe sua mídia</h1><p class="muted">Conteúdo enviado fica pendente até a moderação.</p>
<?php foreach($erros as $e):?><div class="alert error"><?=e($e)?></div><?php endforeach;?><?php if($ok):?><div class="alert success"><?=e($ok)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><?=csrf_input()?><label>Arquivo<input type="file" name="arquivo" accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm" required></label><label>Título<input name="titulo" required placeholder="Digite um título para sua mídia"></label><label>Descrição<textarea name="descricao" placeholder="Adicione uma descrição (opcional)"></textarea></label><label>Categoria<select name="id_categoria" required><option value="">Selecione uma categoria</option><?php foreach($cats as $c):?><option value="<?=$c['id_categoria']?>"><?=e($c['cat_nome'])?></option><?php endforeach;?></select></label><button class="btn btn-primary btn-block">Fazer Upload</button></form></div></div>
<?php render_footer(); ?>
