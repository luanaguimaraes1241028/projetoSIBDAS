-- MedCore Inventory — Script de criação da base de dados
-- Gerado em: 2026-06-23

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Tabela: Utilizador
-- ----------------------------
CREATE TABLE IF NOT EXISTS Utilizador (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    nome     VARCHAR(100) NOT NULL,
    email    VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil   VARCHAR(25)  NOT NULL DEFAULT 'tecnico',
    CONSTRAINT pkUtilizadorcodigo  PRIMARY KEY (codigo),
    CONSTRAINT uqUtilizadoremail   UNIQUE (email),
    CONSTRAINT chkUtilizadorperfil  CHECK (LOWER(perfil) IN ('admin', 'tecnico', 'profissional de saude')),
    CONSTRAINT chkUtilizadoremail   CHECK (email LIKE '%@%.%'),
    CONSTRAINT chkUtilizadornome    CHECK (LENGTH(TRIM(nome)) >= 2),
    CONSTRAINT chkUtilizadorpassword CHECK (LENGTH(password) >= 60)
);

-- ----------------------------
-- Tabela: Categoria
-- ----------------------------
CREATE TABLE IF NOT EXISTS Categoria (
    codigo    INT         NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(50) NOT NULL,
    descricao TEXT,
    CONSTRAINT pkCategoriacodigo PRIMARY KEY (codigo),
    CONSTRAINT uqCategorianome   UNIQUE (nome),
    CONSTRAINT chkCategorianome  CHECK (LENGTH(TRIM(nome)) >= 2)
);

-- ----------------------------
-- Tabela: Localizacao
-- ----------------------------
CREATE TABLE IF NOT EXISTS Localizacao (
    codigo   INT          NOT NULL AUTO_INCREMENT,
    edificio VARCHAR(100) NOT NULL,
    piso     VARCHAR(10),
    servico  VARCHAR(100) NOT NULL,
    sala     VARCHAR(50),
    ativo    TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT pkLocalizacaocodigo  PRIMARY KEY (codigo),
    CONSTRAINT chkLocalizacaoativo  CHECK (ativo IN (0, 1)),
    CONSTRAINT chkLocalizacaoedificio CHECK (LENGTH(TRIM(edificio)) >= 2),
    CONSTRAINT chkLocalizacaoservico  CHECK (LENGTH(TRIM(servico)) >= 2)
);

-- ----------------------------
-- Tabela: Fornecedor
-- ----------------------------
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
    CONSTRAINT pkFornecedorcodigo  PRIMARY KEY (codigo),
    CONSTRAINT chkFornecedortipo   CHECK (LOWER(tipoFornecedor) IN ('fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis')),
    CONSTRAINT chkFornecedornome   CHECK (LENGTH(TRIM(nome)) >= 2),
    CONSTRAINT chkFornecedornif      CHECK (nif IS NULL OR nif REGEXP '^[0-9]{9}$'),
    CONSTRAINT chkFornecedoremail    CHECK (email IS NULL OR email LIKE '%@%.%'),
    CONSTRAINT chkFornecedortelefone CHECK (telefone IS NULL OR telefone REGEXP '^[0-9]{9}$'),
    CONSTRAINT chkFornecedorativo    CHECK (ativo IN (0, 1))
);

-- ----------------------------
-- Tabela: Equipamento
-- ----------------------------
CREATE TABLE IF NOT EXISTS Equipamento (
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
    tipoEntrada       VARCHAR(20)   NOT NULL,
    estado            VARCHAR(20)   NOT NULL,
    criticidade       VARCHAR(20)   NOT NULL,
    observacoes       TEXT,
    codigoCategoria   INT           NOT NULL,
    codigoLocalizacao INT,
    CONSTRAINT pkEquipamentocodigo                PRIMARY KEY (codigo),
    CONSTRAINT uqEquipamentocodigoInterno         UNIQUE (codigoInterno),
    CONSTRAINT uqEquipamentoserieFabricanteModelo UNIQUE (numeroSerie, fabricante, modelo),
    CONSTRAINT chkEquipamentotipoEntrada          CHECK (LOWER(tipoEntrada) IN ('compra', 'doacao', 'aluguer', 'emprestimo')),
    CONSTRAINT chkEquipamentoestado               CHECK (LOWER(estado) IN ('ativo', 'em manutencao', 'inativo', 'em calibracao', 'em quarentena', 'abatido')),
    CONSTRAINT chkEquipamentocriticidade          CHECK (LOWER(criticidade) IN ('baixa', 'media', 'alta', 'suporte de vida')),
    CONSTRAINT chkEquipamentoanoFabrico           CHECK (anoFabrico IS NULL OR (anoFabrico >= 1900 AND anoFabrico <= 2100)),
    CONSTRAINT chkEquipamentocustoAquisicao       CHECK (custoAquisicao IS NULL OR custoAquisicao >= 0),
    CONSTRAINT fkEquipamentoCategoria             FOREIGN KEY (codigoCategoria)   REFERENCES Categoria (codigo),
    CONSTRAINT fkEquipamentoLocalizacao           FOREIGN KEY (codigoLocalizacao) REFERENCES Localizacao (codigo)
);

-- ----------------------------
-- Tabela: EquipamentoFornecedor
-- ----------------------------
CREATE TABLE IF NOT EXISTS EquipamentoFornecedor (
    codigoEquipamento INT NOT NULL,
    codigoFornecedor  INT NOT NULL,
    CONSTRAINT pkEquipamentoFornecedor PRIMARY KEY (codigoEquipamento, codigoFornecedor),
    CONSTRAINT fkEqFornEquipamento     FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkEqFornFornecedor      FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- ----------------------------
-- Tabela: Documentacao
-- ----------------------------
CREATE TABLE IF NOT EXISTS Documentacao (
    codigo            INT          NOT NULL AUTO_INCREMENT,
    nome              VARCHAR(100) NOT NULL,
    tipo              VARCHAR(60)  NOT NULL,
    dataDocumento     DATE         NOT NULL,
    dataValidade      DATE,
    ficheiro          VARCHAR(255),
    ativo             TINYINT(1)   NOT NULL DEFAULT 1,
    codigoEquipamento INT          NOT NULL,
    codigoFornecedor  INT,
    CONSTRAINT pkDocumentacaocodigo       PRIMARY KEY (codigo),
    CONSTRAINT chkDocumentacaotipo        CHECK (LOWER(tipo) IN ('manual de utilizador', 'manual de servico', 'certificado de calibracao', 'contrato de manutencao', 'fatura ou guia de aquisicao', 'declaracao de conformidade', 'relatorio tecnico')),
    CONSTRAINT chkDocumentacaovalidade    CHECK (dataValidade IS NULL OR dataValidade >= dataDocumento),
    CONSTRAINT chkDocumentacaoativo       CHECK (ativo IN (0, 1)),
    CONSTRAINT fkDocumentacaoEquipamento  FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkDocumentacaoFornecedor   FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- ----------------------------
-- Tabela: Garantia
-- ----------------------------
CREATE TABLE IF NOT EXISTS Garantia (
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
    CONSTRAINT pkGarantiacodigo       PRIMARY KEY (codigo),
    CONSTRAINT chkGarantiadatas       CHECK (dataFim > dataInicio),
    CONSTRAINT chkGarantiaperiodicidade CHECK (periodicidade IS NULL OR LOWER(periodicidade) IN ('trimestral', 'semestral', 'anual')),
    CONSTRAINT fkGarantiaEquipamento  FOREIGN KEY (codigoEquipamento) REFERENCES Equipamento (codigo),
    CONSTRAINT fkGarantiaFornecedor   FOREIGN KEY (codigoFornecedor)  REFERENCES Fornecedor (codigo)
);

-- ----------------------------
-- Tabela: ConteudoPublico
-- ----------------------------
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

-- ----------------------------
-- Tabela: Log
-- ----------------------------
CREATE TABLE IF NOT EXISTS Log (
    codigo           INT          NOT NULL AUTO_INCREMENT,
    tipo             VARCHAR(50)  NOT NULL,
    descricao        VARCHAR(255) NOT NULL,
    dataHora         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codigoUtilizador INT,
    CONSTRAINT pkLogcodigo     PRIMARY KEY (codigo),
    CONSTRAINT fkLogUtilizador FOREIGN KEY (codigoUtilizador) REFERENCES Utilizador (codigo)
);

-- ----------------------------
-- Tabela: MensagemContacto
-- ----------------------------
CREATE TABLE IF NOT EXISTS MensagemContacto (
    codigo    INT          NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL,
    mensagem  TEXT         NOT NULL,
    dataEnvio DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida      TINYINT(1)   NOT NULL DEFAULT 0,
    CONSTRAINT pkMensagemContactocodigo PRIMARY KEY (codigo),
    CONSTRAINT chkMensagemContactoemail CHECK (email LIKE '%@%.%'),
    CONSTRAINT chkMensagemContactonome  CHECK (LENGTH(TRIM(nome)) >= 2),
    CONSTRAINT chkMensagemContactolida  CHECK (lida IN (0, 1))
);

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------
-- Dados iniciais: Equipamentos
-- ----------------------------
INSERT INTO Equipamento (codigoInterno, designacao, marca, modelo, fabricante, numeroSerie, anoFabrico, dataAquisicao, custoAquisicao, tipoEntrada, estado, criticidade, observacoes, codigoCategoria, codigoLocalizacao) VALUES
('EQ-0005', 'Desfibrilhador Externo Automático',   'Philips',         'HeartStart FRx',   'Philips Healthcare',      'SN-PHL-55321-D', 2021, '2022-03-15', 3200.00,   'compra', 'ativo',         'suporte de vida', NULL,                                   2, 1),
('EQ-0006', 'Bomba De Infusão Volumétrica',         'Fresenius',       'Agilia VP',        'Fresenius Kabi',          'SN-FRK-22109-A', 2020, '2020-11-10', 4800.00,   'compra', 'ativo',         'alta',            NULL,                                   3, 2),
('EQ-0007', 'Analisador De Gases Sanguíneos',       'Radiometer',      'ABL90 FLEX',       'Radiometer Medical',      'SN-RAD-11205-B', 2019, '2019-06-20', 28500.00,  'compra', 'ativo',         'alta',            NULL,                                   5, 5),
('EQ-0008', 'Autoclave De Vapor',                   'Tuttnauer',       '3870EHA',          'Tuttnauer Europe',        'SN-TUT-33087-C', 2018, '2018-09-01', 12000.00,  'compra', 'ativo',         'media',           NULL,                                   6, 3),
('EQ-0009', 'Aparelho De Raio-X Portátil',          'Siemens',         'Mobilett Mira',    'Siemens Healthineers',    'SN-SIE-44321-F', 2022, '2022-07-15', 85000.00,  'compra', 'ativo',         'alta',            NULL,                                   4, 4),
('EQ-0010', 'Monitor De ECG 12 Derivações',         'Nihon Kohden',    'ECP-2350',         'Nihon Kohden Corp',       'SN-NKH-66132-A', 2021, '2021-04-20', 7500.00,   'compra', 'ativo',         'alta',            NULL,                                   1, 1),
('EQ-0011', 'Ventilador De Transporte',             'Dräger',          'Oxylog 3000',      'Dräger Medical',          'SN-DRG-77421-Y', 2020, '2020-08-10', 22000.00,  'compra', 'ativo',         'suporte de vida', NULL,                                   2, 2),
('EQ-0012', 'Bomba De Seringa',                     'B. Braun',        'Perfusor Space',   'B. Braun Melsungen',      'SN-BBR-88543-G', 2021, '2021-10-05', 3100.00,   'compra', 'ativo',         'alta',            NULL,                                   3, 2),
('EQ-0013', 'Eletroencefalógrafo Digital',          'Natus',           'Quantum EEG',      'Natus Medical',           'SN-NAT-99012-H', 2019, '2019-12-15', 45000.00,  'compra', 'em manutencao', 'alta',            'Substituição de eléctrodos agendada.', 4, 2),
('EQ-0014', 'Centrifugadora De Bancada',            'Eppendorf',       '5702 R',           'Eppendorf AG',            'SN-EPP-10321-I', 2020, '2020-03-25', 3800.00,   'compra', 'ativo',         'media',           NULL,                                   5, 5),
('EQ-0015', 'Máquina De Anestesia',                 'Dräger',          'Fabius Plus',      'Dräger Medical',          'SN-DRG-11432-Z', 2022, '2022-05-30', 65000.00,  'compra', 'ativo',         'suporte de vida', NULL,                                   2, 3),
('EQ-0016', 'Coluna De Laparoscopia',               'Karl Storz',      'AIDA Hub',         'Karl Storz GmbH',         'SN-KST-22543-J', 2021, '2021-09-15', 120000.00, 'compra', 'ativo',         'alta',            NULL,                                   3, 3),
('EQ-0017', 'Nebulizador Ultrassónico',             'Omron',           'NE-U17',           'Omron Healthcare',        'SN-OMR-33654-K', 2023, '2023-01-20', 280.00,    'compra', 'ativo',         'baixa',           NULL,                                   3, 1),
('EQ-0018', 'Eletrocardiógrafo Portátil',           'Schiller',        'AT-102',           'Schiller AG',             'SN-SCH-44765-L', 2022, '2022-11-10', 5200.00,   'compra', 'ativo',         'alta',            NULL,                                   1, 1),
('EQ-0019', 'Aparelho De CPAP',                     'ResMed',          'AirSense 10',      'ResMed Corp',             'SN-RSM-55876-M', 2021, '2021-07-05', 1800.00,   'compra', 'ativo',         'alta',            NULL,                                   2, 2),
('EQ-0020', 'Mesa Cirúrgica Elétrica',              'Maquet',          'Alphastar',        'Maquet GmbH',             'SN-MAQ-66987-N', 2019, '2019-11-20', 95000.00,  'compra', 'ativo',         'alta',            NULL,                                   3, 3),
('EQ-0021', 'Monitor De Pressão Invasiva',          'Spacelabs',       '91369',            'Spacelabs Healthcare',    'SN-SPL-77098-O', 2020, '2020-06-15', 9500.00,   'compra', 'em calibracao', 'alta',            'Calibração semestral em curso.',       1, 2),
('EQ-0022', 'Analisador Bioquímico',                'Beckman Coulter', 'AU480',            'Beckman Coulter Inc',     'SN-BCK-88209-P', 2018, '2018-04-10', 85000.00,  'compra', 'ativo',         'alta',            NULL,                                   5, 5),
('EQ-0023', 'Cadeira De Reabilitação Motorizada',   'Permobil',        'M3 Corpus',        'Permobil AB',             'SN-PRM-99310-Q', 2023, '2023-03-15', 12500.00,  'compra', 'ativo',         'media',           NULL,                                   7, 1),
('EQ-0024', 'Hemoglobinómetro Portátil',            'HemoCue',         'Hb 201+',          'HemoCue AB',              'SN-HMC-10421-R', 2022, '2022-09-20', 1200.00,   'compra', 'ativo',         'media',           NULL,                                   5, 5);

INSERT INTO Garantia (dataInicio, dataFim, temContrato, tipoContrato, entidadeResponsavel, periodicidade, codigoEquipamento) VALUES
('2022-03-15', '2025-03-15', 1, 'manutencao preventiva', 'Philips Healthcare Portugal',   'anual',     (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0005')),
('2020-11-10', '2023-11-10', 0, NULL,                    NULL,                            NULL,        (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0006')),
('2019-06-20', '2024-06-20', 1, 'manutencao preventiva', 'Radiometer Portugal',           'semestral', (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0007')),
('2018-09-01', '2023-09-01', 1, 'manutencao preventiva', 'Tuttnauer Service',             'anual',     (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0008')),
('2022-07-15', '2025-07-15', 1, 'manutencao preventiva', 'Siemens Healthineers Portugal', 'semestral', (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0009')),
('2022-05-30', '2027-05-30', 1, 'manutencao preventiva', 'Dräger Portugal',               'semestral', (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0015')),
('2021-09-15', '2026-09-15', 1, 'manutencao preventiva', 'Karl Storz Portugal',           'anual',     (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0016')),
('2022-11-10', '2025-11-10', 0, NULL,                    NULL,                            NULL,        (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0018')),
('2019-11-20', '2024-11-20', 1, 'manutencao preventiva', 'Maquet Service GmbH',           'anual',     (SELECT codigo FROM Equipamento WHERE codigoInterno = 'EQ-0020'));
