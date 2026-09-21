package br.gov.ba.prodeb.lojinha.service;

import br.gov.ba.prodeb.lojinha.model.Produto;
import br.gov.ba.prodeb.lojinha.repository.ProdutoRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import jakarta.transaction.Transactional;

import java.util.List;
import java.util.Optional;

@Service
public class ProdutoService {
    @Autowired
    private ProdutoRepository produtoRepository;

    public List<Produto> getAllProdutos() {
        return produtoRepository.findAll();
    }

    public Optional<Produto> getProdutoById(Long id) {
        return produtoRepository.findById(id);
    }

    @Transactional
    public Produto createProduto(Produto produto) {
        return produtoRepository.save(produto);
    }

    @Transactional
    public Produto updateProduto(Long id, Produto produtoDetails) {
        Optional<Produto> optional = produtoRepository.findById(id);
        if (optional.isPresent()) {
            Produto produto = optional.get();
            produto.setNome(produtoDetails.getNome());
            produto.setPreco(produtoDetails.getPreco());
            produto.setQuantidade(produtoDetails.getQuantidade());
            return produtoRepository.save(produto);
        }
        return null;
    }

    @Transactional
    public void deleteProduto(Long id) {
        produtoRepository.deleteById(id);
    }
}

