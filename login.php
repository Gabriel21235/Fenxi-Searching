<?php
require_once __DIR__.'/includes/funcoes.php';
if(usuario_logado()){header('Location: dashboard.php');exit;}
$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    exigir_csrf();
    $email=trim((string)($_POST['email']??'')); $senha=(string)($_POST['senha']??'');
    $st=$pdo->prepare('SELECT id_usuario,usu_nome,usu_email,usu_senha,id_plano FROM usuarios WHERE usu_email=:email LIMIT 1');
    $st->execute([':email'=>mb_strtolower($email)]);
    $u=$st->fetch();
    if($u && password_verify($senha,$u['usu_senha'])){
        session_regenerate_id(true);
        $_SESSION['usuario_id']=(int)$u['id_usuario'];
        $_SESSION['usuario_nome']=$u['usu_nome'];
        $_SESSION['id_plano']=(int)$u['id_plano'];
        unset($_SESSION['csrf_token']);
        header('Location: dashboard.php');exit;
    }
    $erro='E-mail ou senha inválidos.';
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar · Fênix Searching</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth-page"><div class="auth-layout"><div class="auth-card">
<a class="brand auth-brand" href="index.php"><span class="phoenix">✦</span> FÊNIX <span>SEARCHING</span></a>
<h1>Bem-vindo de volta!</h1><p class="sub">Entre na sua conta para continuar.</p>
<?php if($erro):?><div class="alert error"><?=e($erro)?></div><?php endif;?>
<form method="post"><?=csrf_input()?><label>E-mail<input type="email" name="email" placeholder="usuario@email.com" required></label><label>Senha<input type="password" name="senha" required></label><button class="btn btn-primary btn-block">Entrar</button></form>
<p class="auth-foot">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
</div></div></body></html>
