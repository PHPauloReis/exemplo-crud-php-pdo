package br.gov.ba.prodeb.lojinha.controller;

import br.gov.ba.prodeb.lojinha.model.Produto;
import br.gov.ba.prodeb.lojinha.service.ProdutoService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.List;
import java.util.Optional;

@Controller
@RequestMapping("/")
public class ProdutoController {
    @Autowired
    private ProdutoService produtoService;

    @GetMapping
    public String listarProdutos(@RequestParam(value = "action", defaultValue = "list") String action,
                                  @RequestParam(value = "id", required = false) Long id,
                                  Model model) {
        List<Produto> produtos = produtoService.getAllProdutos();
        model.addAttribute("produtos", produtos);

        if ("edit".equals(action) && id != null) {
            Optional<Produto> produto = produtoService.getProdutoById(id);
            if (produto.isPresent()) {
                model.addAttribute("produtoEdit", produto.get());
            }
        }

        return "index";
    }

    @PostMapping
    public String processarProducto(@RequestParam(value = "id", required = false) Long id,
                                    @RequestParam(value = "nome") String nome,
                                    @RequestParam(value = "preco") java.math.BigDecimal preco,
                                    @RequestParam(value = "quantidade") Integer quantidade,
                                    @RequestParam(value = "create", required = false) String create,
                                    @RequestParam(value = "update", required = false) String update,
                                    @RequestParam(value = "delete", required = false) String delete,
                                    RedirectAttributes redirectAttributes) {

        if (create != null) {
            Produto produto = new Produto();
            produto.setNome(nome);
            produto.setPreco(preco);
            produto.setQuantidade(quantidade);
            produtoService.createProduto(produto);
            redirectAttributes.addFlashAttribute("message", "Produto criado com sucesso!");
        } else if (update != null && id != null) {
            Produto produto = new Produto();
            produto.setNome(nome);
            produto.setPreco(preco);
            produto.setQuantidade(quantidade);
            produtoService.updateProduto(id, produto);
            redirectAttributes.addFlashAttribute("message", "Produto atualizado com sucesso!");
        } else if (delete != null && id != null) {
            produtoService.deleteProduto(id);
            redirectAttributes.addFlashAttribute("message", "Produto deletado com sucesso!");
        }

        return "redirect:/";
    }
}

