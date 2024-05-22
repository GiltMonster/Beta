<?php

session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: ../../pages/login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM PRODUTO;");
        $stmt->execute();
        $qtd_produtos = $stmt->fetchColumn();
        $qtd_produtos = $qtd_produtos ? $qtd_produtos : "Sem Produtos!";
    } catch (PDOException $e) {
        echo "Erro ao pegar a quantidade de produtos cadastrados";
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM PRODUTO WHERE PRODUTO.PRODUTO_ATIVO = 1");
        $stmt->execute();
        $qtd_produtos_ativos = $stmt->fetchColumn();
        $qtd_produtos_ativos = $qtd_produtos_ativos ? $qtd_produtos_ativos : "Sem Produtos!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar os produtos ativos";
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM PRODUTO WHERE PRODUTO.PRODUTO_ATIVO = 0");
        $stmt->execute();
        $qtd_produtos_desativados = $stmt->fetchColumn();
        $qtd_produtos_desativados = $qtd_produtos_desativados ? $qtd_produtos_desativados : "Sem Produtos!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar os produtos desativados";
    }

    try {
        $stmt = $pdo->prepare("SELECT SUM(PRODUTO_ESTOQUE.PRODUTO_QTD) FROM PRODUTO_ESTOQUE;");
        $stmt->execute();
        $total_produtos_estoque = $stmt->fetchColumn();
        $total_produtos_estoque = $total_produtos_estoque ? $total_produtos_estoque : "Sem Produtos!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar o total de produtos em estoque";
    }

    // Dados das categorias

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM CATEGORIA;");
        $stmt->execute();
        $qtd_categorias = $stmt->fetchColumn();
        $qtd_categorias = $qtd_categorias ? $qtd_categorias : "Sem Categorias!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar a quantidade de categorias cadastradas";
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM CATEGORIA WHERE CATEGORIA.CATEGORIA_ATIVO = 1;");
        $stmt->execute();
        $qtd_categorias_ativas = $stmt->fetchColumn();
        $qtd_categorias_ativas = $qtd_categorias_ativas ? $qtd_categorias_ativas : "Sem Categorias!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar as categorias ativas";
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM CATEGORIA WHERE CATEGORIA.CATEGORIA_ATIVO = 0");
        $stmt->execute();
        $qtd_categorias_desativadas = $stmt->fetchColumn();
        $qtd_categorias_desativadas = $qtd_categorias_desativadas ? $qtd_categorias_desativadas : "Sem Categorias!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar as categorias desativadas";
    }

    try {
        $stmt = $pdo->prepare("SELECT CATEGORIA.CATEGORIA_NOME, COUNT(PRODUTO.PRODUTO_ID) FROM PRODUTO JOIN CATEGORIA ON CATEGORIA.CATEGORIA_ID = PRODUTO.CATEGORIA_ID GROUP BY CATEGORIA.CATEGORIA_NOME ORDER BY COUNT(PRODUTO.PRODUTO_ID) DESC LIMIT 5");
        $stmt->execute();
        $categorias_mais_usadas = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Erro ao selecionar as categorias mais usadas";
    }

    // Dados das vendas

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM PEDIDO;");
        $stmt->execute();
        $qtd_vendas = $stmt->fetchColumn();
        $qtd_vendas = $qtd_vendas ? $qtd_vendas : "Sem Vendas!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar a quantidade de vendas";
    }

    try {
        $stmt = $pdo->prepare("SELECT SUM(PRODUTO.PRODUTO_PRECO * PRODUTO_ESTOQUE.PRODUTO_QTD) FROM PRODUTO JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID;");
        $stmt->execute();
        $total_valor_estoque = $stmt->fetchColumn();
        $total_valor_estoque = number_format($total_valor_estoque, 2, ',', '.');
        $total_valor_estoque = $total_valor_estoque ? 'R$' . $total_valor_estoque : "Sem Vendas!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar o total de vendas";
    }

    try{
        $stmt = $pdo->prepare("SELECT SUM(PEDIDO_ITEM.ITEM_PRECO) FROM PEDIDO_ITEM;");
        $stmt->execute();
        $total_vendas = $stmt->fetchColumn();
        $total_vendas = number_format($total_vendas, 2, ',', '.');
        $total_vendas = $total_vendas ? 'R$' . $total_vendas : "Sem Vendas!";
    } catch (PDOException $e) {
        echo "Erro ao selecionar o total de vendas";
    }

    try {
        $stmt = $pdo->prepare("SELECT PRODUTO.PRODUTO_NOME, PEDIDO_ITEM.ITEM_QTD FROM PEDIDO_ITEM 
        JOIN PRODUTO ON PEDIDO_ITEM.PRODUTO_ID = PRODUTO.PRODUTO_ID
            GROUP BY
                PRODUTO.PRODUTO_NOME,
                PEDIDO_ITEM.ITEM_QTD
            ORDER BY
                PEDIDO_ITEM.ITEM_QTD DESC
            LIMIT 5;
            ");
        $stmt->execute();
        $produtos_mais_vendidos = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Erro ao selecionar o total de vendas";
    }
}


function reduzirString($string, $comprimentoDesejado = 15) {
    // Verifica se o comprimento da string é maior que o comprimento desejado
    if (strlen($string) > $comprimentoDesejado) {
        // Trunca a string ao comprimento desejado e adiciona "..."
        return substr($string, 0, $comprimentoDesejado) . "...";
    }
    // Retorna a string original se não for necessário truncá-la
    return $string;
}