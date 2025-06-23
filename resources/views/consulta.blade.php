<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Rápida de Prestadores</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align to top for better content flow */
            min-height: 100vh;
            padding: 2rem; /* Add some padding */
        }
        .container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px; /* Increased max-width for more inputs */
        }
        h1 {
            color: #1f2937;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.75rem;
            font-weight: bold;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: medium;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #374151;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        button {
            width: 100%;
            padding: 1rem;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.125rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        button:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }
        button:active {
            background-color: #1e40af;
            transform: translateY(0);
        }
        #resultado {
            background-color: #f9fafb;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            margin-top: 2rem;
            white-space: pre-wrap; /* Preserve white space and wrap text */
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-size: 0.875rem;
            color: #374151;
            max-height: 500px; /* Limit height for scrollability */
            overflow-y: auto; /* Enable vertical scrolling */
            resize: vertical; /* Allow manual resizing */
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #374151;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }
        .grid-cols-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        @media (max-width: 640px) {
            .grid-cols-2 {
                grid-template-columns: 1fr; /* Stack on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Consulta Rápida de Prestadores</h1>

        <div class="section-title">Coordenadas</div>
        <div class="grid-cols-2">
            <div>
                <label for="latitude_origem">Latitude Origem:</label>
                <input type="number" id="latitude_origem" value="-19.9190" step="any" required>
            </div>
            <div>
                <label for="longitude_origem">Longitude Origem:</label>
                <input type="number" id="longitude_origem" value="-43.9386" step="any" required>
            </div>
            <div>
                <label for="latitude_destino">Latitude Destino:</label>
                <input type="number" id="latitude_destino" value="-19.8653" step="any" required>
            </div>
            <div>
                <label for="longitude_destino">Longitude Destino:</label>
                <input type="number" id="longitude_destino" value="-43.9624" step="any" required>
            </div>
        </div>

        <div class="section-title">Serviço e Quantidade</div>
        <div>
            <label for="servico_id">ID do Serviço:</label>
            <input type="number" id="servico_id" value="1" required>
        </div>
        <div>
            <label for="quantidade">Quantidade de Prestadores (max 100):</label>
            <input type="number" id="quantidade" value="10" min="1" max="100">
        </div>

        <div class="section-title">Ordenação</div>
        <div class="grid-cols-2">
            <div>
                <label for="ordenacao_campo">Ordenar Por:</label>
                <select id="ordenacao_campo">
                    <option value="">Nenhum</option>
                    <option value="valor_total">Valor Total</option>
                    <option value="distancia_total">Distância Total</option>
                </select>
            </div>
            <div>
                <label for="ordenacao_direcao">Direção:</label>
                <select id="ordenacao_direcao">
                    <option value="asc">Crescente (ASC)</option>
                    <option value="desc">Decrescente (DESC)</option>
                </select>
            </div>
        </div>

        <div class="section-title">Filtros (Opcional)</div>
        <div class="grid-cols-2">
            <div>
                <label for="filtro_cidade">Cidade:</label>
                <input type="text" id="filtro_cidade" placeholder="Ex: Belo Horizonte">
            </div>
            <div>
                <label for="filtro_estado">Estado (UF):</label>
                <input type="text" id="filtro_estado" maxlength="2" placeholder="Ex: MG">
            </div>
        </div>

        <button id="buscarPrestadores">Buscar Prestadores</button>

        <div class="section-title">Resultado</div>
        <pre id="resultado">Aguardando busca...</pre>
    </div>

    <script>
        
                    const bearerToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9sb2dpbiIsImlhdCI6MTc1MDY5NTQ1MCwiZXhwIjoxNzUwNjk5MDUwLCJuYmYiOjE3NTA2OTU0NTAsImp0aSI6Ik8yeWROdmJFN08xd09tNnEiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.qxrWlLVC8QpY-QfOfsVoDu2ze6M1AafvqFYbVNKZ4O4';

        document.getElementById('buscarPrestadores').addEventListener('click', async () => {
            document.getElementById('resultado').textContent = 'Buscando...';

            const latitude_origem = document.getElementById('latitude_origem').value;
            const longitude_origem = document.getElementById('longitude_origem').value;
            const latitude_destino = document.getElementById('latitude_destino').value;
            const longitude_destino = document.getElementById('longitude_destino').value;
            const servico_id = document.getElementById('servico_id').value;
            const quantidade = document.getElementById('quantidade').value;
            const ordenacaoCampo = document.getElementById('ordenacao_campo').value;
            const ordenacaoDirecao = document.getElementById('ordenacao_direcao').value;
            const filtroCidade = document.getElementById('filtro_cidade').value;
            const filtroEstado = document.getElementById('filtro_estado').value;

            const requestBody = {
                latitude_origem: parseFloat(latitude_origem),
                longitude_origem: parseFloat(longitude_origem),
                latitude_destino: parseFloat(latitude_destino),
                longitude_destino: parseFloat(longitude_destino),
                servico_id: parseInt(servico_id),
            };

            // Adiciona quantidade se for um valor válido
            if (quantidade && parseInt(quantidade) > 0) {
                requestBody.quantidade = parseInt(quantidade);
            }

            // Adiciona ordenação se um campo foi selecionado
            if (ordenacaoCampo) {
                requestBody.ordenacao = {
                    [ordenacaoCampo]: ordenacaoDirecao
                };
            }

            // Adiciona filtros se foram preenchidos
            if (filtroCidade || filtroEstado) {
                requestBody.filtros = {};
                if (filtroCidade) {
                    requestBody.filtros.cidade = filtroCidade;
                }
                if (filtroEstado) {
                    requestBody.filtros.estado = filtroEstado;
                }
            }

            try {
                const response = await fetch('http://localhost/api/prestadores/buscar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${bearerToken}`
                    },
                    body: JSON.stringify(requestBody)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    document.getElementById('resultado').textContent = `Erro: ${response.status} - ${JSON.stringify(errorData, null, 2)}`;
                    return;
                }

                const data = await response.json();
                document.getElementById('resultado').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                console.error('Erro na requisição:', error);
                document.getElementById('resultado').textContent = 'Ocorreu um erro ao buscar prestadores. Verifique o console do navegador para mais detalhes.';
            }
        });
    </script>
</body>
</html>
