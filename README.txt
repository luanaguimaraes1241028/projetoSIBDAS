================================================================================
 IDENTIFICAÇÃO
================================================================================

 Nome do projeto    : MedCore Inventory
 Estudante          : Luana Guimarães
 Número             : 1241028
 Unidade curricular : Sistemas de Informação e Bases de Dados Aplicados à Saúde
 Ano letivo         : 2025 / 2026 — LEBIOM, ISEP
 URL local          : http://127.0.0.1/sibdas/1241028/medcore

================================================================================
 DESCRIÇÃO DA APLICAÇÃO
================================================================================

O MedCore Inventory é um sistema web de gestão de inventário de equipamentos
médicos hospitalares. Permite registar, consultar e gerir todo o ciclo de vida
dos equipamentos — desde a aquisição até ao abate — incluindo localização
física, fornecedores associados, documentação técnica e garantias.

A plataforma divide-se em duas áreas:
 - Área pública   : landing page institucional e formulário de contacto.
 - Backoffice     : painel de gestão com módulos de equipamentos, fornecedores,
                    localizações, documentação e mensagens (acesso autenticado).

================================================================================
 TECNOLOGIAS UTILIZADAS
================================================================================

 PHP 8.1          Lógica de servidor, sessões, PDO
 MySQL 8.0        Base de dados relacional
 Bootstrap 5.3    Layout e componentes de interface
 jQuery 3.6.0     Manipulação DOM
 DataTables 1.13  Tabelas interativas com filtros personalizados
 Flatpickr 4.x    Seletor de datas (formato Y-m-d)
 FontAwesome 6.x  Ícones
 Chart.js 3.x     Gráficos do dashboard

================================================================================
 ESTRUTURA PRINCIPAL DAS PASTAS
================================================================================

 medcore/
 ├── assets/           Recursos estáticos (CSS, JS, imagens, plugins)
 ├── config/           config.php — configuração da BD e constantes
 ├── database/         medcore.sql (schema) + inserts.sql (dados demo)
 ├── private/
 │   ├── dashboard/    Painel com estatísticas e alertas
 │   ├── documentacao/ Gestão de documentos técnicos
 │   ├── equipamentos/ CRUD do inventário de equipamentos
 │   ├── fornecedores/ Gestão de fornecedores
 │   ├── includes/     Componentes partilhados (header, nav, sidebar, funções)
 │   ├── localizacoes/ Gestão de localizações físicas
 │   └── mensagens/    Mensagens de contacto recebidas
 └── public/           Página pública, login e logout

================================================================================
 INSTALAÇÃO E EXECUÇÃO
================================================================================

Pré-requisitos:
 - Laragon (recomendado) ou XAMPP com PHP 8.1+
 - Acesso à rede ISEP ou VPN ativa

Passos:
 1. Copiar a pasta 'medcore' para:
       C:\laragon\www\sibdas\1241028\medcore\

 2. A BD já está configurada em config/config.php:
       Servidor     : vsgate-s1.dei.isep.ipp.pt
       Porta        : 10464
       Base de dados: db1241028
       Utilizador   : 1241028

 3. Se necessário recriar a BD, importar por esta ordem:
       database/medcore.sql    -> cria tabelas e restrições
       database/inserts.sql    -> insere dados de demonstração

 4. Iniciar o Laragon e aceder a:
       http://127.0.0.1/sibdas/1241028/medcore

================================================================================
 CREDENCIAIS DE ACESSO
================================================================================

 Perfil                | Email                    | Password
 ----------------------|--------------------------|----------
 Administrador         | admin@medcore.pt         | 123456
 Técnico               | tecnico@medcore.pt       | 654321
 Profissional de Saúde | profissional@medcore.pt  | prof123

================================================================================
 PERFIS E PERMISSÕES
================================================================================

 Admin              : acesso total — consulta, inserção, edição, abate de
                      equipamentos e gestão de conteúdos públicos.
 Técnico            : consulta, inserção e edição; sem abate de equipamentos.
 Profissional Saúde : leitura e exportação apenas; sem inserção ou edição.

 Qualquer tentativa de aceder a páginas fora das permissões do perfil resulta
 em redirecionamento automático para o dashboard.

================================================================================
 FUNCIONALIDADES IMPLEMENTADAS
================================================================================

 Autenticação e segurança
   - Login com email e password (bcrypt)
   - Controlo de acesso por perfil em todas as páginas privadas
   - IDs de URL cifrados com AES-256-CBC
   - Registo de log de todas as operações (tabela Log)
   - Alteração de password pelo próprio utilizador

 Dashboard
   - Contadores por estado (ativo, em manutenção, inativo)
   - Alertas de garantias expiradas e a expirar nos próximos 30 dias
   - Alerta de equipamentos sem documentação
   - Gráfico de barras por serviço
   - Gráfico de rosca por criticidade clínica

 Equipamentos
   - Listagem com filtros por estado, criticidade e categoria
   - Secção separada para equipamentos abatidos
   - Inserção com associação de fornecedores (N:M) e garantia (opcional)
   - Edição completa com reatribuição de fornecedores
   - Abate (soft delete — preserva histórico)
   - Exportação em CSV, JSON e PDF

 Fornecedores
   - CRUD completo (fabricante, distribuidor, assistência técnica, consumíveis)
   - Soft delete + exportação CSV, JSON, PDF

 Localizações
   - CRUD completo (edifício, piso, serviço, sala)
   - Soft delete + exportação CSV, JSON, PDF

 Documentação
   - Associação de documentos a equipamentos (manuais, certificados, contratos…)
   - Controlo de data de validade
   - Soft delete + exportação CSV, JSON, PDF

 Página pública
   - Landing page com conteúdos editáveis pelo admin
   - Formulário de contacto sem autenticação

 Mensagens
   - Listagem das mensagens recebidas
   - Marcação como lida

================================================================================
 PRINCIPAIS TESTES A REALIZAR
================================================================================

 Autenticação
   - Iniciar sessão com cada um dos três perfis
   - Aceder sem sessão a uma página privada -> redireciona para login
   - Aceder como Profissional a uma página de inserção -> redireciona

 Equipamentos
   - Listar e testar filtros por estado, criticidade e categoria
   - Inserir equipamento com fornecedor e garantia
   - Editar localização de um equipamento
   - Abater equipamento (só admin) e confirmar que sai da lista ativa
   - Exportar CSV (abrir no Excel), JSON (verificar envelope) e PDF (imprimir)

 Fornecedores / Localizações
   - Inserir e verificar que ficam disponíveis nos formulários de equipamentos
   - Desativar e confirmar que ficam marcados como inativos

 Documentação
   - Associar documento a um equipamento
   - Editar e depois remover; verificar que alerta do dashboard atualiza

 Mensagens
   - Enviar formulário de contacto na página pública
   - Verificar a mensagem no backoffice e marcá-la como lida

 Alteração de password
   - Testar com password nova de 5 caracteres -> deve rejeitar
   - Testar com confirmação diferente -> deve rejeitar
   - Alterar com dados corretos e confirmar que o login funciona

================================================================================
 BASE DE DADOS
================================================================================

 Servidor    : vsgate-s1.dei.isep.ipp.pt
 Porta       : 10464
 Base dados  : db1241028

 Tabelas:
   Utilizador           Utilizadores e credenciais de acesso
   Equipamento          Inventário de equipamentos médicos
   Categoria            Categorias (monitorização, terapia, laboratório…)
   Localizacao          Localização física (edifício/piso/serviço/sala)
   Fornecedor           Fornecedores e parceiros
   EquipamentoFornecedor Associação N:M equipamento <-> fornecedor
   Garantia             Contratos e prazos de garantia
   Documentacao         Documentos associados a equipamentos
   ConteudoPublico      Textos editáveis da página pública (chave-valor)
   Log                  Auditoria de todas as operações
   MensagemContacto     Mensagens do formulário de contacto

================================================================================
 NOTAS IMPORTANTES
================================================================================

 - Soft delete: nenhum registo é apagado fisicamente da BD; o estado é alterado.
 - IDs cifrados: parâmetros GET com AES-256-CBC para evitar manipulação de URLs.
 - CSV europeu: separador ';' e BOM UTF-8 para compatibilidade com Excel pt-PT.
 - Transações PDO: inserção/edição de equipamentos é atómica (equipamento +
   garantia + fornecedores gravados em conjunto ou nenhum é gravado).
 - Página pública dinâmica: textos geridos em BD, editáveis pelo admin.
 - Encoding: todos os ficheiros PHP em UTF-8 sem BOM.

================================================================================
 FICHEIROS A INCLUIR NA SUBMISSÃO
================================================================================

 medcore/
 ├── assets/
 ├── config/config.php
 ├── database/medcore.sql
 ├── database/inserts.sql
 ├── private/
 ├── public/
 ├── commits.txt          <- gerado antes da submissão (ver comando abaixo)
 └── README.txt

 Não incluir: vendor/, .env, .idea/, .vscode/, ficheiros temporários.

================================================================================
 COMANDO PARA GERAR O COMMITS.TXT
================================================================================

 PowerShell (Laragon / Windows):

   git log --pretty=format:"%h - %an - %ad - %s" --date=iso | Out-File -Encoding utf8 commits.txt

 Git Bash:

   git log --pretty=format:"%h - %an - %ad - %s" --date=iso > commits.txt

================================================================================
