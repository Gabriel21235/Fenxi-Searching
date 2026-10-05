<?php
require_once __DIR__.'/includes/funcoes.php';exigir_login();$user=usuario_atual($pdo);$erro='';$resposta='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    exigir_csrf();$pergunta=trim((string)($_POST['pergunta']??''));
    if($pergunta==='')$erro='Digite uma pergunta.';
    else{
        $key=getenv('GEMINI_API_KEY')?:'';$model=getenv('GEMINI_MODEL')?:($user['modelo_ia']?:'gemini-flash');
        if($key==='')$erro='Configure GEMINI_API_KEY no ambiente do PHP para ativar a IA.';
        else{
            $payload=json_encode(['contents'=>[['parts'=>[['text'=>'Você é o Explica-Meme do Fênix Searching. Explique de forma clara e curta o contexto, significado ou possível origem do conteúdo. Não invente fatos; quando não souber, diga que não sabe. Pergunta: '.$pergunta]]]]],JSON_UNESCAPED_UNICODE);
            $url='https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent';
            $ctx=stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\nX-goog-api-key: {$key}\r\n",'content'=>$payload,'timeout'=>20,'ignore_errors'=>true]]);
            $raw=@file_get_contents($url,false,$ctx);$data=$raw?json_decode($raw,true):null;$resposta=$data['candidates'][0]['content']['parts'][0]['text']??'';
            if($resposta==='')$erro='A API não retornou uma resposta. Verifique a chave e o modelo.';
            else{$st=$pdo->prepare("INSERT INTO historico_ia(id_usuario,tipo_input,conteudo_input,resposta_ia,modelo_usado) VALUES(:u,'texto',:i,:r,:m)");$st->execute([':u'=>$user['id_usuario'],':i'=>$pergunta,':r'=>$resposta,':m'=>$model]);}
        }
    }
}
render_header('Explica-Meme',$user);
?>
<div class="container narrow section"><div class="card form-card"><p class="eyebrow">IA · EXPLICADOR DE MEMES</p><h1>Explicador de Memes com IA</h1><p class="muted">Digite o contexto ou descreva a mídia para entender seu significado.</p>
<?php if($erro):?><div class="alert error"><?=e($erro)?></div><?php endif;?>
<form method="post"><?=csrf_input()?><label>Pergunta<textarea name="pergunta" required placeholder="Ex.: O que significa este meme?"></textarea></label><button class="btn btn-primary">Explicar com IA</button></form>
<?php if($resposta):?><div class="ai-result"><h2>Explicação</h2><p><?=nl2br(e($resposta))?></p></div><?php endif;?></div></div>
<?php render_footer(); ?>
