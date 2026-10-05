<?php
require_once __DIR__.'/includes/funcoes.php';
if(usuario_logado()){header('Location: dashboard.php');exit;}
$erros=[];$nome='';$email='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    exigir_csrf();
    $nome=trim((string)($_POST['nome']??''));$email=trim((string)($_POST['email']??''));$senha=(string)($_POST['senha']??'');$conf=(string)($_POST['senha_confirma']??'');
    if(mb_strlen($nome)<3||mb_strlen($nome)>100)$erros[]='O nome deve ter entre 3 e 100 caracteres.';
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>150)$erros[]='Informe um e-mail válido.';
    if(mb_strlen($senha)<6)$erros[]='A senha deve possuir no mínimo 6 caracteres.';
    if($senha!==$conf)$erros[]='As senhas não coincidem.';
    if(!$erros){try{
        $st=$pdo->prepare('INSERT INTO usuarios(usu_nome,usu_email,usu_senha,id_plano) VALUES(:nome,:email,:senha,1)');
        $st->execute([':nome'=>$nome,':email'=>mb_strtolower($email),':senha'=>password_hash($senha,PASSWORD_DEFAULT)]);
        header('Location: login.php?cadastro=ok');exit;
    }catch(PDOException $e){$erros[]=(int)($e->errorInfo[1]??0)===1062?'Este e-mail já está cadastrado.':'Não foi possível concluir o cadastro.';}}
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Criar conta · Fênix Searching</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth-page"><div class="auth-layout"><div class="auth-card">
<a class="brand auth-brand" href="index.php"><span class="phoenix">✦</span> FÊNIX <span>SEARCHING</span></a>
<h1>Criar conta</h1><p class="sub">Junte-se à comunidade.</p>
<?php foreach($erros as $e):?><div class="alert error"><?=e($e)?></div><?php endforeach;?>
<form method="post"><?=csrf_input()?><label>Nome<input name="nome" placeholder="Seu nome" required value="<?=e($nome)?>"></label><label>E-mail<input type="email" name="email" placeholder="seu@email.com" required value="<?=e($email)?>"></label><label>Senha<input type="password" name="senha" minlength="6" placeholder="Mínimo 6 caracteres" required></label><label>Confirmar senha<input type="password" name="senha_confirma" minlength="6" required></label><button class="btn btn-primary btn-block">Cadastrar</button></form>
<p class="auth-foot">Já tem uma conta? <a href="login.php">Faça login</a></p>
</div></div></body></html>
