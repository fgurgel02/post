# Bororos PostFlow

Aplicação PHP/MySQL para gerenciar solicitações de posts do Grupo Escoteiro Bororos. Pensada para rodar em hospedagem compartilhada (Apache + PHP 8 + MySQL), sem dependências externas pesadas.

## Estrutura
- `public/` front controller (`index.php`), assets e `.htaccess`
- `app/` controllers, services e views em PHP
- `config/config.php.example` modelo de configuração
- `database/schema.sql` criação das tabelas
- `database/seed_admin.php` cria usuário admin inicial
- `uploads/` armazenamento local de arquivos (com proteção `.htaccess`)

## Instalação (shared hosting)
1. Crie um banco MySQL e importe `database/schema.sql` via phpMyAdmin.
2. Copie `config/config.php.example` para `config/config.php` e ajuste credenciais de banco e caminhos.
3. Faça upload de todo o projeto para o diretório público (ex.: `public/` vira raiz web). Mantenha `app/`, `config/`, `database/` fora da raiz pública se o host permitir.
4. Garanta permissões de escrita em `uploads/` e `uploads/tmp` (ex.: `chmod 775 uploads uploads/tmp`).
5. Rode uma vez `database/seed_admin.php` (via linha de comando `php database/seed_admin.php` ou acessando pelo navegador) para criar o usuário admin. Defina `ADMIN_EMAIL`, `ADMIN_NAME`, `ADMIN_PASSWORD` via variáveis de ambiente ou edite o script.
6. Acesse `/login`, entre com o admin e configure seus formulários.

## Uso rápido
- Admin cria formulários em `/admin/forms` com seções, campos, opções e lógica condicional.
- Link público do formulário: `/f/{slug}`. Envio cria `submission` + card no Kanban.
- Kanban para comunicação: `/crm` (drag & drop de colunas).
- Detalhe do card: respostas, arquivos (com thumbnails), legenda final editável, comentários e responsáveis.

## Upload em chunks (sem limite prático)
- Front-end envia arquivos em partes de ~2MB (`chunk-upload.js`):
  - `POST /api/upload/init` → retorna `upload_id`
  - `POST /api/upload/chunk?upload_id=...` → envia cada pedaço (mantendo `upload_max_filesize` baixo)
  - `POST /api/upload/complete` → junta o arquivo em `uploads/tmp`
- No envio do formulário, cada `upload_id` é movido para `uploads/form_{id}/submission_{id}/YYYYMMDD/` e o hash SHA-256 é salvo no MySQL.
- `.htaccess` bloqueia execução de scripts dentro de `uploads/`.

## Segurança
- PDO com prepared statements.
- `password_hash` e sessões PHP.
- CSRF token em todos os POSTs.
- Escape de saídas no HTML, validação básica de inputs.
- Thumbnails gerados apenas se a extensão GD estiver disponível; caso contrário, mantém o arquivo original.

## Checklist de testes manuais
- [ ] Criar formulário com 3 seções, campos variados e lógica condicional funcionando no front-end.
- [ ] Enviar submissão pública com fotos grandes (chunk upload funcionando).
- [ ] Card aparece na coluna “Recebido” do Kanban.
- [ ] Abrir card: ver respostas, thumbnails/fotos, editar legenda, comentar, atribuir responsáveis.
- [ ] Mover card entre colunas por drag & drop e ver status persistido no MySQL.
- [ ] Exportar/visualizar uploads no caminho `uploads/form_{id}/submission_{id}/YYYYMMDD/` com hashes salvos.

## Dicas de hospedagem
- Se o host exigir, coloque `public/` como raiz e mantenha o restante fora do webroot.
- Certifique-se de que `mod_rewrite` está ativo para o front controller (`public/.htaccess`).
- Ajuste `memory_limit`/`max_execution_time` se precisar lidar com imagens grandes (thumbnails).
