package br.gov.ba.prodeb.lojinha.repository;

import br.gov.ba.prodeb.lojinha.model.Produto;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface ProdutoRepository extends JpaRepository<Produto, Long> {
}

