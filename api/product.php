<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

// Database connection
$db_conn = mysqli_connect("localhost", "root", "nopass", "reactphp", 3306);

if ($db_conn === false) {
    die("ERROR: Could Not Connect. " . mysqli_connect_error());
}

// Check HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[4]) && is_numeric($path[4])) {
            echo "Get API Single Row"; die;
        } else {
            $destination = $_SERVER['DOCUMENT_ROOT'] . "/reactwebapp/";
            $allproduct = mysqli_query($db_conn, "SELECT * FROM tbl_product");
            if (mysqli_num_rows($allproduct) > 0) {
                while ($row = mysqli_fetch_array($allproduct)) {
                    $json_array["productdata"][] = array(
                        "id" => $row['p_id'],
                        "ptitle" => $row["ptitle"],
                        "pprice" => $row["pprice"],
                        "pimage" => $row["pfile"],
                        "status" => $row["pstatus"]
                    );
                }
                echo json_encode($json_array["productdata"]);
                return;
            } else {
                echo json_encode(["result" => "please check the Data"]);
                return;
            }
        }

    case "POST":
        if (isset($_FILES['pfile'])) {
            $ptitle = $_POST['ptitle'];
            $pprice = $_POST['pprice'];
            $pfile = time() . $_FILES['pfile']['name'];
            $pfile_temp = $_FILES['pfile']['tmp_name'];
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/reactwebapp/images/' . $pfile;

            $query = "INSERT INTO tbl_product (ptitle, pprice, pfile, pstatus) VALUES ('$ptitle', '$pprice', '$pfile', '1')";
            $result = mysqli_query($db_conn, $query);

            if ($result) {
                move_uploaded_file($pfile_temp, $destination);
                echo json_encode(["success" => "Product Inserted Successfully"]);
            } else {
                echo json_encode(["error" => "Product NOT Inserted Successfully"]);
            }
        } else {
            echo json_encode(["error" => "No file uploaded"]);
        }
        break;

    case "PUT":
        // Get data from the request body
        $productUpdate = json_decode(file_get_contents("php://input"));
        $productId = $productUpdate->id;
        $ptitle = $productUpdate->ptitle;
        $pprice = $productUpdate->pprice;
        $pstatus = $productUpdate->status;

        $updateData = mysqli_query($db_conn, "UPDATE tbl_product SET ptitle='$ptitle', pprice='$pprice', pstatus='$pstatus' WHERE p_id ='$productId'");
        if ($updateData) {
            echo json_encode(["success" => "Product Record updated successfully"]);
        } else {
            echo json_encode(["error" => "Error updating product. Please check the data."]);
        }
        break;

    case "DELETE":
        $path = explode('/', $_SERVER["REQUEST_URI"]);
        $productId = $path[4];

        if (is_numeric($productId)) {
            $result = mysqli_query($db_conn, "DELETE FROM tbl_product WHERE p_id = '$productId'");
            if ($result) {
                echo json_encode(["success" => "Product Record deleted successfully"]);
            } else {
                echo json_encode(["error" => "Error deleting product. Please check the data."]);
            }
        } else {
            echo json_encode(["error" => "Invalid product ID"]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

mysqli_close($db_conn); // Close the database connection
?>
