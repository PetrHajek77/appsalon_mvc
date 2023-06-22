<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nuevo password a continuacion</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?> 

<?php if($error) return; ?>

<!-- action va a ser la misma url pero como tiene token en la parte superior no lo ponemos en el form -->
<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">
            Password
        </label>
        <input type="password" id="password" name="password" placeholder="Tu Nuevo Password"> 
    </div>
    <input type="submit" class="boton" value="Guardar Nuevo Password">
</form>

<div class="acciones">
    <a href="/">Ya tienes cuenta? Iniciar Sesion</a>
    <a href="/crear-cuenta">Todavia no tienes cuenta? Obtener una</a>
</div>