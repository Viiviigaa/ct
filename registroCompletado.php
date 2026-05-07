<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    body {
        background-color: #f8f9fa; 
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
        padding: 20px;
    }

    h1 {
        color: green;
    }
</style>
<script>
    setTimeout(function() {
      window.location.href = 'index.php';
    }, 3000);
</script>
<body>   
    <div class="container text-center">
        <h1>Registro Completado con exito</h1>;
        <p>Redirigiendo...</p><br>
        <div class="spinner-border text-info" role="status" id="loading">
            <span class="sr-only"></span>
        </div>
    </div>
   
</body>
</html>