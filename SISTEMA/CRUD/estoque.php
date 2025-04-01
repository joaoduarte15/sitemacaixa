<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Gerenciador de Estoque</h1>

        <?php

        $host = 'localhost';
        $dbname = 'estoque';
        $user = 'root';
        $pass = '1234';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("<div class='alert alert-danger'>Erro na conexão com o banco de dados: " . $e->getMessage() . "</div>");
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['action']) && $_POST['action'] == 'delete') {

                $id = $_POST['id'];
                $sql = "DELETE FROM produtos_tbl WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
                echo "<div class='alert alert-success'>Produto excluído com sucesso!</div>";
            } else {

                $nome = $_POST['nome'];
                $quantidade = $_POST['quantidade'];
                $preco = $_POST['preco'];

                if (isset($_POST['id']) && !empty($_POST['id'])) {

                    $id = $_POST['id'];
                    $sql = "UPDATE produtos_tbl SET nome = :nome, quantidade = :quantidade, preco = :preco WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['nome' => $nome, 'quantidade' => $quantidade, 'preco' => $preco, 'id' => $id]);
                    echo "<div class='alert alert-success'>Produto atualizado com sucesso!</div>";
                } else {

                    $sql = "INSERT INTO produtos_tbl (nome, quantidade, preco) VALUES (:nome, :quantidade, :preco)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['nome' => $nome, 'quantidade' => $quantidade, 'preco' => $preco]);
                    echo "<div class='alert alert-success'>Produto criado com sucesso!</div>";
                }
            }
        }


        $nome = "";
        $quantidade = "";
        $preco = "";
        $id_update = "";

        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $sql = "SELECT * FROM produtos_tbl WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                $nome = $produto['nome'];
                $quantidade = $produto['quantidade'];
                $preco = $produto['preco'];
                $id_update = $produto['id'];
            }
        }
        ?>

        <section class="form-section">
            <h2><?= ($id_update) ? 'Editar Produto' : 'Adicionar Produto' ?></h2>
            <form method="post" class="product-form" onsubmit="return validarFormulario()">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id_update) ?>">

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" value="<?= htmlspecialchars($quantidade) ?>" required>
                </div>

                <div class="form-group">
                    <label for="preco">Preço:</label>
                    <input type="number" step="0.01" id="preco" name="preco" value="<?= htmlspecialchars($preco) ?>" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary"><?= ($id_update) ? 'Atualizar' : 'Salvar' ?></button>
                    <?php if ($id_update): ?>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="list-section">
            <h2>Lista de Produtos</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query('SELECT * FROM produtos_tbl');
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['quantidade']) ?></td>
                            <td><?= htmlspecialchars($row['preco']) ?></td>
                            <td>
                                <a href="index.php?edit=<?= htmlspecialchars($row['id']) ?>" class="btn btn-edit">Editar</a>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" class="btn btn-delete" onclick="return confirmarExclusao('<?= htmlspecialchars($row['nome']) ?>')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script src="script.js"></script>

</body>
</html>
