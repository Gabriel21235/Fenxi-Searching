CREATE DATABASE IF NOT EXISTS Fenix_Searching CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Fenix_Searching;

CREATE TABLE IF NOT EXISTS planos (
 id_plano INT AUTO_INCREMENT PRIMARY KEY,
 nome_plano VARCHAR(50) NOT NULL UNIQUE,
 preco DECIMAL(10,2) NOT NULL DEFAULT 0,
 limite_edicoes_dia INT NULL,
 cooldown_horas INT DEFAULT 0,
 modelo_ia VARCHAR(80) NOT NULL,
 permite_zip BOOLEAN DEFAULT FALSE,
 marca_dagua BOOLEAN DEFAULT TRUE,
 exibe_ads BOOLEAN DEFAULT TRUE,
 limite_upload_mb INT DEFAULT 15,
 limite_favoritos INT DEFAULT 20,
 descricao TEXT
) ENGINE=InnoDB;

INSERT INTO planos(id_plano,nome_plano,preco,limite_edicoes_dia,cooldown_horas,modelo_ia,permite_zip,marca_dagua,exibe_ads,limite_upload_mb,limite_favoritos,descricao)
VALUES
(1,'Gratuito',0,10,20,'gemini-flash',FALSE,TRUE,TRUE,15,20,'Pesquisa, downloads com cooldown de 20h, até 10 edições por dia e IA básica.'),
(2,'Pro',29.90,NULL,0,'gemini-pro',TRUE,FALSE,FALSE,100,NULL,'Downloads e edições ilimitados, sem anúncios e IA avançada.')
ON DUPLICATE KEY UPDATE nome_plano=VALUES(nome_plano),preco=VALUES(preco),limite_edicoes_dia=VALUES(limite_edicoes_dia),cooldown_horas=VALUES(cooldown_horas),modelo_ia=VALUES(modelo_ia);

CREATE TABLE IF NOT EXISTS usuarios (
 id_usuario INT AUTO_INCREMENT PRIMARY KEY,
 usu_nome VARCHAR(100) NOT NULL,
 usu_email VARCHAR(150) NOT NULL UNIQUE,
 usu_senha VARCHAR(255) NOT NULL,
 usu_cpf VARCHAR(14) NULL,
 usu_telefone VARCHAR(20) NULL,
 id_plano INT NOT NULL DEFAULT 1,
 data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(id_plano) REFERENCES planos(id_plano) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS administradores (
 id_admin INT AUTO_INCREMENT PRIMARY KEY,
 adm_nome VARCHAR(100) NOT NULL,
 adm_email VARCHAR(150) NOT NULL UNIQUE,
 adm_senha VARCHAR(255) NOT NULL,
 adm_nivel ENUM('master','moderador') DEFAULT 'moderador',
 data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
 id_categoria INT AUTO_INCREMENT PRIMARY KEY,
 cat_nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO categorias(id_categoria,cat_nome) VALUES
(1,'Meme'),(2,'GIF'),(3,'Video'),(4,'Imagem'),(5,'Emoji'),(6,'Texto Viral')
ON DUPLICATE KEY UPDATE cat_nome=VALUES(cat_nome);

CREATE TABLE IF NOT EXISTS midias (
 id_midia INT AUTO_INCREMENT PRIMARY KEY,
 titulo VARCHAR(200) NOT NULL,
 descricao TEXT,
 id_categoria INT NOT NULL,
 caminho_arquivo VARCHAR(500) NOT NULL,
 tipo_arquivo VARCHAR(50) NOT NULL,
 id_usuario INT NULL,
 status ENUM('aprovado','pendente','rejeitado') DEFAULT 'pendente',
 data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT,
 FOREIGN KEY(id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
 INDEX idx_midias_status(status),
 INDEX idx_midias_titulo(titulo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS downloads (
 id_download INT AUTO_INCREMENT PRIMARY KEY,
 id_midia INT NOT NULL,
 id_usuario INT NOT NULL,
 data_download DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(id_midia) REFERENCES midias(id_midia) ON DELETE CASCADE,
 FOREIGN KEY(id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
 INDEX idx_download_usuario_data(id_usuario,data_download)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS edicoes (
 id_edicao INT AUTO_INCREMENT PRIMARY KEY,
 id_usuario INT NOT NULL,
 id_midia INT NOT NULL,
 caminho_editado VARCHAR(500),
 data_edicao DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
 FOREIGN KEY(id_midia) REFERENCES midias(id_midia) ON DELETE CASCADE,
 INDEX idx_edicao_usuario_data(id_usuario,data_edicao)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS historico_ia (
 id_historico INT AUTO_INCREMENT PRIMARY KEY,
 id_usuario INT NOT NULL,
 tipo_input ENUM('texto','imagem','link') NOT NULL,
 conteudo_input TEXT NOT NULL,
 resposta_ia TEXT NOT NULL,
 modelo_usado VARCHAR(80),
 data_consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Administrador de demonstração:
-- senha: Admin@2026
INSERT INTO administradores(adm_nome,adm_email,adm_senha,adm_nivel)
VALUES('Administrador Master','admin@fenix.com','$2y$12$8bMB4nQfHkAtqk1lJFqzYuBRfg7bCkYuAlHU9lTx1fzgrRTOFhPBW','master')
ON DUPLICATE KEY UPDATE adm_email=VALUES(adm_email);

-- Mídias de demonstração. Os arquivos SVG abaixo são criados pelo pacote.
INSERT INTO midias(titulo,descricao,id_categoria,caminho_arquivo,tipo_arquivo,status)
VALUES
('Gato Surpreso','Exemplo de reação inesperada.',1,'uploads/demo_gato.svg','image/svg+xml','aprovado'),
('Dançando','Exemplo de conteúdo em formato visual.',2,'uploads/demo_danca.svg','image/svg+xml','aprovado'),
('Imagem Viral','Exemplo de imagem do acervo.',4,'uploads/demo_imagem.svg','image/svg+xml','aprovado'),
('Deu ruim no deploy','Exemplo de texto viral.',6,'uploads/demo_texto.svg','image/svg+xml','aprovado');
