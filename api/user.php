<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

// Database connection with username and password
$db_conn = mysqli_connect("localhost", "root", "nopass", "reactphp", 3306);

if ($db_conn === false) {
    die("ERROR: Could Not Connect. " . mysqli_connect_error());
}

// Check HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch($method)
{
    case "GET":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[4]) && is_numeric($path[4])) {
            $json_array = array();
            $userid = $path[4];
            
            $getuserrow = mysqli_query($db_conn, "SELECT * FROM tbl_user WHERE userid='$userid'");
            while ($userrow = mysqli_fetch_array($getuserrow)) { // Updated this line
                $json_array['rowUserdata'] = array(
                    'id' => $userrow['userid'],
                    'username' => $userrow['username'],
                    'email' => $userrow['useremail'],
                    'status' => $userrow['status']
                );
            }
            echo json_encode($json_array['rowUserdata']);
            return;
        } else {

        $alluser = mysqli_query($db_conn, "SELECT * FROM tbl_user");
        if(mysqli_num_rows($alluser) > 0)
        {
            $json_array = []; // Initialize the array to hold user data
            while($row = mysqli_fetch_array($alluser))
            {
                $json_array["userdata"][] = array(
                    "id" => $row["userid"],
                    "username" => $row["username"],
                    "email" => $row["useremail"],
                    "status" => $row["status"]
                );
            }
            // Output JSON-encoded data
            echo json_encode($json_array);
        }
        else
        {
            // Output an error message in JSON format
            echo json_encode(["result" => "No data found. Please check the data."]);
        }
        break;
    }
    case "POST":
        $userpostdata = json_decode(file_get_contents("php://input"));
        
        $username = $userpostdata->username;
        $useremail = $userpostdata->email;
        $status = $userpostdata->status; 

        $result = mysqli_query($db_conn, "INSERT INTO tbl_user (username, useremail, status) 
        VALUES('$username','$useremail','$status')");

        if($result)
        {
            echo json_encode(["success" => "User added successfully"]);
        }
        else {
            echo json_encode(["result" => "Error adding user. Please check the data."]);
        }
        break;

    // default:
    //     echo json_encode(["result" => "Invalid request method."]);
    //     break;

        case "PUT":
            $userUpdate = json_decode(file_get_contents("php://input"));
            $userid = $userUpdate->id;
            $username = $userUpdate-> username;
            $useremail = $userUpdate-> email;
            $status = $userUpdate-> status;

            $updateData = mysqli_query($db_conn, "UPDATE tbl_user SET username='$username', useremail='$useremail', status='$status' WHERE userid ='$userid' ");
            if($updateData)
            {
            echo json_encode(["success" => "User Record updated successfully"]);
            }
            else {
            echo json_encode(["result" => "Error adding user. Please check the data."]);
            }
            break;

            //print_r($userUpdate);die;
            break;

            case "DELETE":
                $path = explode('/', $_SERVER["REQUEST_URI"]);
                //echo "message userid-------------".$path[4];die;
                $result = mysqli_query($db_conn, "DELETE FROM tbl_user WHERE userid = '$path[4]' ");
                if($result)
                {
                echo json_encode(["success" => "User Record delet successfully"]);
                }
                else {
                echo json_encode(["result" => "Error adding user. Please check the data."]);
                }
        
                break;


}

mysqli_close($db_conn); // Close the database connection
?>
