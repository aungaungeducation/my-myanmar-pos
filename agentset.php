<?php

namespace Api\V1\Usercontroller;

class User
{

    protected function sendErrorResponse($code, $conn)
    {
        echo json_encode([
            "responsecode" => $code
        ]);
        http_response_code($code);
        mysqli_close($conn);
        exit();
    }

    public function usergeneratemobile($data, $conn)
    {
        $mobile = $data->mobile;
        $android = $data->android;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE mobile=?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();

        $res = $stmt->get_result();
        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }

        $row = $res->fetch_assoc();
        $userandroid = $row['android'];
        if ($android != $userandroid) {
            $this->sendErrorResponse("402", $conn);
        }
        $userprivate = $row['private'];
        $token = bin2hex(openssl_random_pseudo_bytes(2));

        $check = $conn->prepare("SELECT * FROM verify WHERE private=?");
        $check->bind_param("s", $userprivate);
        $check->execute();

        $res = $check->get_result();
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $id = $row['id'];

            $stmt = $conn->prepare("UPDATE verify SET token=? WHERE private=?");
            $stmt->bind_param("ss", $token, $userprivate);
            $stmt->execute();

            echo json_encode([
                "responsecode" => "200",
                "token" => $token
            ]);
            exit();
        }

        $insert = $conn->prepare("INSERT INTO verify (token, private) VALUES (?, ?)");
        $insert->bind_param("ss", $token, $userprivate);
        $insert->execute();

        echo json_encode([
            "responsecode" => "200",
            "token" => $token
        ]);
        exit();
    }

    public function usergeneratetoken($data, $conn)
    {
        $private = $data->private;
        $android = $data->android;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=? and android=?");
        $stmt->bind_param("ss", $private, $android);
        $stmt->execute();

        $res = $stmt->get_result();
        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }

        $token = bin2hex(openssl_random_pseudo_bytes(2));
        $check = $conn->prepare("SELECT * FROM verify WHERE private=?");
        $check->bind_param("s", $private);
        $check->execute();
        $hehe = $check->get_result();
        if ($hehe->num_rows > 0) {
            $row = $hehe->fetch_assoc();
            $id = $row['id'];

            $stmt = $conn->prepare("UPDATE verify SET token=? WHERE private=?");
            $stmt->bind_param("ss", $token, $private);
            $stmt->execute();

            echo json_encode([
                "responsecode" => "200",
                "token" => $token
            ]);
            exit();
        }

        $insert = $conn->prepare("INSERT INTO verify (token, private) VALUES (?, ?)");
        $insert->bind_param('ss', $token, $private);
        $insert->execute();

        echo json_encode([
            "responsecode" => "200",
            "token" => $token
        ]);
        exit();
    }

    public function getinvinations($data, $conn)
    {
        $usercode = $data->usercode;
        $array = [];
        $stmt = $conn->prepare("SELECT username, profile FROM userauth WHERE accept=?");
        $stmt->bind_param("s", $usercode);
        $stmt->execute();

        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
    }

    public function userauthorized($data, $conn)
    {
        $mobile = $data->mobile;
        $password = $data->password;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE mobile=?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();

        $res = $stmt->get_result();
        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }

        $row = $res->fetch_assoc();
        $dbpassword = $row['password'];
        if (!password_verify($password, $dbpassword)) {
            $this->sendErrorResponse("402", $conn);
        }

        echo json_encode([
            "responsecode" => "200",
            "private" => $row['private']
        ]);
        exit();
    }

    private function checkuser($data, $para, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM userauth WHERE $para=?");
        $stmt->bind_param("s", $data->$para);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }
    public function updatecode($data, $conn)
    {
        $private = $data->private;
        $old = $data->old;
        $code = $data->code;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $private);
        $stmt->execute();

        $res = $stmt->get_result();
        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }
        $row = $res->fetch_assoc();
        $password = $row['password'];


        if (!password_verify($old, $password)) {
            $this->sendErrorResponse("402", $conn);
        }

        $hashcode = password_hash($code, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE payment SET pincode=? WHERE private=?");
        $stmt->bind_param("ss", $hashcode, $private);
        $stmt->execute();

        echo json_encode([
            "responsecode" => "200"
        ]);
        http_response_code(200);
        exit();
    }
    public function updatepassword($data, $conn)
    {
        $private = $data->private;
        $old = $data->old;
        $new = $data->new;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $private);
        $stmt->execute();

        $res = $stmt->get_result();

        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }

        $row = $res->fetch_assoc();
        $password = $row['password'];

        if (!password_verify($old, $password)) {
            $this->sendErrorResponse("402", $conn);
        }
        $hashpass = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE userauth SET password=? WHERE private=?");
        $stmt->bind_param("ss", $hashpass, $private);
        $stmt->execute();

        echo json_encode([
            "responsecode" => "200"
        ]);
        http_response_code(200);
        exit();
    }

    public function userauth($data, $conn)
    {
        $android = $data->android;
        $private = $data->private;

        if (!$this->checkuser($data, "private", $conn)) {
            $this->sendErrorResponse("401", $conn);
        }

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $private);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if ($row['android'] !== $android) {
            $this->sendErrorResponse("402", $conn);
        }

        if ($row['status'] != "true") {
            $this->sendErrorResponse("403", $conn);
        }

        echo json_encode([
            "responsecode" => "200",
            "username" => $row['username'],
            "profile" => $row['profile'],
            "balance" => $row['balance'],
            "mobile" => $row['mobile'],
            "usercode" => $row['usercode'],
            "commesion" => $row['commesion']
        ]);
        mysqli_close($conn);
        exit();
    }

    private function checkagent($data, $conn)
    {
        $agentmobile = $data->hobby;

        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $agentmobile);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return json_encode([
            "agentprivate" => $row['private']
        ]);
    }

    private function userthreelimit($userprivate, $agentprivate, $conn) {
        $search = $conn->prepare("SELECT * FROM agent_limit WHERE agentprivate=?");
        $search -> bind_param("s", $agentprivate);
        $search -> execute();

        $res = $search->get_result();
        $values = [];
        while($row = $res->fetch_assoc()) {
            $number = $row['number'];
            $value = $row['value'];
            $values[] = "('$agentprivate', '$userprivate', '$number', '$value')";
        }

        $insert_sql = implode(", ", $values);
        $stmt = "INSERT INTO user_three_limit (agentprivate, userprivate, number, value) VALUES $insert_sql";
        $conn->query($stmt);
    }

    public function Userregister($data, $conn)
    {
        if ($this->checkuser($data, "mobile", $conn)) {
            $this->sendErrorResponse("401", $conn);
        }
        if ($this->checkuser($data, "android", $conn)) {
            $this->sendErrorResponse("402", $conn);
        }

        $usercode = strtoupper($data->username . bin2hex(openssl_random_pseudo_bytes(20)));
        $commesion = "0";
        $password = password_hash($data->password, PASSWORD_BCRYPT);
        $noti = "false";
        $private = bin2hex(openssl_random_pseudo_bytes(100));

        $accept = $data->accept;
        $panelcode = $data->hobby;

        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $panelcode);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $agentprivate = $row['private'];

        $stmt = $conn->prepare("SELECT * FROM agentset WHERE agentprivate=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $coins = $row['coins'];
        $twod = $row['twod'];
        $threed = $row['threed'];
        $toot = $row['toot'];
        $commesion = $row['defaultcommission'];
        if ($accept == "false") {
            
            $balance = "0";

        } else {
            $stmt = $conn->prepare("SELECT * FROM userauth WHERE usercode=?");
            $stmt->bind_param("s", $accept);
            $stmt->execute();
            $res = $stmt->get_result();
            $otheruser = $res->fetch_assoc();
            $otherusercom = $otheruser['commesion'];
            $otheruserprivate = $otheruser['private'];

            if ($otherusercom == "0") {
                $otherusercom = $otherusercom + $row['commission'];
            } else {
                $otherusercom = $otherusercom + $row['secondcommission'];
            }
            $balance = $coins;

            $update = $conn->prepare("UPDATE userauth SET commesion=? WHERE private=?");
            $update->bind_param("ss", $otherusercom, $otheruserprivate);
            $update->execute();
        }
        $status = "true";
        $profile = 'https://lightseagreen-herring-407374.hostingersite.com/Myanmarpos/Profile/' . $data->profile;


        $agenthehe = json_decode($this->checkagent($data, $conn));
        $agentprivate = $agenthehe->agentprivate;

        $stmt = $conn->prepare("INSERT INTO userset (userprivate, twod, threed, toot) VALUES (?, ?, ?, ?) ");
        $stmt->bind_param("ssss", $private, $twod, $threed, $toot);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM panel_limit WHERE agentprivate = ?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $this->userthreelimit($private, $agentprivate, $conn);

        $stmt = $conn->prepare("INSERT INTO userauth (username, mobile, usercode, commesion, password, noti, balance, android, private, accept, status, panelcode, profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $data->username, $data->mobile, $usercode, $commesion, $password, $noti, $balance, $data->android, $private, $accept, $status, $panelcode, $profile);

        if ($stmt->execute()) {
            $columns = [
                'zero',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',
                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',
                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen',
                'twenty',
                'twenty_one',
                'twenty_two',
                'twenty_three',
                'twenty_four',
                'twenty_five',
                'twenty_six',
                'twenty_seven',
                'twenty_eight',
                'twenty_nine',
                'thirty',
                'thirty_one',
                'thirty_two',
                'thirty_three',
                'thirty_four',
                'thirty_five',
                'thirty_six',
                'thirty_seven',
                'thirty_eight',
                'thirty_nine',
                'forty',
                'forty_one',
                'forty_two',
                'forty_three',
                'forty_four',
                'forty_five',
                'forty_six',
                'forty_seven',
                'forty_eight',
                'forty_nine',
                'fifty',
                'fifty_one',
                'fifty_two',
                'fifty_three',
                'fifty_four',
                'fifty_five',
                'fifty_six',
                'fifty_seven',
                'fifty_eight',
                'fifty_nine',
                'sixty',
                'sixty_one',
                'sixty_two',
                'sixty_three',
                'sixty_four',
                'sixty_five',
                'sixty_six',
                'sixty_seven',
                'sixty_eight',
                'sixty_nine',
                'seventy',
                'seventy_one',
                'seventy_two',
                'seventy_three',
                'seventy_four',
                'seventy_five',
                'seventy_six',
                'seventy_seven',
                'seventy_eight',
                'seventy_nine',
                'eighty',
                'eighty_one',
                'eighty_two',
                'eighty_three',
                'eighty_four',
                'eighty_five',
                'eighty_six',
                'eighty_seven',
                'eighty_eight',
                'eighty_nine',
                'ninety',
                'ninety_one',
                'ninety_two',
                'ninety_three',
                'ninety_four',
                'ninety_five',
                'ninety_six',
                'ninety_seven',
                'ninety_eight',
                'ninety_nine'
            ];
            $colNames = implode(", ", array_map(fn($c) => "`$c`", $columns));
            $placeholders = implode(", ", array_fill(0, count($columns), "?"));
            $values = array_map(fn($col) => $row[$col] ?? "0", $columns);

            $sql = "INSERT INTO user_limit (userprivate, agentprivate, $colNames) VALUES (?, ?, $placeholders)";
            $stmt = $conn->prepare($sql);
            $params = array_merge([$private, $agentprivate], $values);
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            echo json_encode([
                "responsecode" => "200",
                "private" => $private
            ]);
        } else {
            echo json_encode([
                "responsecode" => "500",
                "message" => "Register failed: " . $stmt->error
            ]);
        }

        mysqli_close($conn);
        exit();
    }

    public function search($data, $conn)
    {
        $search = $conn->prepare("SELECT * FROM userauth WHERE usercode=?");
        $search->bind_param("s", $data->usercode);
        $search->execute();
        $res = $search->get_result();

        if (!$res->num_rows > 0) {
            $this->sendErrorResponse("401", $conn);
        }

        $row = $res->fetch_assoc();
        echo json_encode([
            "responsecode" => "200",
            "username" => $row['username'],
            "profile" => $row['profile'],
            "status" => $row['status']
        ]);
        mysqli_close($conn);
        exit();
    }
}
