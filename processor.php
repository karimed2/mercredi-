<?php
// Activer les erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root"; // Change avec ton utilisateur MySQL
$password = ""; // Change avec ton mot de passe MySQL
$dbname = "ma_base_de_donnees"; // Change avec le nom de ta base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];
    $identifient = trim($_POST["Identifient"]);
    $password = trim($_POST["password"]);

    if ($action == "login") {
        // Vérifier si l'utilisateur existe
        $sql = "SELECT * FROM users WHERE Identifient = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $identifient);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                echo "valid";
            } else {
                echo "invalid";
            }
        } else {
            echo "invalid";
        }
    } elseif ($action == "create") {
        // Vérifier si l'utilisateur existe déjà
        $sql_check = "SELECT * FROM users WHERE Identifient = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("s", $identifient);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "exists"; // L'utilisateur existe déjà
        } else {
            // Hachage du mot de passe avant insertion
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO users (Identifient, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("ss", $identifient, $hashed_password);

            if ($stmt->execute()) {
                echo "created"; // Succès
            } else {
                // Afficher l'erreur SQL
                echo "Erreur lors de la création du compte : " . $stmt->error;
            }
        }
    }
}

// Fermer la connexion
$conn->close();
?>
