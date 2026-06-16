-- =============================================================
-- MedCore - Sistema de Gestão de Inventário Hospitalar
-- DDL gerado a partir de medcore.dbml
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- Utilizador
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Utilizador` (
  `codigo`   INT          NOT NULL AUTO_INCREMENT,
  `nome`     VARCHAR(100) NOT NULL,
  `email`    VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `perfil`   VARCHAR(20)  NOT NULL DEFAULT 'tecnico',
  CONSTRAINT `pkUtilizadorcodigo`  PRIMARY KEY (`codigo`),
  CONSTRAINT `uqUtilizadoremail`   UNIQUE (`email`),
  CONSTRAINT `chkUtilizadorperfil` CHECK (LOWER(`perfil`) IN ('admin', 'tecnico'))
);

-- -------------------------------------------------------------
-- Categoria
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Categoria` (
  `codigo`    INT         NOT NULL AUTO_INCREMENT,
  `nome`      VARCHAR(50) NOT NULL,
  `descricao` TEXT,
  CONSTRAINT `pkCategoriacodigo` PRIMARY KEY (`codigo`),
  CONSTRAINT `uqCategorianome`   UNIQUE (`nome`)
);

-- -------------------------------------------------------------
-- Localizacao
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Localizacao` (
  `codigo`   INT          NOT NULL AUTO_INCREMENT,
  `edificio` VARCHAR(100) NOT NULL,
  `piso`     VARCHAR(10),
  `servico`  VARCHAR(100) NOT NULL,
  `sala`     VARCHAR(50),
  CONSTRAINT `pkLocalizacaocodigo` PRIMARY KEY (`codigo`)
);

-- -------------------------------------------------------------
-- Fornecedor
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Fornecedor` (
  `codigo`          INT          NOT NULL AUTO_INCREMENT,
  `nome`            VARCHAR(100) NOT NULL,
  `nif`             VARCHAR(20),
  `telefone`        VARCHAR(20),
  `email`           VARCHAR(100),
  `morada`          VARCHAR(200),
  `website`         VARCHAR(100),
  `pessoaContacto`  VARCHAR(100),
  `telefonePessoa`  VARCHAR(20),
  `tipoFornecedor`  VARCHAR(30)  NOT NULL,
  `observacoes`     TEXT,
  CONSTRAINT `pkFornecedorcodigo`          PRIMARY KEY (`codigo`),
  CONSTRAINT `chkFornecedortipoFornecedor` CHECK (LOWER(`tipoFornecedor`) IN ('fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis'))
);

-- -------------------------------------------------------------
-- Equipamento
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Equipamento` (
  `codigo`            INT            NOT NULL AUTO_INCREMENT,
  `codigoInterno`     VARCHAR(20)    NOT NULL,
  `designacao`        VARCHAR(100)   NOT NULL,
  `marca`             VARCHAR(50)    NOT NULL,
  `modelo`            VARCHAR(50)    NOT NULL,
  `fabricante`        VARCHAR(100),
  `numeroSerie`       VARCHAR(50),
  `anoFabrico`        INT,
  `dataAquisicao`     DATE,
  `custoAquisicao`    DECIMAL(10,2),
  `tipoEntrada`       VARCHAR(20)    NOT NULL,
  `estado`            VARCHAR(20)    NOT NULL,
  `criticidade`       VARCHAR(20)    NOT NULL,
  `observacoes`       TEXT,
  `codigoCategoria`   INT            NOT NULL,
  `codigoLocalizacao` INT            NOT NULL,
  CONSTRAINT `pkEquipamentocodigo`               PRIMARY KEY (`codigo`),
  CONSTRAINT `uqEquipamentocodigoInterno`         UNIQUE (`codigoInterno`),
  CONSTRAINT `uqEquipamentoserieFabricanteModelo` UNIQUE (`numeroSerie`, `fabricante`, `modelo`),
  CONSTRAINT `chkEquipamentotipoEntrada` CHECK (LOWER(`tipoEntrada`) IN ('compra', 'doacao', 'aluguer', 'emprestimo')),
  CONSTRAINT `chkEquipamentoestado`      CHECK (LOWER(`estado`)      IN ('ativo', 'em manutencao', 'inativo', 'em calibracao', 'em quarentena', 'abatido')),
  CONSTRAINT `chkEquipamentocriticidade` CHECK (LOWER(`criticidade`) IN ('baixa', 'media', 'alta', 'suporte de vida')),
  CONSTRAINT `fkEquipamentoCategoria_Categoria`     FOREIGN KEY (`codigoCategoria`)   REFERENCES `Categoria`   (`codigo`),
  CONSTRAINT `fkEquipamentoLocalizacao_Localizacao` FOREIGN KEY (`codigoLocalizacao`) REFERENCES `Localizacao` (`codigo`)
);

-- -------------------------------------------------------------
-- EquipamentoFornecedor  (tabela de associação N:M)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `EquipamentoFornecedor` (
  `codigoEquipamento` INT NOT NULL,
  `codigoFornecedor`  INT NOT NULL,
  CONSTRAINT `pkEquipamentoFornecedoredificiocodigoEquipamentocodigoFornecedor`
             PRIMARY KEY (`codigoEquipamento`, `codigoFornecedor`),
  CONSTRAINT `fkEqFornEquipamento_Equipamento` FOREIGN KEY (`codigoEquipamento`) REFERENCES `Equipamento` (`codigo`),
  CONSTRAINT `fkEqFornFornecedor_Fornecedor`   FOREIGN KEY (`codigoFornecedor`)  REFERENCES `Fornecedor`  (`codigo`)
);

-- -------------------------------------------------------------
-- Documentacao
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Documentacao` (
  `codigo`            INT          NOT NULL AUTO_INCREMENT,
  `nome`              VARCHAR(100) NOT NULL,
  `tipo`              VARCHAR(60)  NOT NULL,
  `dataDocumento`     DATE         NOT NULL,
  `dataValidade`      DATE,
  `ficheiro`          VARCHAR(255),
  `codigoEquipamento` INT          NOT NULL,
  `codigoFornecedor`  INT,
  CONSTRAINT `pkDocumentacaocodigo` PRIMARY KEY (`codigo`),
  CONSTRAINT `chkDocumentacaotipo`  CHECK (LOWER(`tipo`) IN (
    'manual de utilizador', 'manual de servico', 'certificado de calibracao',
    'contrato de manutencao', 'fatura ou guia de aquisicao',
    'declaracao de conformidade', 'relatorio tecnico'
  )),
  CONSTRAINT `fkDocumentacaoEquipamento_Equipamento` FOREIGN KEY (`codigoEquipamento`) REFERENCES `Equipamento` (`codigo`),
  CONSTRAINT `fkDocumentacaoFornecedor_Fornecedor`   FOREIGN KEY (`codigoFornecedor`)  REFERENCES `Fornecedor`  (`codigo`)
);

-- -------------------------------------------------------------
-- Garantia
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Garantia` (
  `codigo`              INT         NOT NULL AUTO_INCREMENT,
  `dataInicio`          DATE        NOT NULL,
  `dataFim`             DATE        NOT NULL,
  `temContrato`         BOOLEAN     NOT NULL DEFAULT FALSE,
  `tipoContrato`        VARCHAR(50),
  `entidadeResponsavel` VARCHAR(100),
  `periodicidade`       VARCHAR(30),
  `observacoes`         TEXT,
  `codigoEquipamento`   INT         NOT NULL,
  `codigoFornecedor`    INT,
  CONSTRAINT `pkGarantiacodigo` PRIMARY KEY (`codigo`),
  CONSTRAINT `fkGarantiaEquipamento_Equipamento` FOREIGN KEY (`codigoEquipamento`) REFERENCES `Equipamento` (`codigo`),
  CONSTRAINT `fkGarantiaFornecedor_Fornecedor`   FOREIGN KEY (`codigoFornecedor`)  REFERENCES `Fornecedor`  (`codigo`)
);

-- -------------------------------------------------------------
-- ConteudoPublico  (chave-valor: cada campo do formulário = 1 linha)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `ConteudoPublico`;
CREATE TABLE `ConteudoPublico` (
  `codigo`           INT          NOT NULL AUTO_INCREMENT,
  `chave`            VARCHAR(100) NOT NULL,
  `valor`            TEXT         NOT NULL,
  `dataAtualizacao`  DATE         NOT NULL,
  `codigoUtilizador` INT          NOT NULL,
  CONSTRAINT `pkConteudoPublicocodigo`         PRIMARY KEY (`codigo`),
  CONSTRAINT `uqConteudoPublicochave`           UNIQUE (`chave`),
  CONSTRAINT `fkConteudoUtilizador_Utilizador` FOREIGN KEY (`codigoUtilizador`) REFERENCES `Utilizador` (`codigo`)
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- Dados de Teste (Seed)
-- Password de TODOS os utilizadores: password
-- =============================================================

-- Limpar dados existentes (ordem inversa das FK)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `ConteudoPublico`;
TRUNCATE TABLE `Garantia`;
TRUNCATE TABLE `Documentacao`;
TRUNCATE TABLE `EquipamentoFornecedor`;
TRUNCATE TABLE `Equipamento`;
TRUNCATE TABLE `Fornecedor`;
TRUNCATE TABLE `Localizacao`;
TRUNCATE TABLE `Categoria`;
TRUNCATE TABLE `Utilizador`;
SET FOREIGN_KEY_CHECKS = 1;

INSERT IGNORE INTO `Utilizador` (`nome`, `email`, `password`, `perfil`) VALUES
('Administrador Sistema', 'admin@medcore.pt',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uye29tblrm', 'admin'),
('João Silva',            'joao.silva@medcore.pt',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uye29tblrm', 'tecnico'),
('Ana Ferreira',          'ana.ferreira@medcore.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uye29tblrm', 'tecnico');

INSERT IGNORE INTO `Categoria` (`nome`, `descricao`) VALUES
('Ventilação Mecânica',    'Equipamentos de suporte ventilatório e respiratório'),
('Monitorização Cardíaca', 'Monitores de sinais vitais, ECG e desfibrilhadores'),
('Imagiologia',            'Equipamentos de diagnóstico por imagem (Rx, TAC, RM, Ecografia)'),
('Bloco Operatório',       'Equipamentos cirúrgicos, anestésicos e de controlo térmico'),
('Reanimação',             'Equipamentos de emergência e suporte de vida imediato');

INSERT IGNORE INTO `Localizacao` (`edificio`, `piso`, `servico`, `sala`) VALUES
('Edifício Principal',  '2', 'UCIP',             'Sala 201'),
('Edifício Principal',  '1', 'Bloco Operatório', 'Sala BO-1'),
('Edifício B',          '0', 'Radiologia',       'Sala R01'),
('Edifício Principal',  '3', 'Cardiologia',      'Sala 302'),
('Edifício Urgências',  '0', 'Urgência Geral',   'Sala U01');

INSERT IGNORE INTO `Fornecedor` (`nome`, `nif`, `telefone`, `email`, `morada`, `pessoaContacto`, `tipoFornecedor`) VALUES
('Dräger Medical Portugal, Lda.', '501234567', '210 000 100', 'contacto@draeger.pt',   'Av. da Liberdade 110, Lisboa', 'Carlos Mendes',   'fabricante'),
('Philips Healthcare Portugal',   '508765432', '210 000 200', 'healthcare@philips.pt', 'Rua das Flores 45, Porto',      'Maria Costa',     'fabricante'),
('MedTech Services, Unipessoal',  '512345678', '220 000 300', 'servicos@medtech.pt',   'Rua do Comércio 22, Coimbra',  'Paulo Rodrigues', 'assistencia tecnica');

INSERT IGNORE INTO `Equipamento` (`codigoInterno`, `designacao`, `marca`, `modelo`, `fabricante`, `numeroSerie`, `anoFabrico`, `dataAquisicao`, `custoAquisicao`, `tipoEntrada`, `estado`, `criticidade`, `codigoCategoria`, `codigoLocalizacao`) VALUES
('EQ-0001', 'Ventilador Volumétrico Hospitalar', 'Dräger',       'Evita V500',       'Dräger Medical GmbH',      'SN-DRG-88321-X', 2020, '2021-03-15',  45000.00, 'compra', 'ativo',         'suporte de vida', 1, 1),
('EQ-0002', 'Desfibrilhador Bifásico Pro',       'Zoll Medical', 'R Series',         'Zoll Medical Corporation', 'SN-ZOL-11029-M', 2019, '2020-07-20',  18500.00, 'compra', 'ativo',         'alta',            2, 5),
('EQ-0003', 'Monitor de Sinais Vitais',          'Philips',      'IntelliVue MX700', 'Philips Healthcare',        'SN-PHI-55432-A', 2021, '2022-01-10',  32000.00, 'compra', 'ativo',         'alta',            2, 4),
('EQ-0004', 'Arco Cirúrgico (C-Arm)',            'Siemens',      'Cios Flow',        'Siemens Healthineers',      'SN-SIE-74210-B', 2018, '2019-05-02', 120000.00, 'compra', 'em manutencao', 'alta',            3, 2),
('EQ-0005', 'Bisturi Elétrico Monopolar',        'Valleylab',    'FT10',             'Medtronic',                 'SN-VAL-30021-C', 2022, '2022-11-30',   9800.00, 'compra', 'ativo',         'media',           4, 2);

INSERT IGNORE INTO `EquipamentoFornecedor` (`codigoEquipamento`, `codigoFornecedor`) VALUES
(1, 1), (2, 3), (3, 2), (4, 3), (5, 3);

INSERT IGNORE INTO `Documentacao` (`nome`, `tipo`, `dataDocumento`, `dataValidade`, `codigoEquipamento`, `codigoFornecedor`) VALUES
('Manual de Utilizador - Evita V500',                'manual de utilizador',        '2021-03-15', NULL,         1, 1),
('Contrato de Manutenção - Desfibrilhador R Series', 'contrato de manutencao',      '2023-01-01', '2025-12-31', 2, 3),
('Certificado de Calibração - IntelliVue MX700',     'certificado de calibracao',   '2024-06-01', '2025-06-01', 3, 2),
('Manual de Serviço - Cios Flow',                    'manual de servico',           '2019-05-02', NULL,         4, NULL),
('Fatura de Aquisição - Bisturi FT10',               'fatura ou guia de aquisicao', '2022-11-30', NULL,         5, 3);

INSERT IGNORE INTO `Garantia` (`dataInicio`, `dataFim`, `temContrato`, `tipoContrato`, `entidadeResponsavel`, `periodicidade`, `codigoEquipamento`, `codigoFornecedor`) VALUES
('2021-03-15', '2024-03-15', TRUE,  'Garantia de Fábrica',    'Dräger Medical Portugal', 'Anual',     1, 1),
('2022-01-10', '2025-01-10', TRUE,  'Contrato de Manutenção', 'Philips Healthcare',       'Semestral', 3, 2),
('2022-11-30', '2025-11-30', FALSE, NULL,                      'MedTech Services',         NULL,        5, 3);

INSERT IGNORE INTO `ConteudoPublico` (`chave`, `valor`, `dataAtualizacao`, `codigoUtilizador`) VALUES
('inicio_titulo',     'Gestão Tecnológica Hospitalar Integrada',                                                                                                                                                                '2025-06-01', 1),
('inicio_subtitulo',  'A plataforma definitiva para Engenharia Clínica e controlo absoluto de ativos médicos.',                                                                                                                 '2025-06-01', 1),
('inicio_imagem',     '/assets/img/foto front office.jpg',                                                                                                                                                                      '2025-06-01', 1),
('inicio_botao',      'Solicitar Demonstração',                                                                                                                                                                                 '2025-06-01', 1),
('sobre_col1_titulo', 'Ecossistema Unificado',                                                                                                                                                                                  '2025-06-01', 1),
('sobre_col1_texto',  'O MedCore Inventory centraliza a gestão de equipamentos médicos. Substituímos folhas de Excel dispersas e pastas físicas por uma plataforma única e digital, garantindo dados atualizados em tempo real.', '2025-06-01', 1),
('sobre_col2_titulo', 'Ciclo de Vida Clínico',                                                                                                                                                                                  '2025-06-01', 1),
('sobre_col2_texto',  'Alinhado com as diretrizes da OMS, o sistema controla os dispositivos desde a aquisição até ao seu abate técnico, servindo de base para o planeamento, segurança e análise de risco hospitalar.',        '2025-06-01', 1),
('sobre_col3_titulo', 'Rastreabilidade e Futuro',                                                                                                                                                                               '2025-06-01', 1),
('sobre_col3_texto',  'Garanta total controlo sobre a localização de ativos, fornecedores e contratos. Tudo assente numa estrutura de dados robusta, pronta para evoluir para um sistema CMMS/GMAO completo.',                  '2025-06-01', 1),
('contacto_endereco', 'Rua Dr. António Bernardino de Almeida, 4200-072, Porto, Portugal',                                                                                                                                       '2025-06-01', 1),
('contacto_horarios', '2ª a 6ª Feira: 8h — 18h | Sábados: 9h — 13h | Piquete de Prevenção: 24h / 7 dias',                                                                                                                     '2025-06-01', 1),
('contacto_email',    'suporte@medcore.pt',                                                                                                                                                                                     '2025-06-01', 1),
('contacto_telefone', '+351 226 811 298',                                                                                                                                                                                       '2025-06-01', 1);
