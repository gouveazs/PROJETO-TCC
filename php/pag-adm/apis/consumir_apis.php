<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumir API</title>
</head>
<body>
    <h1>Receita Federal: Lista de viados</h1>
    <ul id="lista"></ul>

    <script>
        fetch("http://localhost/PROJETO-TCC/php/pag-adm/apis/apis-fodona.php")
            .then(res => res.json())
            .then(dados => {
                const lista = document.getElementById("lista");

                dados.usuarios.forEach(usuario => {
                    const li = document.createElement("li");
                    li.textContent = "USUÁRIO → ID: " + usuario.idusuario + 
                        " | Nome: " + usuario.nome + 
                        " | Email: " + usuario.email + 
                        " | Telefone: " + usuario.telefone + 
                        " | Status: " + usuario.status;
                    lista.appendChild(li);
                });

                dados.vendedores.forEach(vendedor => {
                    const li = document.createElement("li");
                    li.textContent = "VENDEDOR → ID: " + vendedor.idvendedor + 
                        " | Nome: " + vendedor.nome_completo + 
                        " | Email: " + vendedor.email + 
                        " | Reputação: " + vendedor.reputacao + 
                        " | Status: " + vendedor.status;
                    lista.appendChild(li);
                });

                dados.comunidades.forEach(comunidade => {
                    const li = document.createElement("li");
                    li.textContent = "COMUNIDADES → ID: " + comunidade.idcomunidades + 
                        " | Nome: " + comunidade.nome + 
                        " | Descrição: " + comunidade.descricao + 
                        " | Criada em: " + comunidade.criada_em + 
                        " | Status: " + comunidade.status;
                    lista.appendChild(li);
                });
            })
            .catch(err => console.error("Erro na API:", err));
    </script>
</body>
</html>
