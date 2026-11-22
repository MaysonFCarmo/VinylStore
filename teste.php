
<?php

echo "<h1>Teste do Ambiente VinylStore</h1>";

echo "<p>Versão do PHP: " . PHP_VERSION . "</p>";

echo "<p>PDO SQLite: ";
echo extension_loaded("pdo_sqlite") ? "ATIVADO" : "DESATIVADO";
echo "</p>";

echo "<p>SQLite3: ";
echo extension_loaded("sqlite3") ? "ATIVADO" : "DESATIVADO";
echo "</p>";

?>
