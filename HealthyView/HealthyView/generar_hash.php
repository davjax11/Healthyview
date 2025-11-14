<?php

// --- Configura tu contraseña aquí ---
$contrasena_plana = "admin123"; 
// ------------------------------------


// Generar el hash usando BCRYPT (el mismo que usa tu app)
$hash_generado = password_hash($contrasena_plana, PASSWORD_BCRYPT);

// Mostrar los resultados
echo "<h1>Generador de Hash para Administrador</h1>";
echo "<p><strong>Contraseña Plana:</strong> " . htmlspecialchars($contrasena_plana) . "</p>";
echo "<p><strong>Hash (Copia y pega esto en tu SQL):</strong></p>";
echo '<textarea rows="3" cols="70" readonly>' . htmlspecialchars($hash_generado) . '</textarea>';

?>
