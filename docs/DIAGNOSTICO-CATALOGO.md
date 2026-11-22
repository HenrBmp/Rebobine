# Diagnóstico do catálogo

## Ajustes nesta revisão
- O carregamento do `.env` usa caminho absoluto baseado na pasta do projeto, não depende do diretório de trabalho do Apache.
- A conexão PDO declara `charset=utf8mb4`.
- Falhas de conexão e de consulta são registradas no log do PHP/Apache sem expor credenciais na página.
- O crédito TMDB fica em coluna: logo dimensionada como a marca REBOBINE (130px de largura) e atribuição abaixo.

## Se o catálogo continuar mostrando falha
Consulte o log de erros do Apache/PHP no Laragon. Procure linhas iniciadas por `[REBOBINE]`.
Não compartilhe o arquivo `.env` nem publique senhas/chaves de API.
