# Lojinha - Sistema de Gerenciamento de Produtos

Uma recriação de uma aplicação PHP CRUD em **Spring Boot** com suporte a **Docker** e **Docker Compose**, utilizando **Nginx** como proxy reverso.

## 📋 O que é?

Uma aplicação web simples que permite:
- **Criar** novos produtos
- **Listar** todos os produtos
- **Editar** produtos existentes
- **Deletar** produtos

## 🏗️ Arquitetura

A aplicação segue a seguinte arquitetura com Docker Compose:

```
┌─────────────┐
│   Nginx     │ (Reverse Proxy)
│  :8080      │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Java App   │ (Spring Boot)
│  :8081      │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  ProxySQL   │ (Connection Pool)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    MySQL    │ (Database)
│  :3306      │
└─────────────┘
```

## 🚀 Como Usar

### Pré-requisitos
- Docker
- Docker Compose

### Iniciar a Aplicação

1. **Clone ou acesse o diretório do projeto:**
```bash
cd /Users/pauloreis/Documents/Estudo/cafe-digital-performance/lojinha
```

2. **Inicie os serviços com Docker Compose:**
```bash
docker-compose up --build
```

Este comando irá:
- Construir a imagem Docker da aplicação Spring Boot
- Iniciar o MySQL
- Iniciar o ProxySQL
- Iniciar a aplicação Java
- Iniciar o Nginx

3. **Acesse a aplicação:**
```
http://localhost:8080
```

### Parar a Aplicação

```bash
docker-compose down
```

Para remover também os volumes (dados do banco):
```bash
docker-compose down -v
```

## 📁 Estrutura do Projeto

```
lojinha/
├── src/
│   ├── main/
│   │   ├── java/
│   │   │   └── br/gov/ba/prodeb/lojinha/
│   │   │       ├── LojinhaApplication.java
│   │   │       ├── controller/
│   │   │       │   └── ProdutoController.java
│   │   │       ├── model/
│   │   │       │   └── Produto.java
│   │   │       ├── repository/
│   │   │       │   └── ProdutoRepository.java
│   │   │       └── service/
│   │   │           └── ProdutoService.java
│   │   └── resources/
│   │       ├── application.properties
│   │       └── templates/
│   │           └── index.html
│   └── test/
│       └── java/
├── nginx/
│   └── default.conf           # Configuração Nginx (Proxy Reverso)
├── Dockerfile                  # Docker image da aplicação Java
├── docker-compose.yml          # Orquestração dos serviços
├── init.sql                    # Schema e dados iniciais do banco
├── proxysql.cnf                # Configuração ProxySQL
├── .env                        # Variáveis de ambiente
└── pom.xml                     # Dependências Maven

```

## 🔧 Configuração

### Variáveis de Ambiente (.env)

```
DB_HOST=db
DB_NAME=loja
DB_USER=root
DB_PASS=root
```

### application.properties

A partir do arquivos de ambiente Docker, a aplicação se conecta automaticamente:

```properties
spring.datasource.url=jdbc:mysql://${DB_HOST:localhost}:3306/${DB_NAME:loja}
spring.datasource.username=${DB_USER:root}
spring.datasource.password=${DB_PASS:root}
```

## 🗄️ Banco de Dados

### Tabela de Produtos

```sql
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT NOT NULL
);
```

### Dados Iniciais

A tabela é preenchida com dados iniciais:
- Notebook Dell: R$ 4500.00 (10 un)
- Smartphone Samsung: R$ 2500.00 (25 un)
- Monitor LG: R$ 1200.00 (15 un)
- Teclado Mecânico: R$ 350.00 (30 un)
- Mouse Sem Fio: R$ 150.00 (50 un)

## 🛠️ Desenvolvimento Local (sem Docker)

Se desejar executar localmente sem Docker:

1. **Configure um MySQL localmente** e ajuste as variáveis de ambiente:
```bash
export DB_HOST=localhost
export DB_NAME=loja
export DB_USER=root
export DB_PASS=root
```

2. **Execute a aplicação:**
```bash
./mvnw spring-boot:run
```

3. **Acesse em:**
```
http://localhost:8081
```

## 📦 Tecnologias

- **Backend:** Spring Boot 4.1.1
- **Java:** 21+
- **Banco de Dados:** MySQL 8.0
- **Cache:** ProxySQL
- **Proxy Reverso:** Nginx
- **Containerização:** Docker & Docker Compose
- **ORM:** JPA/Hibernate
- **Template:** Thymeleaf
- **Build:** Maven

## 📝 Endpoints da Aplicação

### GET /
Lista todos os produtos

### GET /?action=edit&id={id}
Carrega um produto específico para edição

### POST /
Processa criação, atualização ou deleção de produtos

## 🐛 Troubleshooting

### A aplicação não consegue conectar ao banco

Verifique se o MySQL e ProxySQL estão rodando:
```bash
docker-compose ps
```

### Porta 8080 já está em uso

Edite o `docker-compose.yml` e mude a porta do Nginx:
```yaml
ports:
  - "8090:80"  # Mude para 8090
```

## 📄 Licença

Este projeto é um exemplo de aprendizado e está disponível para fins educacionais.

