-- MedCore Inventory — Script de criação da base de dados

-- Desativa temporariamente a verificação das chaves estrangeiras para permitir criar as tabelas em qualquer ordem
SET FOREIGN_KEY_CHECKS = 0;

-- --- GESTÃO DE UTILIZADORES E ACESSOS ---
CREATE TABLE IF NOT EXISTS Utilizador (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    nome     VARCHAR(100) NOT NULL,
    email    VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil   VARCHAR(25)  NOT NULL DEFAULT 'tecnico',
    CONSTRAINT pkUtilizadorcodigo   PRIMARY KEY (codigo),
    CONSTRAINT uqUtilizadoremail    UNIQUE (email),
    -- Garante apenas os três níveis de acesso previstos no sistema
    CONSTRAINT chkUtilizadorperfil  CHECK (LOWER(perfil) IN ('admin', 'tecnico', 'profissional de saude')),
    -- Validação básica do formato de email antes de inserir
    CONSTRAINT uqUtilizadoremail_format CHECK (email LIKE '%@%.%'),
    CONSTRAINT chkUtilizadornome    CHECK (LENGTH(TRIM(nome)) >= 2),
    -- Obriga o uso de hashes seguras (como bcrypt de 60 caracteres), evitando passwords em texto limpo
    CONSTRAINT chkUtilizadorpassword CHECK (LENGTH(password) >= 60)
);

-- --- CLASSIFICAÇÃO DOS EQUIPAMENTOS ---
CREATE TABLE IF NOT EXISTS Categoria (
    codigo    INT         NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(50) NOT NULL,
    descricao TEXT,
    CONSTRAINT pkCategoriacodigo PRIMARY KEY (codigo),
    CONSTRAINT uqCategorianome   UNIQUE (nome),
    CONSTRAINT chkCategorianome  CHECK (LENGTH(TRIM(nome)) >= 2)
);

-- --- ESTRUTURA FÍSICA / ORGANIZACIONAL ---
CREATE TABLE IF NOT EXISTS Localizacao (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    edificio VARCHAR(100) NOT NULL,
    piso     VARCHAR(10),
    servico  VARCHAR(100) NOT NULL, -- Ex: Urgências, Radiologia, etc.
    sala     VARCHAR(50),
    ativo    TINYINT(1)   NOT NULL DEFAULT 1, -- Permite desativar localizações antigas sem apagar o histórico
    CONSTRAINT pkLocalizacaocodigo    PRIMARY KEY (codigo),
    CONSTRAINT chkLocalizacaoativo    CHECK (ativo IN (0, 1)),
    CONSTRAINT chkLocalizacaoedificio CHECK (LENGTH(TRIM(edificio)) >= 2),
    CONSTRAINT chkLocalizacaoservico  CHECK (LENGTH(TRIM(servico)) >= 2)
);

-- --- ENTIDADES EXTERNAS E FORNECEDORES ---
CREATE TABLE IF NOT EXISTS Fornecedor (
    codigo         INT          NOT NULL AUTO_INCREMENT,
    nome           VARCHAR(100) NOT NULL,
    nif            VARCHAR(20),
    telefone       VARCHAR(20),
    email          VARCHAR(100),
    morada         VARCHAR(200),
    website        VARCHAR(100),
    pessoaContacto VARCHAR(100),
    telefonePessoa VARCHAR(20),
    tipoFornecedor VARCHAR(30)  NOT NULL,
    observacoes    TEXT,
    ativo          TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT pkFornecedorcodigo    PRIMARY KEY (codigo),
    CONSTRAINT chkFornecedortipo     CHECK (LOWER(tipoFornecedor) IN ('fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis')),
    CONSTRAINT chkFornecedornome     CHECK (LENGTH(TRIM(nome)) >= 2),
    -- Aceita apenas NIFs válidos com exatamente 9 algarismos (se preenchido)
    CONSTRAINT chkFornecedornif      CHECK (nif IS NULL OR nif REGEXP '^[0-9]{9}$'),
    CONSTRAINT chkFornecedoremail    CHECK (email IS NULL OR email LIKE '%@%.%'),
    CONSTRAINT chkFornecedortelefone CHECK (telefone IS NULL OR telefone REGEXP '^[0-9]{9}$'),
    CONSTRAINT chkFornecedorativo    CHECK (ativo IN (0, 1))
);

-- --- INVENTÁRIO DE EQUIPAMENTOS MÉDICOS ---
CREATE TABLE IF NOT EXISTS Equipamento (
    codigo            INT           NOT NULL AUTO_INCREMENT,
    codigoInterno     VARCHAR(20)   NOT NULL, -- Identificador único da instituição (etiqueta/inventário)
    designacao        VARCHAR(100)  NOT NULL,
    marca             VARCHAR(50)   NOT NULL,
    modelo            VARCHAR(50)   NOT NULL,
    fabricante        VARCHAR(100),
    numeroSerie       VARCHAR(50),
    anoFabrico        INT,
    dataAquisicao     DATE,
    custoAquisicao    DECIMAL(10,2),
    tipoEntrada       VARCHAR(20)   NOT NULL,
    estado            VARCHAR(20)   NOT NULL,
    criticidade       VARCHAR(20)   NOT NULL,
    observacoes       TEXT,
    codigoCategoria   INT           NOT NULL,
    codigoLocalizacao INT, -- Permite NULL temporariamente (equipamento em trânsito ou acabado de chegar)
    CONSTRAINT pkEquipamentocodigo        PRIMARY KEY (codigo),
    CONSTRAINT uqEquipamentocodigoInterno UNIQUE (codigoInterno),
    -- Regra de negócio: impede duplicar o mesmo nº de série para o mesmo modelo do mesmo fabricante
    CONSTRAINT uqEquipamentoserieFabricanteModelo UNIQUE (numeroSerie, fabricante, modelo),
    CONSTRAINT chkEquipamentipoEntrada  CHECK (LOWER(tipoEntrada) IN ('compra', 'doacao', 'aluguer', 'emprestimo')),
    CONSTRAINT chkEquipamentoestado       CHECK (LOWER(estado) IN ('ativo', 'em manutencao', 'inativo', 'em calibracao', 'em quarentena', 'abatido')),
    CONSTRAINT chkEquipamentocriticidade  CHECK (LOWER(criticidade) IN ('baixa', 'media', 'alta', 'suporte de vida')),
    CONSTRAINT chkEquipamentoanoFabrico   CHECK (anoFabrico IS NULL OR (anoFabrico >= 1900 AND anoFabrico <= 2100)),
    CONSTRAINT chkEquipamentocustoAquisicao CHECK (custoAquisicao IS NULL OR custoAquisicao >= 0),
    CONSTRAINT fkEquipamentoCategoria     FOREIGN KEY (codigoCategoria)   REFERENCES Categoria (codigo),
    CONSTRAINT fkEquipamentoLocalizacao   FOREIGN KEY (codigoLocalizacao) REFERENCES Localizacao (codigo)
);

-- --- TABELA DE ASSOCIAÇÃO (N para M) ---
-- Liga os equipamentos aos seus respetivos fornecedores (um equipamento pode ter vários parceiros e vice-versa)
CREATE TABLE IF NOT EXISTS EquipamentoFornecedor (
    codigoEquipamento INT NOT NULL,
    codigoFornecedor  INT NOT NULL,
    CONSTRAINT pkEquipamentoFornecedor PRIMARY KEY (codigoEquipamento, codigoFornecedor),
    CONSTRAINT fkEqFornEquipamento     FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkEqFornFornecedor      FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- --- MANUAIS, CERTIFICADOS E RELATÓRIOS ---
CREATE TABLE IF NOT EXISTS Documentacao (
    codigo            INT          NOT NULL AUTO_INCREMENT,
    nome              VARCHAR(100) NOT NULL,
    tipo              VARCHAR(60)  NOT NULL,
    dataDocumento     DATE         NOT NULL,
    dataValidade      DATE,
    ficheiro          VARCHAR(255), -- Caminho/Path para o arquivo guardado no servidor
    ativo             TINYINT(1)   NOT NULL DEFAULT 1,
    codigoEquipamento INT          NOT NULL,
    codigoFornecedor  INT,
    CONSTRAINT pkDocumentacaocodigo      PRIMARY KEY (codigo),
    CONSTRAINT chkDocumentacaotipo       CHECK (LOWER(tipo) IN ('manual de utilizador', 'manual de servico', 'certificado de calibracao', 'contrato de manutencao', 'fatura ou guia de aquisicao', 'declaracao de conformidade', 'relatorio tecnico')),
    -- Regra de negócio: a data de validade não pode ser anterior à data em que o documento foi emitido
    CONSTRAINT chkDocumentacaovalidade   CHECK (dataValidade IS NULL OR dataValidade >= dataDocumento),
    CONSTRAINT chkDocumentacaoativo      CHECK (ativo IN (0, 1)),
    CONSTRAINT fkDocumentacaoEquipamento FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkDocumentacaoFornecedor  FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- --- CONTRATOS E PRAZOS DE GARANTIA ---
CREATE TABLE IF NOT EXISTS Garantia (
    codigo              INT          NOT NULL AUTO_INCREMENT,
    dataInicio          DATE         NOT NULL,
    dataFim             DATE         NOT NULL,
    temContrato         BOOLEAN      NOT NULL DEFAULT FALSE,
    tipoContrato        VARCHAR(50),
    entidadeResponsavel VARCHAR(100),
    periodicidade       VARCHAR(30), -- Intervalo das revisões preventivas obrigatórias
    observacoes         TEXT,
    codigoEquipamento   INT          NOT NULL,
    codigoFornecedor    INT,
    CONSTRAINT pkGarantiacodigo         PRIMARY KEY (codigo),
    CONSTRAINT chkGarantiadatas         CHECK (dataFim > dataInicio),
    CONSTRAINT chkGarantiaperiodicidade CHECK (periodicidade IS NULL OR LOWER(periodicidade) IN ('trimestral', 'semestral', 'anual')),
    CONSTRAINT fkGarantiaEquipamento    FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkGarantiaFornecedor     FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- --- DINAMIZAÇÃO DA PLATAFORMA ---
-- Sistema de chave-valor para gerir os textos da área pública dinamicamente através do painel de Admin
CREATE TABLE IF NOT EXISTS ConteudoPublico (
    codigo           INT          NOT NULL AUTO_INCREMENT,
    chave            VARCHAR(100) NOT NULL,
    valor            TEXT         NOT NULL,
    dataAtualizacao  DATE         NOT NULL,
    codigoUtilizador INT          NOT NULL,
    CONSTRAINT pkConteudoPublicocodigo PRIMARY KEY (codigo),
    CONSTRAINT uqConteudoPublicochave  UNIQUE (chave),
    CONSTRAINT fkConteudoUtilizador    FOREIGN KEY (codigoUtilizador) REFERENCES Utilizador (codigo)
);

-- --- AUDITORIA E SEGURANÇA (LOGS) ---
-- Registo estrito de ações críticas efetuadas na plataforma (alimentado apenas pelo PHP, sem deletes)
CREATE TABLE IF NOT EXISTS Log (
    codigo           INT          NOT NULL AUTO_INCREMENT,
    tipo             VARCHAR(50)  NOT NULL, -- Ex: LOGIN, INSERT, UPDATE, DELETE
    descricao        VARCHAR(255) NOT NULL,
    dataHora         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codigoUtilizador INT, -- Pode ser NULL caso o utilizador não consiga efetuar login (erro de autenticação)
    CONSTRAINT pkLogcodigo     PRIMARY KEY (codigo),
    CONSTRAINT fkLogUtilizador FOREIGN KEY (codigoUtilizador) REFERENCES Utilizador (codigo)
);

-- --- COMUNICAÇÃO EXTERNA ---
-- Guarda as submissões enviadas através do formulário de contacto da Landing Page pública
CREATE TABLE IF NOT EXISTS MensagemContacto (
    codigo    INT          NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL,
    mensagem  TEXT         NOT NULL,
    dataEnvio DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida      TINYINT(1)   NOT NULL DEFAULT 0, -- Permite ao técnico/admin marcar mensagens resolvidas no backoffice
    CONSTRAINT pkMensagemContactocodigo PRIMARY KEY (codigo),
    CONSTRAINT chkMensagemContactoemail CHECK (email LIKE '%@%.%'),
    CONSTRAINT chkMensagemContactonome  CHECK (LENGTH(TRIM(nome)) >= 2),
    CONSTRAINT chkMensagemContactolida  CHECK (lida IN (0, 1))
);

-- Reativa as validações de chaves estrangeiras globalmente na base de dados
SET FOREIGN_KEY_CHECKS = 1;