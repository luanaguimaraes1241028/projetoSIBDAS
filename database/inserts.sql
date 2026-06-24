-- MedCore Inventory — Dados iniciais

-- Desativa temporariamente a verificação para permitir a inserção de registos com chaves cruzadas
SET FOREIGN_KEY_CHECKS = 0;

-- --- 1. CREDENCIAIS DE DEMONSTRAÇÃO ---
-- As palavras-passe foram encriptadas previamente no PHP usando password_hash() com bcrypt
INSERT INTO Utilizador (nome, email, password, perfil) VALUES
('Administrador',         'admin@medcore.pt',         '$2y$10$a.qbvhJRmGX86d3OW257KObFwxy6WVgoSe6viPywRVdzgsxcHqcGa', 'admin'),
('Técnico',               'tecnico@medcore.pt',        '$2y$10$rsH/GqszRzySyTBslcP5FeGPS0Et/uzSodLv7M.tnWLDOU8N2BkD6', 'tecnico'),
('Profissional de Saúde','profissional@medcore.pt',   '$2y$10$FuFVulDJ1nfhDZLZGK7uKuol5pciTBL.JDLf9VLRONJV27d66Icv2', 'profissional de saude');

-- --- 2. CATEGORIAS DE DISPOSITIVOS MÉDICOS ---
-- Divisão base para a organização do inventário e filtros de pesquisa
INSERT INTO Categoria (nome) VALUES
('Monitorização'),
('Suporte de vida'),
('Terapia'),
('Diagnóstico'),
('Laboratório'),
('Esterilização'),
('Reabilitação');

-- --- 3. INFRAESTRUTURA HOSPITALAR / LOCALIZAÇÕES ---
-- Mapeamento físico onde os equipamentos vão ser alocados
INSERT INTO Localizacao (edificio, piso, servico, sala) VALUES
('Bloco A', 'Piso 1', 'Urgência',                           'Sala de Triagem'),
('Bloco A', 'Piso 2', 'UCI - Unidade de Cuidados Intensivos', 'UCIP-01'),
('Bloco B', 'Piso 0', 'Bloco Operatório',                  'BO-03'),
('Bloco C', 'Piso 1', 'Radiologia',                        'Sala de Raio-X'),
('Bloco D', 'Piso 0', 'Laboratório de Análises Clínicas',  'LAC-01'),
('Bloco A', 'Piso 2', 'UCI',                                'Sala 5');

-- --- 4. PARQUE TECNOLÓGICO (EQUIPAMENTOS) ---
-- Inventário inicial com marcas de referência (Philips, Dräger, GE, etc.) e criticidade clínica definida
INSERT INTO Equipamento (codigoInterno, designacao, marca, modelo, fabricante, numeroSerie, anoFabrico, dataAquisicao, custoAquisicao, tipoEntrada, estado, criticidade, observacoes, codigoCategoria, codigoLocalizacao) VALUES
('EQ-0001', 'Monitor De Sinais Vitais',        'Philips',   'IntelliVue MX40', 'Philips Healthcare',   'SN-PHL-10023-A', 2020, '2021-01-10', 15000.00, 'compra', 'ativo',         'alta',            NULL, 1, 2),
('EQ-0002', 'Ventilador Volumétrico',          'Dräger',    'Evita Infinity',  'Dräger Medical',       'SN-DRG-88321-X', 2019, '2020-03-05', 48000.00, 'compra', 'ativo',         'suporte de vida', NULL, 2, 2),
('EQ-0003', 'Bisturi Elétrico',                'Erbe',      'VIO 300 D',       'Erbe Elektromedizin',  'SN-ERB-44210-B', 2021, '2021-09-20', 22000.00, 'compra', 'ativo',         'alta',            NULL, 3, 3),
('EQ-0004', 'Ecógrafo Portátil',               'GE',        'Vivid IQ',        'GE Healthcare',        'SN-GEH-33109-C', 2022, '2022-06-15', 62000.00, 'compra', 'ativo',         'alta',            NULL, 4, 4),
('EQ-3456', 'Oxímetro De Pulso Portátil',      'Nonin',     'Model 9590',      'Nonin Medical',        'SN-NON-77231-E', 2023, '2023-05-10',   850.00, 'compra', 'ativo',         'media',           NULL, 1, 1),
('EQ-0005', 'Desfibrilhador Externo Automático',   'Philips',          'HeartStart FRx',   'Philips Healthcare',      'SN-PHL-55321-D', 2021, '2022-03-15',  3200.00,  'compra', 'ativo',         'suporte de vida', NULL,                                   2, 1),
('EQ-0006', 'Bomba De Infusão Volumétrica',         'Fresenius',       'Agilia VP',        'Fresenius Kabi',          'SN-FRK-22109-A', 2020, '2020-11-10',  4800.00,  'compra', 'ativo',         'alta',            NULL,                                   3, 2),
('EQ-0007', 'Analisador De Gases Sanguíneos',       'Radiometer',      'ABL90 FLEX',       'Radiometer Medical',      'SN-RAD-11205-B', 2019, '2019-06-20', 28500.00,  'compra', 'ativo',         'alta',            NULL,                                   5, 5),
('EQ-0008', 'Autoclave De Vapor',                   'Tuttnauer',       '3870EHA',          'Tuttnauer Europe',        'SN-TUT-33087-C', 2018, '2018-09-01', 12000.00,  'compra', 'ativo',         'media',           NULL,                                   6, 3),