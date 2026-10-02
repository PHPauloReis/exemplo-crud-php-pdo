# CRUD de Produtos em Go

Aplicação Go equivalente ao CRUD do diretório `html`, usando MySQL e Docker Compose.

## Executar

```bash
cd go
cp .env.example .env
docker compose up --build
```

Abra http://localhost:8080. O Nginx recebe as requisições e as encaminha para a aplicação Go; a aplicação não fica exposta diretamente. A rota `GET /health` verifica a conexão com o banco.

Se a porta 8080 já estiver em uso, suba com outra porta externa, por exemplo:

```bash
APP_PORT=8081 docker compose up --build
```

O MySQL é inicializado com cinco produtos de exemplo na primeira criação do volume. Para reinicializar os dados, execute `docker compose down -v` e suba novamente.
