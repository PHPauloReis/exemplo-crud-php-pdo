package main

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"html/template"
	"log"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

const pageTemplate = `<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD Produtos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f8f9fa}.container{margin-top:50px;background:#fff;padding:30px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,.1)}</style>
</head>
<body>
  <main class="container">
    <h2 class="mb-4">Gerenciamento de Produtos</h2>
    {{if .Message}}<div class="alert alert-success">{{.Message}}</div>{{end}}
    {{if .Error}}<div class="alert alert-danger">{{.Error}}</div>{{end}}
    <div class="row">
      <section class="col-md-4">
        <h4>{{if .Edit}}Editar Produto{{else}}Novo Produto{{end}}</h4>
        <form method="post" action="{{if .Edit}}/produtos/{{.Edit.ID}}{{else}}/produtos{{end}}">
          <div class="mb-3"><label class="form-label" for="nome">Nome</label><input id="nome" name="nome" class="form-control" required value="{{if .Edit}}{{.Edit.Nome}}{{end}}"></div>
          <div class="mb-3"><label class="form-label" for="preco">Preço</label><input id="preco" type="number" step="0.01" min="0" name="preco" class="form-control" required value="{{if .Edit}}{{printf "%.2f" .Edit.Preco}}{{end}}"></div>
          <div class="mb-3"><label class="form-label" for="quantidade">Quantidade</label><input id="quantidade" type="number" min="0" name="quantidade" class="form-control" required value="{{if .Edit}}{{.Edit.Quantidade}}{{end}}"></div>
          {{if .Edit}}<button class="btn btn-warning" type="submit">Atualizar</button><a class="btn btn-secondary" href="/">Cancelar</a>{{else}}<button class="btn btn-primary" type="submit">Salvar</button>{{end}}
        </form>
      </section>
      <section class="col-md-8">
        <h4>Lista de Produtos</h4>
        <table class="table table-striped table-hover"><thead class="table-dark"><tr><th>ID</th><th>Nome</th><th>Preço</th><th>Qtd</th><th>Ações</th></tr></thead>
        <tbody>{{range .Produtos}}<tr><td>{{.ID}}</td><td>{{.Nome}}</td><td>R$ {{printf "%.2f" .Preco}}</td><td>{{.Quantidade}}</td><td><a class="btn btn-sm btn-info text-white" href="/?edit={{.ID}}">Editar</a><form class="d-inline" method="post" action="/produtos/{{.ID}}/excluir"><button class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar este produto?')">Deletar</button></form></td></tr>{{else}}<tr><td colspan="5" class="text-center">Nenhum produto encontrado.</td></tr>{{end}}</tbody></table>
      </section>
    </div>
  </main>
</body>
</html>`

type Produto struct {
	ID         int
	Nome       string
	Preco      float64
	Quantidade int
}

type pageData struct {
	Produtos []Produto
	Edit     *Produto
	Message  string
	Error    string
}

type app struct {
	db  *sql.DB
	tpl *template.Template
}

func main() {
	db, err := connectDatabase()
	if err != nil {
		log.Fatalf("não foi possível conectar ao banco: %v", err)
	}
	defer db.Close()

	a := &app{db: db, tpl: template.Must(template.New("index").Parse(pageTemplate))}
	mux := http.NewServeMux()
	mux.HandleFunc("GET /", a.index)
	mux.HandleFunc("POST /produtos", a.create)
	mux.HandleFunc("POST /produtos/{id}", a.update)
	mux.HandleFunc("POST /produtos/{id}/excluir", a.delete)
	mux.HandleFunc("GET /health", a.health)

	addr := env("APP_ADDR", ":8080")
	log.Printf("servidor iniciado em http://localhost%s", addr)
	log.Fatal(http.ListenAndServe(addr, mux))
}

func connectDatabase() (*sql.DB, error) {
	config := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&charset=utf8mb4&loc=Local",
		url.QueryEscape(env("DB_USER", "root")), url.QueryEscape(env("DB_PASS", "root")),
		env("DB_HOST", "localhost"), env("DB_PORT", "3306"), env("DB_NAME", "loja"))

	db, err := sql.Open("mysql", config)
	if err != nil { return nil, err }
	db.SetMaxOpenConns(20)
	db.SetMaxIdleConns(10)
	db.SetConnMaxLifetime(3 * time.Minute)

	var lastErr error
	for range 30 {
		ctx, cancel := context.WithTimeout(context.Background(), 2*time.Second)
		lastErr = db.PingContext(ctx)
		cancel()
		if lastErr == nil { return db, nil }
		log.Printf("aguardando MySQL: %v", lastErr)
		time.Sleep(2 * time.Second)
	}
	db.Close()
	return nil, lastErr
}

func (a *app) index(w http.ResponseWriter, r *http.Request) {
	data := pageData{Message: r.URL.Query().Get("mensagem")}
	produtos, err := a.list()
	if err != nil { data.Error = "Não foi possível carregar os produtos."; a.render(w, data); return }
	data.Produtos = produtos
	if idText := r.URL.Query().Get("edit"); idText != "" {
		id, err := strconv.Atoi(idText)
		if err != nil || id < 1 { http.NotFound(w, r); return }
		produto, err := a.find(id)
		if errors.Is(err, sql.ErrNoRows) { http.NotFound(w, r); return }
		if err != nil { data.Error = "Não foi possível carregar o produto para edição." } else { data.Edit = produto }
	}
	a.render(w, data)
}

func (a *app) create(w http.ResponseWriter, r *http.Request) {
	p, err := produtoFromRequest(r)
	if err != nil { http.Error(w, err.Error(), http.StatusBadRequest); return }
	_, err = a.db.ExecContext(r.Context(), "INSERT INTO produtos (nome, preco, quantidade) VALUES (?, ?, ?)", p.Nome, p.Preco, p.Quantidade)
	if err != nil { http.Error(w, "Não foi possível criar o produto.", http.StatusInternalServerError); return }
	http.Redirect(w, r, "/?mensagem=Produto+criado+com+sucesso!", http.StatusSeeOther)
}

func (a *app) update(w http.ResponseWriter, r *http.Request) {
	id, err := produtoID(r)
	if err != nil { http.NotFound(w, r); return }
	p, err := produtoFromRequest(r)
	if err != nil { http.Error(w, err.Error(), http.StatusBadRequest); return }
	result, err := a.db.ExecContext(r.Context(), "UPDATE produtos SET nome = ?, preco = ?, quantidade = ? WHERE id = ?", p.Nome, p.Preco, p.Quantidade, id)
	if err != nil { http.Error(w, "Não foi possível atualizar o produto.", http.StatusInternalServerError); return }
	changed, err := result.RowsAffected()
	if err != nil { http.Error(w, "Não foi possível confirmar a atualização do produto.", http.StatusInternalServerError); return }
	if changed == 0 {
		// MySQL retorna zero linhas afetadas quando os valores já são iguais.
		// Confirme a existência para não confundir esse caso com um produto ausente.
		if _, err := a.find(id); errors.Is(err, sql.ErrNoRows) { http.NotFound(w, r); return } else if err != nil { http.Error(w, "Não foi possível confirmar a atualização do produto.", http.StatusInternalServerError); return }
	}
	http.Redirect(w, r, "/?mensagem=Produto+atualizado+com+sucesso!", http.StatusSeeOther)
}

func (a *app) delete(w http.ResponseWriter, r *http.Request) {
	id, err := produtoID(r)
	if err != nil { http.NotFound(w, r); return }
	_, err = a.db.ExecContext(r.Context(), "DELETE FROM produtos WHERE id = ?", id)
	if err != nil { http.Error(w, "Não foi possível deletar o produto.", http.StatusInternalServerError); return }
	http.Redirect(w, r, "/?mensagem=Produto+deletado+com+sucesso!", http.StatusSeeOther)
}

func (a *app) health(w http.ResponseWriter, r *http.Request) {
	if err := a.db.PingContext(r.Context()); err != nil { http.Error(w, "database unavailable", http.StatusServiceUnavailable); return }
	w.WriteHeader(http.StatusOK)
	w.Write([]byte("ok\n"))
}

func (a *app) list() ([]Produto, error) {
	rows, err := a.db.Query("SELECT id, nome, preco, quantidade FROM produtos ORDER BY id")
	if err != nil { return nil, err }
	defer rows.Close()
	var produtos []Produto
	for rows.Next() { var p Produto; if err := rows.Scan(&p.ID, &p.Nome, &p.Preco, &p.Quantidade); err != nil { return nil, err }; produtos = append(produtos, p) }
	return produtos, rows.Err()
}

func (a *app) find(id int) (*Produto, error) {
	var p Produto
	err := a.db.QueryRow("SELECT id, nome, preco, quantidade FROM produtos WHERE id = ?", id).Scan(&p.ID, &p.Nome, &p.Preco, &p.Quantidade)
	return &p, err
}

func (a *app) render(w http.ResponseWriter, data pageData) { w.Header().Set("Content-Type", "text/html; charset=utf-8"); if err := a.tpl.Execute(w, data); err != nil { log.Printf("erro ao renderizar: %v", err) } }

func produtoFromRequest(r *http.Request) (Produto, error) {
	if err := r.ParseForm(); err != nil { return Produto{}, errors.New("formulário inválido") }
	p := Produto{Nome: strings.TrimSpace(r.FormValue("nome"))}
	var err error
	if p.Nome == "" || len(p.Nome) > 255 { return Produto{}, errors.New("informe um nome de até 255 caracteres") }
	if p.Preco, err = strconv.ParseFloat(r.FormValue("preco"), 64); err != nil || p.Preco < 0 { return Produto{}, errors.New("informe um preço válido") }
	if p.Quantidade, err = strconv.Atoi(r.FormValue("quantidade")); err != nil || p.Quantidade < 0 { return Produto{}, errors.New("informe uma quantidade válida") }
	return p, nil
}

func produtoID(r *http.Request) (int, error) { id, err := strconv.Atoi(r.PathValue("id")); if err != nil || id < 1 { return 0, errors.New("id inválido") }; return id, nil }
func env(key, fallback string) string { if value := os.Getenv(key); value != "" { return value }; return fallback }
