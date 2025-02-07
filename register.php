<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "conn";

// Connexion à MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les valeurs du formulaire
$identifient = $_POST['Identifient'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Sécurisation du mot de passe

// Vérifier si l'utilisateur existe déjà
$sql_check = "SELECT * FROM users WHERE Identifient = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $identifient);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Ce compte existe déjà !";
} else {
    // Insérer le nouvel utilisateur
    $sql_insert = "INSERT INTO users (Identifient, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ss", $identifient, $password);

    if ($stmt->execute()) {
        echo "Compte créé avec succès !";
    } else {
        echo "Erreur lors de la création du compte.";
    }
}

$stmt->close();
$conn->close();
?>
