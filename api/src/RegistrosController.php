<?php
function getAllTRegistros($db) {
    $query = "SELECT * FROM tregistro";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
