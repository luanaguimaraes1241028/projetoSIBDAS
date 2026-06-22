-- =============================================================
-- MedCore - Base de Dados
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ConteudoPublico;
DROP TABLE IF EXISTS Documentacao;
DROP TABLE IF EXISTS Garantia;
DROP TABLE IF EXISTS EquipamentoFornecedor;
DROP TABLE IF EXISTS Equipamento;
DROP TABLE IF EXISTS Fornecedor;
DROP TABLE IF EXISTS Localizacao;
DROP TABLE IF EXISTS Categoria;
DROP TABLE IF EXISTS Utilizador;

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
-- Utilizador
-- -------------------------------------------------------------
CREATE TABLE Utilizador (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    nome     VARCHAR(100) NOT NULL,
    email    VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil   VARCHAR(25)  NOT NULL DEFAULT 'tecnico'
        CHECK (LOWER(perfil) IN ('admin', 'tecnico', 'profissional de saude')),
    CONSTRAINT pkUtilizadorcodigo PRIMARY KEY (codigo),
    CONSTRAINT uqUtilizadoremail  UNIQUE (email)
);

-- -------------------------------------------------------------
-- Categoria
-- -------------------------------------------------------------
CREATE TABLE Categoria (
    codigo    INT         NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(50) NOT NULL,
    descricao TEXT,
    CONSTRAINT pkCategoriacodigo PRIMARY KEY (codigo),
    CONSTRAINT uqCategorianome   UNIQUE (nome)
);

-- -------------------------------------------------------------
-- Localizacao
-- -------------------------------------------------------------
CREATE TABLE Localizacao (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    edificio VARCHAR(100) NOT NULL,
    piso     VARCHAR(10),
    servico  VARCHAR(100) NOT NULL,
    sala     VARCHAR(50),
    ativo    TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT pkLocalizacaocodigo PRIMARY KEY (codigo)
);

-- -------------------------------------------------------------
-- Fornecedor
-- -------------------------------------------------------------
CREATE TABLE Fornecedor (
    codigo          INT          NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(100) NOT NULL,
    nif             VARCHAR(20),
    telefone        VARCHAR(20),
    email           VARCHAR(100),
    morada          VARCHAR(200),
    website         VARCHAR(100),
    pessoaContacto  VARCHAR(100),
    telefonePessoa  VARCHAR(20),
    tipoFornecedor  VARCHAR(30)  NOT NULL
        CHECK (LOWER(tipoFornecedor) IN ('fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis')),
    observacoes     TEXT,
    CONSTRAINT pkFornecedorcodigo PRIMARY KEY (codigo)
);

-- -------------------------------------------------------------
-- Equipamento
-- -------------------------------------------------------------
CREATE TABLE Equipamento (
    codigo            INT           NOT NULL AUTO_INCREMENT,
    codigoInterno     VARCHAR(20)   NOT NULL,
    designacao        VARCHAR(100)  NOT NULL,
    marca             VARCHAR(50)   NOT NULL,
    modelo            VARCHAR(50)   NOT NULL,
    fabricante        VARCHAR(100),
    numeroSerie       VARCHAR(50),
    anoFabrico        INT,
    dataAquisicao     DATE,
    custoAquisicao    DECIMAL(10,2),
    tipoEntrada       VARCHAR(20)   NOT NULL
        CHECK (LOWER(tipoEntrada) IN ('compra', 'doacao', 'aluguer', 'emprestimo')),
    estado            VARCHAR(20)   NOT NULL
        CHECK (LOWER(estado) IN ('ativo', 'em manutencao', 'inativo', 'em calibracao', 'em quarentena', 'abatido')),
    criticidade       VARCHAR(20)   NOT NULL
        CHECK (LOWER(criticidade) IN ('baixa', 'media', 'alta', 'suporte de vida')),
    observacoes       TEXT,
    codigoCategoria   INT           NOT NULL,
    codigoLocalizacao INT,
    CONSTRAINT pkEquipamentocodigo                PRIMARY KEY (codigo),
    CONSTRAINT uqEquipamentocodigoInterno         UNIQUE (codigoInterno),
    CONSTRAINT uqEquipamentoserieFabricanteModelo UNIQUE (numeroSerie, fabricante, modelo),
    CONSTRAINT fkEquipamentoCategoria             FOREIGN KEY (codigoCategoria)   REFERENCES Categoria (codigo),
    CONSTRAINT fkEquipamentoLocalizacao           FOREIGN KEY (codigoLocalizacao) REFERENCES Localizacao (codigo)
);

-- -------------------------------------------------------------
-- EquipamentoFornecedor
-- -------------------------------------------------------------
CREATE TABLE EquipamentoFornecedor (
    codigoEquipamento INT NOT NULL,
    codigoFornecedor  INT NOT NULL,
    CONSTRAINT pkEquipamentoFornecedor PRIMARY KEY (codigoEquipamento, codigoFornecedor),
    CONSTRAINT fkEqFornEquipamento     FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkEqFornFornecedor      FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- -------------------------------------------------------------
-- Garantia
-- -------------------------------------------------------------
CREATE TABLE Garantia (
    codigo              INT          NOT NULL AUTO_INCREMENT,
    dataInicio          DATE         NOT NULL,
    dataFim             DATE         NOT NULL,
    temContrato         BOOLEAN      NOT NULL DEFAULT FALSE,
    tipoContrato        VARCHAR(50),
    entidadeResponsavel VARCHAR(100),
    periodicidade       VARCHAR(30),
    observacoes         TEXT,
    codigoEquipamento   INT          NOT NULL,
    codigoFornecedor    INT,
    CONSTRAINT pkGarantiacodigo      PRIMARY KEY (codigo),
    CONSTRAINT fkGarantiaEquipamento FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkGarantiaFornecedor  FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- -------------------------------------------------------------
-- Documentacao
-- -------------------------------------------------------------
CREATE TABLE Documentacao (
    codigo            INT          NOT NULL AUTO_INCREMENT,
    nome              VARCHAR(100) NOT NULL,
    tipo              VARCHAR(60)  NOT NULL
        CHECK (LOWER(tipo) IN ('manual de utilizador', 'manual de servico', 'certificado de calibracao',
                               'contrato de manutencao', 'fatura ou guia de aquisicao',
                               'declaracao de conformidade', 'relatorio tecnico')),
    dataDocumento     DATE         NOT NULL,
    dataValidade      DATE,
    ficheiro          VARCHAR(255),
    codigoEquipamento INT          NOT NULL,
    codigoFornecedor  INT,
    CONSTRAINT pkDocumentacaocodigo      PRIMARY KEY (codigo),
    CONSTRAINT fkDocumentacaoEquipamento FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkDocumentacaoFornecedor  FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- -------------------------------------------------------------
-- ConteudoPublico
-- -------------------------------------------------------------
CREATE TABLE ConteudoPublico (
    codigo           INT          NOT NULL AUTO_INCREMENT,
    chave            VARCHAR(100) NOT NULL,
    valor            TEXT         NOT NULL,
    dataAtualizacao  DATE         NOT NULL,
    codigoUtilizador INT          NOT NULL,
    CONSTRAINT pkConteudoPublicocodigo PRIMARY KEY (codigo),
    CONSTRAINT uqConteudoPublicochave  UNIQUE (chave),
    CONSTRAINT fkConteudoUtilizador    FOREIGN KEY (codigoUtilizador) REFERENCES Utilizador (codigo)
);

-- =============================================================
-- DADOS DE TESTE
-- =============================================================

-- Utilizadores
-- Passwords: admin=123456 | tecnico=654321 | profissional=prof123
INSERT INTO Utilizador (nome, email, password, perfil) VALUES
('Administrador',         'admin@medcore.pt',        '$2y$10$RF0QrvXIFmPz5/5cAbgqxeHF3Hg2XacnDtfsesg6O3Rofi6HDdnSq', 'admin'),
('Técnico',               'tecnico@medcore.pt',      '$2y$10$rsH/GqszRzySyTBslcP5FeGPS0Et/uzSodLv7M.tnWLDOU8N2BkD6', 'tecnico'),
('Profissional de Saúde', 'profissional@medcore.pt', '$2y$10$FuFVulDJ1nfhDZLZGK7uKuol5pciTBL.JDLf9VLRONJV27d66Icv2', 'profissional de saude');

-- Categorias
INSERT INTO Categoria (nome, descricao) VALUES
('Monitorização',   'Equipamentos utilizados para monitorizar sinais fisiológicos do doente'),
('Suporte de vida', 'Equipamentos essenciais para manter funções vitais'),
('Terapia',         'Equipamentos utilizados para tratamento'),
('Diagnóstico',     'Equipamentos utilizados para avaliação clínica ou imagiologia'),
('Laboratório',     'Equipamentos usados em análises clínicas'),
('Esterilização',   'Equipamentos utilizados no processamento de dispositivos médicos'),
('Reabilitação',    'Equipamentos usados em fisioterapia ou recuperação funcional');

-- Localizações
INSERT INTO Localizacao (edificio, piso, servico, sala) VALUES
('Bloco A', 'Piso 1', 'Urgência',                             'Sala de Triagem'),
('Bloco A', 'Piso 2', 'UCI - Unidade de Cuidados Intensivos', 'UCIP-01'),
('Bloco B', 'Piso 0', 'Bloco Operatório',                     'BO-03'),
('Bloco C', 'Piso 1', 'Radiologia',                           'Sala de Raio-X'),
('Bloco D', 'Piso 0', 'Laboratório de Análises Clínicas',     'LAC-01');

-- Equipamentos
INSERT INTO Equipamento (codigoInterno, designacao, marca, modelo, fabricante, numeroSerie, anoFabrico, dataAquisicao, custoAquisicao, tipoEntrada, estado, criticidade, observacoes, codigoCategoria, codigoLocalizacao) VALUES
('EQ-0001', 'Monitor De Sinais Vitais', 'Philips', 'IntelliVue MX450', 'Philips Healthcare',  'SN-PHL-10023-A', 2022, '2022-03-15', 12500.00, 'compra', 'ativo',         'alta',            'Calibrado na receção. Uso exclusivo na UCIP.',           1, 2),
('EQ-0002', 'Ventilador Volumétrico',   'Dräger',  'Evita V500',       'Dräger Medical GmbH', 'SN-DRG-88321-X', 2023, '2023-06-01', 24500.00, 'compra', 'ativo',         'suporte de vida', 'Unidade principal do bloco de cuidados intensivos.',      2, 2),
('EQ-0003', 'Bisturi Elétrico',         'Erbe',    'VIO 300 D',        'Erbe Elektromedizin', 'SN-ERB-44210-B', 2021, '2021-09-10',  8900.00, 'compra', 'em manutencao', 'alta',            'Manutenção preventiva agendada para 2026-07-01.',         3, 3),
('EQ-0004', 'Ecógrafo Portátil',        'GE',      'Vivid IQ',         'GE HealthCare',       'SN-GEH-33109-C', 2023, '2023-11-20', 35000.00, 'compra', 'ativo',         'media',           'Equipamento partilhado entre Radiologia e Urgência.',    4, 4);

-- Garantias
INSERT INTO Garantia (dataInicio, dataFim, temContrato, tipoContrato, entidadeResponsavel, periodicidade, observacoes, codigoEquipamento) VALUES
('2022-03-15', '2025-03-15', TRUE,  'Preventiva e Corretiva', 'Philips Healthcare Portugal', 'Anual',     'Inclui peças e mão de obra.', 1),
('2023-06-01', '2026-06-01', TRUE,  'Full Risk',               'Dräger Portugal Lda.',        'Semestral', 'Inclui firmware e calibrações.', 2),
('2021-09-10', '2024-09-10', FALSE, NULL,                      NULL,                          NULL,        NULL, 3),
('2023-11-20', '2026-11-20', TRUE,  'Preventiva',              'GE HealthCare Portugal',      'Anual',     NULL, 4);
