<?php
$header = file_get_contents('php://input');
if(empty($header)) {
    echo json_encode([
        "responsecode"=>"404",
        "Auth"=>"Unautorization not found"
    ]);
    http_response_code(404);
    exit();
}

$data = json_decode($header);

class Panel {
    private $conn;

    public function __construct() {
        $server = "localhost";
        $username = "u203809175_movies";
        $password = "@May0000";
        $dbname = "u203809175_movies";

        $this->conn = new mysqli($server, $username, $password, $dbname);
    }

    public function request($data) {
        switch($data->request) {
            case '1101':
                $this->getmovies();
            break;
            case '1102':
                $this->deletemovies($data);
            break;
            case '1103':
                $this->videoinsert($data);
            break;
            case '1104':
                $this->adsense($data);
            break;
            case '1105':
                $this->userget();
            break;
            case '1106':
                $this->getnoti();
            break;
            case '1107':
                $this->deletenoti($data);
            break;
        }
    }
    private function deletenoti($data) {
        $id = $data->id;

        $stmt = $this->conn->prepare("DELETE FROM noti WHERE id=?");
        $stmt -> bind_param("s", $id);
        $stmt -> execute();
    }
    private function getnoti() {
        $stmt = $this->conn->prepare("SELECT * FROM noti");
        $stmt -> execute();

        $res = $stmt->get_result();
        $array = [];
        while($row = $res -> fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
    }
    private function userget() {
        $stmt = $this->conn->prepare("SELECT * FROM userauth ");
        $stmt -> execute();

        $res = $stmt->get_result();
        $array = [];
        while($row = $res -> fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
    }
    private function adsense($data) {
        $file = '../Config/adsense.json';
        $new = [
            "adsensename"=>$data->adsensename,
            "adsensecontent"=>$data->adsensecontent,
            "adsenseurl"=>$data->adsenseurl,
            "adsenseimage"=>$data->adsenseimage
        ];
        file_put_contents($file, json_encode($new, JSON_PRETTY_PRINT), LOCK_EX);
        echo "update success";
    }
    private function videoinsert($data) {
        $type = $data->type;
        $name = $data->name;
        $video = $data->video;
        $content = $data->content;
        $poster = $data->poster;
        $date = date('M d');
        $come = $data->come;
        $size = $data->size;
        $time = $data->time;
        $private = bin2hex(openssl_random_pseudo_bytes(104));

        $insert = $this->conn->prepare("INSERT INTO movie (type, name, video, content, poster, date, come, size, time, private) VALUES (?,?,?,?,?,?,?,?,?,?) ");
        $insert -> bind_param("ssssssssss", $type, $name, $video, $content, $poster, $date, $come, $size, $time, $private);
        $insert -> execute();

        echo "success upload";

    }
    private function deletemovies($data) {
        $private = $data->private;
        $stmt = $this->conn->prepare("DELETE FROM movie WHERE private=?");
        $stmt -> bind_param("s", $private);
        $stmt -> execute();
    }
    private function getmovies() {
        $stmt = $this->conn->prepare("SELECT * FROM movie");
        $stmt -> execute();
        $array = [];
        $res = $stmt -> get_result();
        while($row = $res -> fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
    }


}
$class = new Panel();
$class -> request($data);
?>