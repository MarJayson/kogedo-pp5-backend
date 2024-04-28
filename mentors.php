<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require_once 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM mentors";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE mentor_id  = :mentor_id ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':mentor_id ', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;


        case "POST":
            $user = json_decode( file_get_contents('php://input') );
            $sql = "INSERT INTO mentors (mentor_id, mentor_name) VALUES(null, :mentor_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':mentor_name', $user->mentor_name);
      
            if($stmt->execute()) {
                $message = ['status' => 1, 'message' => 'Record created successfully.'];
            } else {
                $message = ['status' => 0, 'message' => 'Failed to create record.'];
            }
            echo json_encode($message);
            break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE mentors SET mentor_name= :mentor_name WHERE mentor_id = :mentor_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':mentor_id', $user->mentor_id);
        $stmt->bindParam(':mentor_name', $user->mentor_name);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        
        $sql = "DELETE FROM mentors WHERE mentor_id = :mentor_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':mentor_id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}