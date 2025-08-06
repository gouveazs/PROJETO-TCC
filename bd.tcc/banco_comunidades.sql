CREATE database comunidades;
use comunidades;

-- Tabela: comunidades
CREATE TABLE comunidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    `imagem` LONGBLOB,
    criada_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: membros_comunidade
-- (guarda relação entre ID do usuário externo e a comunidade)
CREATE TABLE membros_comunidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_comunidade INT NOT NULL,
    entrou_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_comunidade) REFERENCES comunidades(id)
);

-- Tabela: mensagens_chat
CREATE TABLE mensagens_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_comunidade INT NOT NULL,
    mensagem TEXT NOT NULL,
    enviada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_comunidade) REFERENCES comunidades(id)
);
