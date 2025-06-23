Teste de Vaga de Desenvolvedor - Infornet
Este projeto implementa uma API REST para busca de prestadores de serviços veiculares 24 horas, com base nos requisitos especificados no desafio Infornet. A aplicação permite a autenticação de usuários, busca de prestadores por localização e serviço, aplicação de filtros e ordenação, e cálculo de valores de serviço com base na distância.

Tecnologias Utilizadas
Framework: Laravel 11

Linguagem: PHP 8.x

Banco de Dados: MySQL 8.x

Containerização: Docker com Laravel Sail

Frontend: HTML, JavaScript (AJAX), Tailwind CSS (via CDN)

Autenticação: JWT (JSON Web Tokens)

Pré-requisitos
Para executar este projeto, você precisará ter instalado em sua máquina:

Docker Desktop: (Windows/macOS) ou Docker Engine e Docker Compose: (Linux)

Verifique a instalação: docker --version e docker compose version

WSL 2 (Windows Subsystem for Linux): (Apenas para usuários Windows, recomendado para rodar o Sail)

Instalação e Execução do Projeto
Siga os passos abaixo para configurar e rodar o projeto localmente:

Clone o Repositório:

git clone https://github.com/DaviLOliveira/infornet-test.git
cd infornet-test

Instale as Dependências do Composer com Sail:

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

Este comando usa uma imagem Docker para instalar as dependências PHP via Composer, garantindo que o ambiente de execução seja consistente.

Copie o Arquivo de Ambiente:

cp .env.example .env

Gere a Chave da Aplicação:

./vendor/bin/sail artisan key:generate

Suba os Contêineres Docker:

./vendor/bin/sail up -d

Isso iniciará os serviços Docker em segundo plano (PHP, MySQL, etc.).

Execute as Migrações e Popule o Banco de Dados (Seeders):

./vendor/bin/sail artisan migrate:fresh --seed

Este comando irá recriar todas as tabelas no seu banco de dados e populará com os dados de teste definidos nos seeders (usuários, serviços, prestadores com dados de endereço e serviços anexados).

Acesse a Aplicação no Navegador:

Frontend (Tela de Consulta): Abra seu navegador e acesse http://localhost.

API: A API estará disponível em http://localhost/api/....

Autenticação (JWT)
As rotas da API estão protegidas por autenticação JWT. Para obter um token de acesso:

Utilize o Postman (ou similar):

Endpoint: POST http://localhost/api/login

Headers: Content-Type: application/json

Body (JSON):

{
    "email": "teste@email.com",
    "password": "password"
}

O teste@email.com e password são criados pelo UserSeeder. Você pode ajustar o UserSeeder se quiser outros credenciais.

Copie o Token: A resposta conterá um token JWT (na propriedade token). Copie este token.

Cole no Frontend: No arquivo resources/views/consulta.blade.php, substitua 'COLE_SEU_TOKEN_JWT_AQUI' pelo token copiado.

const bearerToken = 'SEU_TOKEN_JWT_AQUI';

Em uma aplicação real, o token seria gerenciado dinamicamente após o login do usuário.

Documentação Postman com as Chamadas de API
Uma coleção Postman pode ser exportada do Postman e anexada separadamente ao projeto para detalhar todas as chamadas de API. No entanto, aqui estão os principais endpoints para referência:

Autenticação:

POST /api/login

Body: {"email": "...", "password": "..."}

Response: {"user": {...}, "token": "..."}

Busca de Serviços Disponíveis:

GET /api/servicos

Headers: Authorization: Bearer <SEU_TOKEN>

Response: [{"id": 1, "nome": "Reboque", ...}]

Buscar Prestadores:

POST /api/prestadores/buscar

Headers: Content-Type: application/json, Authorization: Bearer <SEU_TOKEN>

Body (Exemplo):

{
    "latitude_origem": -19.9190,
    "longitude_origem": -43.9386,
    "latitude_destino": -19.8653,
    "longitude_destino": -43.9624,
    "servico_id": 1,
    "quantidade": 10,
    "ordenacao": {
        "valor_total": "asc"
    },
    "filtros": {
        "cidade": "Belo Horizonte",
        "estado": "MG"
    }
}

Response: [{ "id": ..., "nome": ..., "distancia_total": ..., "valor_total_servico": ..., "status_online": "..." }, ...]

API Externa para Status Online (Consumida pelo Backend):

POST https://nhen90f0j3.execute-api.us-east-1.amazonaws.com/v1/api/prestadores/online

Autenticação: Basic Auth (usuario: teste-Infornet, senha: c@nsulta-dados-ap1-teste-Infornet#24)

Body: {"prestadores": [1, 2, 3]}

Response: {"data": [{"id": 1, "status": "online"}, {"id": 2, "status": "offline"}]}

Esta API é chamada internamente pelo endpoint /api/prestadores/buscar.

Diferenciais (Itens Opcionais)
Este projeto implementou alguns dos diferenciais propostos:

Disponibilização do projeto em contêiner Docker: O projeto é configurado e executado através do Docker com Laravel Sail, facilitando a portabilidade e consistência do ambiente de desenvolvimento/produção. As instruções de execução detalham o uso do Sail para iniciar os contêineres.

Documentação Postman com as chamadas de API: As principais chamadas de API foram listadas e detalhadas acima para referência rápida. Uma coleção Postman completa pode ser exportada e incluída no repositório.

Itens Opcionais a serem Detalhados (Não Implementados no Prazo)
Implementação de testes unitários e de integração: Não foram desenvolvidos testes automatizados para as funcionalidades da API e da lógica de negócio. Esta seria a próxima etapa crucial para garantir a robustez e a manutenibilidade do código.

Detalhar execução dos testes: Como os testes não foram implementados, não há execução para detalhar.

Detalhar o processo para execução do contêiner: Já abordado nas seções de "Pré-requisitos" e "Instalação e Execução".

Observações Adicionais
Os seeders atuais populam 10 prestadores com múltiplos serviços, mas podem ser expandidos para cumprir o mínimo de 25 prestadores e garantir 3 serviços para cada um de forma mais robusta.

A interface de busca no frontend aceita coordenadas diretamente. Uma melhoria futura seria integrar a API externa de geocodificação para permitir que o usuário insira um endereço em vez de lat/lng.

Contato
Para dúvidas ou informações adicionais, entre em contato.