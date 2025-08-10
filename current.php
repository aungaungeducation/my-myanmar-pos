<?php
namespace Api\V1\Controller;

class UserController{
    private function sendErrorResponse($code) {
        echo json_encode([
            "responsecode"=>$code,
            "message"=>"Unauthorized not found"
        ]);
        http_response_code($code);
        exit();
    }
    public function login($data, $conn) {
        $usermobile = $data->usermobile;
        $userpassword = $data->password;

        $stmt = $conn->prepare("SELECT * FROM salemen WHERE mobile=?");
        $stmt -> bind_param("s", $usermobile);
        $stmt -> execute();

        $res = $stmt -> get_result();
        if($res->num_rows ‌<= 0) {
            $this->sendErrorResponse("401");
        }

        $row = $res -> fetch_assoc();
        $dbpassword = $row['password'];
        if(!password_verify($userpassword, $dbpassword)) {
            $this->sendErrorResponse("402");
        }
        echo json_encode([
            "responsecode"=>"200",
            "private"=>$row['private']
        ]);
        $stmt->close();
        exit();
    }
}
?>