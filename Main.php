<?php
if($_SERVER['REQUEST_METHOD'] !== "POST") {
    include('error.html');
    exit();
}
class Main {
    private $conn;
    
    public function __construct() {
        $server = "localhost";
        $username = "u203809175_movies";
        $password = "@May0000";
        $dbname = "u203809175_movies";

        $this->conn = new mysqli($server, $username, $password, $dbname);
        
        $this->helper();

    }
    private function insert() {
        $file = "Config/noti.json";
        $filedata = json_decode(file_get_contents($file), true);

        foreach($filedata as $row) {
            $header = $row['header'];
            $content = $row['content'];
            $date = $row['date'];
            $stmt = $this->conn->prepare("INSERT INTO noti (header, content, date) VALUES (?, ?, ?)");
            $stmt -> bind_param("sss", $header, $content, $date);
            $stmt -> execute();
        }
    }
    private function helper() {
        $user = "CREATE TABLE IF NOT EXISTS userauth (
        userid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username TEXT NOT NULL,
        private TEXT NOT NULL,
        mobile TEXT NOT NULL,
        password TEXT NOT NULL,
        profile TEXT NOT NULL,
        android TEXT NOT NULL
        )";

        $this->conn->query($user);

        $movie = "CREATE TABLE IF NOT EXISTS movie (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        type TEXT NOT NULL,
        name TEXT NOT NULL,
        video TEXT NOT NULL,
        content TEXT NOT NULL,
        poster TEXT NOT NULL,
        date TEXT NOT NULL,
        come TEXT NOT NULL,
        size TEXT NOT NULL,
        time TEXT NOT NULL,
        private TEXT NOT NULL
        )";

        $this->conn->query($movie);

        $like = "CREATE TABLE IF NOT EXISTS `like`(
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        movieprivate TEXT NOT NULL,
        userprivate TEXT NOT NULL,
        username TEXT NOT NULL,
        profile TEXT NOT NULL
        )";
        $this->conn->query($like);

        $viewer = "CREATE TABLE IF NOT EXISTS viewer(
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        movieprivate TEXT NOT NULL,
        userprivate TEXT NOT NULL,
        username TEXT NOT NULL,
        profile TEXT NOT NULL
        )";
        $this->conn->query($viewer);

        $noti = "CREATE TABLE IF NOT EXISTS noti (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        header TEXT NOT NULL,
        date TEXT NOT NULL,
        content TEXT NOT NULL
        )";

        $this->conn->query($noti);

    }
    private function aes($datas) {
        $data = $datas;
        $secret_key = "aungaungeducationknowledgeaungaungeducationknowledge";
        $key = hash('sha256', $secret_key, true);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $encrypted_data = base64_encode($iv . $encrypted);
        return $encrypted_data;
    }
    
    public function request($data) {
        switch($data->request) {
            case '1102':
                $this->get($data);
            break;
            case '1103':
                $this->register($data);
            break;
            case '1104':
                $this->userauth($data);
            break;
            case '1105':
                $this->login($data);
            break;
            case '1106':
                $this->upd($data);
            break;
            case '1107':
                //$this->info();
            break;
            case '1108':
                $this->addlikes($data);
            break;
            case '1109':
                $this->movieget($data);
            break;
            case '1100':
                //$this->notiinsert($data);
            break;
            case '1200':
                $this->getnoti();
            break;
            case '1300':
                //$this->viewercount($data);
            break;
            case '1500':
                $this->search($data);
            break;
            case '1400':
                $this->vieweradd($data);
            break;
            default:
                include('error.html');
                $this->error("404");
            break;
        }
    }
    private function search() {
        $stmt = $this->conn->prepare("SELECT * FROM movie");
        $stmt -> execute();

        $array = [];
        $result = $stmt->get_result();
        while($row = $result -> fetch_assoc()) {
            array_push($array, $row);
        }
        $response = $this->aes(json_encode($array));
        echo $response;
    }
    private function getnoti() {
        $stmt = $this->conn->prepare("SELECT * FROM noti");
        $stmt -> execute();

        $array = [];
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
    }
    private function vieweradd($data) {
        $userprivate = $data->userprivate;
        $movieprivate = $data->movieprivate;
        $username = $data->username;
        $profile = $data->profile;

        $stmt = $this->conn->prepare("INSERT INTO viewer (userprivate, movieprivate, username, profile) VALUES (?, ?, ?, ?)");
        $stmt -> bind_param("ssss", $userprivate, $movieprivate, $username, $profile);
        $stmt -> execute();
    }
    private function getcountlike($data) {
        $movieprivate = $data->movieprivate;

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM `like` WHERE movieprivate=?");
        $stmt -> bind_param("s", $movieprivate);
        $stmt -> execute();

        $res = $stmt ->get_result();
        $row = $res->fetch_assoc();

        return (int)$row['total'];
    }
    private function addlikes($data) {
        $movieprivate = $data->movieprivate;
        $userprivate = $data->userprivate;
        $username = $data->username;
        $profile = $data->profile;

        $stmt = $this->conn->prepare("SELECT * FROM `like` WHERE movieprivate=? and userprivate=?");
        $stmt -> bind_param("ss", $movieprivate, $userprivate);
        $stmt -> execute();

        $res = $stmt->get_result();
        if($res->num_rows>0) {
            $status = true;
        } else {
            $status = false;
        }

        if($status) {
            $stmt = $this->conn->prepare("DELETE FROM `like` WHERE movieprivate=? and userprivate=?");
            $stmt -> bind_param("ss", $movieprivate, $userprivate);
            $stmt -> execute();

            $count = $this->getcountlike($data);

            echo json_encode([
                "responsecode"=>"201",
                "return"=>$count
            ]);
            exit();
        }

        $stmt = $this->conn->prepare("INSERT INTO `like` (userprivate, movieprivate, username, profile) VALUES (?,?,?,?)");
        $stmt -> bind_param("ssss", $userprivate, $movieprivate, $username, $profile);
        $stmt -> execute();

        $count = $this->getcountlike($data);
        echo json_encode([
            "responsecode"=>"200",
            "return"=>$count
        ]);
        exit();
    }
    private function movieget($data) {
        $movieprivate = $data->movieprivate;
        $userprivate = $data->userprivate;

        $stmt = $this->conn->prepare("SELECT * FROM `like` WHERE movieprivate=? and userprivate=?");
        $stmt -> bind_param("ss", $movieprivate, $userprivate);
        $stmt -> execute();

        $res = $stmt -> get_result();
        if($res->num_rows>0) {
            $status = "true";
        } else {
            $status = "false";
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM `like` WHERE movieprivate=?");
        $stmt -> bind_param("s", $movieprivate);
        $stmt -> execute();

        $res = $stmt -> get_result();
        $row = $res->fetch_assoc();

        $count = (int)$row['total'];

        echo json_encode([
            "status"=>$status,
            "count"=>$count
        ]);

        exit();

    }
    private function upd($data) {
        $android = $data->android;
        $password = $data->password;
        $verify = $data->verify;
        $private = $data->private;

        $stmt = $this->conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt -> bind_param("s", $private);
        $stmt -> execute();

        $res = $stmt->get_result();
        if(!$res->num_rows>0) {
            $this->error("401");
        }

        $row = $res->fetch_assoc();

        $storeandroid = $row['android'];

        if($storeandroid != $android) {
            $this->error("402");
        }
        $storepassword = $row['password'];

        if(!password_verify($verify, $storepassword)) {
            $this->error("403");
        }

        $hashpassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("UPDATE userauth SET password=? WHERE private=?");
        $stmt ->bind_param("ss", $hashpassword, $private);
        $stmt -> execute();

        echo json_encode([
            "responsecode"=>"200"
        ]);
        exit();
    }
    private function login($data) {
        $mobile = $data->mobile;
        $password = $data->password;
        $android = $data->android;

        $stmt = $this->conn->prepare("SELECT * FROM userauth WHERE mobile=?");
        $stmt -> bind_param("s", $mobile);
        $stmt ->execute();

        $res = $stmt -> get_result();
        if(!$res->num_rows>0) {
            $this->error("403");
        }

        $row = $res ->fetch_assoc();
        $storeandroid = $row['android'];
        $storepass = $row['password'];
        if($storeandroid != $android) {
            $this->error("402");
        }

        if(!password_verify($password, $storepass)) {
            $this->error("401");
        }


        echo json_encode([
            "responsecode"=>"200",
            "private"=>$row['private']
        ]);
        exit();
    }
    private function register($data) {
        $android = $data->android;
        $mobile = $data->mobile;
        $stmt = $this->conn->prepare("SELECT * FROM userauth WHERE mobile=?");
        $stmt -> bind_param("s", $mobile);
        $stmt -> execute();

        $mobile_res=$stmt->get_result();
        if($mobile_res->num_rows>0) {
            $this->error("401");
        }

        $stmt = $this->conn->prepare("SELECT * FROM userauth WHERE android=?");
        $stmt -> bind_param("s", $android);
        $stmt -> execute();

        $android_res = $stmt->get_result();
        if($android_res->num_rows>0) {
            $this->error("402");
        }

        $password = password_hash($data->password, PASSWORD_BCRYPT);
        $private = bin2hex(openssl_random_pseudo_bytes(108));
        $profile = "https://lightseagreen-herring-407374.hostingersite.com/movies/profile/".$data->profile;

        $stmt = $this->conn->prepare("INSERT INTO userauth (username, private, mobile, password, profile, android) VALUES (?,?,?,?,?,?)");
        $stmt ->bind_param("ssssss", $data->username, $private, $mobile, $password,$profile, $android);
        $stmt -> execute();

        echo json_encode([
            "responsecode"=>"200",
            "private"=>$private
        ]);
        exit();
    }
    private function userauth($data) {
        $android = $data->android;
        $private = $data->private;
        
        $stmt = $this->conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt -> bind_param("s", $private);
        $stmt -> execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $profile = $row['profile'];
        $mobile = $row['mobile'];
        $dbandroid = $row['android'];
               
        if($android != $dbandroid) {
            $this->error("401");
        }
        
        echo json_encode([
            "username"=>$username,
            "profile"=>$profile,
            "mobile"=>$mobile
        ]);
        exit();
    }
    private function viewercounts($oop) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM viewer WHERE movieprivate = ?");
        $stmt->bind_param("s", $oop['private']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $count = $row['total'];

        $sum = $count * 100;
        if ($sum >= 1000000) {
            return round($sum / 1000000, 1) . 'M';
        } elseif ($sum >= 1000) {
            return round($sum / 1000, 1) . 'k';
        } else {
            return (int)$sum;
        }
    }

    private function get($oop) {
        $type = $oop->data;
        $movies = $this->conn->prepare("SELECT * FROM movie WHERE type=?");
        $movies -> bind_param("s", $type);
        $movies -> execute();

        $result = $movies->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $array = [];
        foreach($data as $row) {
            $count = $this->viewercounts($row);
            $row['count'] = $count;
            if($row['type'] == $type) {
                array_push($array,$row);
            }
        }
        $response = $this->aes(json_encode($array));
        echo $response;
        
    }    
    private function error($code) {
        echo json_encode([
            "responsecode"=>$code
        ]);
        http_response_code($code);
        exit();
    }
}

function clean_keys($array) {
    $clean = [];
    foreach ($array as $key => $value) {
        $new_key = preg_replace('/[\x00-\x1F\x7F\xA0\xAD\x{200B}]/u', '', $key);
        if (is_array($value)) {
            $value = clean_keys($value);
        }
        $clean[$new_key] = $value;
    }
    return $clean;
}
$raw =file_get_contents('php://input');
if(empty($raw)) {
    include('error.html');
    exit();
}
$data = json_decode($raw, true);
$data = clean_keys($data);
$data = json_decode(json_encode($data));
$obj = new Main();
$obj ->request($data);
?>