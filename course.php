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
        $sql = "SELECT * FROM course";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE course_id  = :course_id ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':course_id ', $path[3]);
            $stmt->execute();
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $course = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($course);
        break;


        case "POST":
            $user = json_decode( file_get_contents('php://input') );
            $sql = "INSERT INTO courses (course_id, course_name) VALUES(null, :course_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':course_name', $user->course_name);
      
            if($stmt->execute()) {
                $message = ['status' => 1, 'message' => 'Record created successfully.'];
            } else {
                $message = ['status' => 0, 'message' => 'Failed to create record.'];
            }
            echo json_encode($message);
            break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE courses SET course_name= :course_name WHERE course_id = :course_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course_id', $user->course_id);
        $stmt->bindParam(':course_name', $user->course_name);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        
        $sql = "DELETE FROM courses WHERE course_id = :course_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course_id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}