<?php
// The database host name.
$host = "studdb.csc.liv.ac.uk";
// The database name.
$dbName = "sgpgezah";
// The database username.
$username = "sgpgezah";
// The database password.
$password = "sgpgezah1234";

// Establish a connection to the database
try {
    // Create a PDO object to connect to the database.
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbName;charset=utf8",
        $username,
        $password
    );
    // Set the PDO error mode to exception.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve information on all players of a specific team
function getPlayerByIdAndTeamId($player_id, $team_id)
{
    global $pdo;
    $query =
        "SELECT * FROM players WHERE id = :player_id AND team_id = :team_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
    return $player;
}

    // This function retrieves information on all teams.
    function getAllTeams($team_Id = null)
    {
        // Get the PDO object.
        global $pdo;
        // Create a query to select all teams from the database.
        $query = "SELECT * FROM teams";
        // If a team ID is specified, add a WHERE clause to the query.
        if ($team_Id !== null) {
            $query .= " WHERE id = :id";
        }
        // Prepare the query.
        $stmt = $pdo->prepare($query);
        if ($team_Id !== null) {
            $stmt->bindParam(":id", $team_Id);
        }
        // Debug: Output the SQL query.
        echo "SQL query: $query\n";
        // Execute the query.
        $stmt->execute();
        // Fetch the results.
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Debug: Output the results.
        echo "Result: ";
        print_r($teams);
        // Return the results.
        return $teams;
    }


// This function retrieves information on all players.
function getAllPlayers()
{
    global $pdo;
    $query = "SELECT * FROM players";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $players;
}

// This function retrieves all players of a specific team.
function getPlayersByTeam($team_id)
{
    global $pdo;
    $query = "SELECT * FROM players WHERE team_id = :team_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $players;
}

// This function adds a player to a team
function addPlayerToTeam(
    $surname,
    $given_names,
    $nationality,
    $date_of_birth,
    $team_id
) {
    global $pdo;
    $query = "INSERT INTO players (surname, given_names, nationality,
date_of_birth, team_id) VALUES (:surname, :given_names, :nationality,
:date_of_birth, :team_id)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":surname", $surname);
    $stmt->bindParam(":given_names", $given_names);
    $stmt->bindParam(":nationality", $nationality);
    $stmt->bindParam(":date_of_birth", $date_of_birth);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();
}

// Delete a player from a team
function deletePlayerFromTeam($player_id, $team_id)
{
    global $pdo;

    $query =
        "SELECT * FROM players WHERE id = :player_id AND team_id = :team_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$player) {
        // Player not found in team, return warning
        return ["message" => "Player not found in team", "status" => "warning"];
    }

    // Delete player from team
    $query = "DELETE FROM players WHERE id = :player_id AND team_id = :team_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();

    // Check if any rows were affected
    $rows_affected = $stmt->rowCount();
    if ($rows_affected > 0) {
        // Player deleted successfully
        return [
            "message" => "Player deleted successfully",
            "status" => "success",
        ];
    } else {
        // No rows affected, player not found in team
        return [
            "message" => 'An error occurred while deleting player
from team',
            "status" => "error",
        ];
    }
}

// Update information for an existing player of a team
function updatePlayer(
    $surname,
    $given_names,
    $nationality,
    $date_of_birth,
    $team_id,
    $player_id
) {
    global $pdo;
    $query = "UPDATE players SET surname = :surname, given_names =
:given_names, nationality = :nationality, date_of_birth =
:date_of_birth, team_id = :team_id WHERE id = :player_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":surname", $surname);
    $stmt->bindParam(":given_names", $given_names);
    $stmt->bindParam(":nationality", $nationality);
    $stmt->bindParam(":date_of_birth", $date_of_birth);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->execute();
}

// This block of code handles API requests.
// It checks the HTTP request method and the resource requested,
// and then calls the appropriate function to handle the request.

// If the request method is GET,
// the following resources are supported:
// * `/teams`: Get a list of all teams.
// * `/players`: Get a list of all players.
// * `/players/<team_id>`: Get a list of all players for a specific team.
// * `/players/<player_id>`: Get information about a specific player.

// If the request method is POST,
// the following resources are supported:
// * `/add-player`: Add a new player to a team.
// * `/delete-player`: Delete a player from a team.
// * `/update-player`: Update information about a player.

// If the request method is not supported,
// an error message is returned.

if ($_SERVER["REQUEST_METHOD"]) {
    // Retrieve information on all teams
    if (isset($_GET["resource"]) && $_GET["resource"] === "teams") {
        $teams = getAllTeams();
        echo json_encode($teams);
    }

    // Retrieve information on an existing player of a team
    elseif (isset($_GET["resource"]) && $_GET["resource"] === "players") {
        if (isset($_GET["team_id"]) && isset($_GET["player_id"])) {
            $team_id = $_GET["team_id"];
            $player_id = $_GET["player_id"];
            $player = getPlayerByIdAndTeamId($player_id, $team_id);
            echo json_encode($player);
        } elseif (isset($_GET["team_id"])) {
            $team_id = $_GET["team_id"];
            $players = getPlayersByTeam($team_id);
            echo json_encode($players);
        } else {
            $players = getAllPlayers();
            echo json_encode($players);
        }
    }

    // Add a player to a team
    elseif (
        $_SERVER["REQUEST_METHOD"] === "POST" &&
        isset($_GET["resource"]) &&
        $_GET["resource"] === "add-player"
    ) {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        if (
            isset($data["surname"]) &&
            isset($data["given_names"]) &&
            isset($data["nationality"]) &&
            isset($data["date_of_birth"]) &&
            isset($data["team_id"])
        ) {
            $surname = $data["surname"];
            $given_names = $data["given_names"];
            $nationality = $data["nationality"];
            $date_of_birth = $data["date_of_birth"];
            $team_id = $data["team_id"];

            addPlayerToTeam(
                $surname,
                $given_names,
                $nationality,
                $date_of_birth,
                $team_id
            );

            // Return JSON response
            $response = [
                "status" => "success",
                "message" => 'Player added to
team successfully',
            ];
            echo json_encode($response);
        } else {
            $response = ["status" => "success", "message" => "Missing fields"];
            echo json_encode($response);
        }
    }
    // Delete a player from a team
    elseif (isset($_GET["resource"]) && $_GET["resource"] === "delete-player") {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $missing_fields = [];
        if (!isset($data["id"])) {
            array_push($missing_fields, "id");
        }
        if (!isset($data["team_id"])) {
            array_push($missing_fields, "team_id");
        }
        if (count($missing_fields) == 0) {
            $player_id = $data["id"];
            $team_id = $data["team_id"];
            $result = deletePlayerFromTeam($player_id, $team_id);
            if ($result["status"] == "success") {
                $response = [
                    "status" => "success",
                    "message" => 'Player deleted
from team successfully',
                ];
                echo json_encode($response);
            } elseif ($result["status"] == "warning") {
                $response = [
                    "status" => "warning",
                    "message" => $result["message"],
                ];
                echo json_encode($response);
            } else {
                $response = [
                    "status" => "error",
                    "message" => 'An error occurred
while deleting player from team',
                ];
                echo json_encode($response);
            }
        } else {
            $response = ["status" => "error", "message" => "Missing fields"];
            echo json_encode($response);
        }
    }

    // Update information for an existing player of a team
    elseif (isset($_GET["resource"]) && $_GET["resource"] === "update-player") {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        if (
            isset($data["surname"]) &&
            isset($data["given_names"]) &&
            isset($data["nationality"]) &&
            isset($data["date_of_birth"]) &&
            isset($_GET["team_id"]) &&
            isset($_GET["id"])
        ) {
            $surname = $data["surname"];
            $given_names = $data["given_names"];
            $nationality = $data["nationality"];
            $date_of_birth = $data["date_of_birth"];
            $team_id = $_GET["team_id"];
            $player_id = $_GET["id"];
            updatePlayer(
                $surname,
                $given_names,
                $nationality,
                $date_of_birth,
                $team_id,
                $player_id
            );
            $response = [
                "status" => "success",
                "message" => 'Player updated
successfully',
            ];
            echo json_encode($response);
        } else {
            $response = ["status" => "error", "message" => "Missing fields"];
            echo json_encode($response);
        }
    }
}
?>