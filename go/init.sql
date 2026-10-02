CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT NOT NULL
);

INSERT INTO produtos (nome, preco, quantidade) VALUES
('Notebook Dell', 4500.00, 10),
('Smartphone Samsung', 2500.00, 25),
('Monitor LG', 1200.00, 15),
('Teclado Mecânico', 350.00, 30),
('Mouse Sem Fio', 150.00, 50);
