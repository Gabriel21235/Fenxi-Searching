# Fênix Searching

MVP do TCC em PHP + PDO + MySQL para execução local no XAMPP.

## Estrutura

A pasta raiz deve ser:

`C:\xampp\htdocs\fenix searching\`

O banco é separado da pasta e se chama:

`Fenix_Searching`

## Instalação

1. Inicie Apache e MySQL no XAMPP.
2. Abra o phpMyAdmin.
3. Importe `database.sql`.
4. Coloque esta pasta dentro de `C:\xampp\htdocs\`.
5. Acesse no navegador:

`http://localhost/fenix%20searching/`

## Administração

O SQL cria um administrador de demonstração. Altere a senha antes de qualquer uso real.

## IA

A página `ia.php` procura `GEMINI_API_KEY` no ambiente. Não coloque uma chave real dentro dos arquivos PHP.

## Observação

O editor implementado é um MVP de edição de imagens no navegador e registra a edição na tabela `edicoes`.
