<?php
namespace Api\V1\Actioncontroller;
date_default_timezone_set('Asia/Yangon');

class Useraction {
    private function sendErrorResponse($code) {
        echo json_encode([
            "responsecode"=>$code,
            "message"=>"Unauthorized not found"
        ]);
        http_response_code($code);
        exit();
    }
    
    public function getplayhis($data, $conn) {
        $userprivate = $data->userprivate;
        $userinfo = $this->getuserinfo($data, $conn);
        $agentprivate = $this->getagentinfo($userinfo['panelcode'], $conn);
        
        $stmt = $conn->prepare("SELECT * FROM playhis WHERE userprivate=? and agentprivate=?");
        $stmt -> bind_param("ss", $userprivate, $agentprivate);
        $stmt -> execute();
        
        $array = [];
        $res = $stmt -> get_result();
        while($row = $res -> fetch_assoc()) {
            array_push($array ,$row);
        }
        echo json_encode($array);
    }

    public function addplaythreehis($data, $conn) {
        $hehe = 0;
        $userprivate = $data->userprivate;

        $userinfo = $this->getuserinfo($data, $conn);
        $agentprivate = $this->getagentinfo($userinfo['panelcode'], $conn);

        $hisprivate = rand(0000, 9999)." ".rand(0000, 9999)." ".rand(0000, 9999)." ".rand(0000, 9999);
        $array = json_decode($data->data, true);
        $playtime = $data->playtime;

        $date = date('M d');
        $hour = date('h:i A');

        $username = $userinfo['username'];
        $profile = $userinfo['profile'];
        $balance = $userinfo['balance'];
        $usercommission = $userinfo['commesion'];
        $status = "pending";
        $wlamount = "false";
        $type = "three";

        foreach($array as $item) {
            $hehe = $item['start'] + $hehe;
        }

        if($hehe > $balance) {
            $this->sendErrorResponse("402");
        }

        $totalpercentage = $hehe * $usercommission / 100;

        foreach($array as $item) {
            $number = $item['number'];
            $value = $item['value'];
            $start = $item['start'];

            $update = $value - $start;
            $balance -= $start;
            $sub = $balance;

            $stmt = $conn->prepare("UPDATE user_three_limit SET value=? WHERE number=? and userprivate=?");
            $stmt -> bind_param("sss", $update, $number, $userprivate);
            $stmt -> execute();

            $stmt = $conn->prepare("UPDATE userauth SET balance=? WHERE private=?");
            $stmt -> bind_param("ss", $sub, $userprivate);
            $stmt -> execute();

            $stmt = $conn->prepare("INSERT INTO playhis (userprivate, agentprivate, hisprivate, amount, number, playtime, date, hour, username, profile, status, wlamount, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt -> bind_param("sssssssssssss", $userprivate, $agentprivate, $hisprivate, $start, $number, $playtime, $date, $hour, $username, $profile, $status, $wlamount, $type);
            $stmt -> execute();

            $stmt = $conn->prepare("INSERT INTO reqtwo (userprivate, agentprivate, hisprivate, amount, number, playtime, date, hour, username, profile, status, wlamount, type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt -> bind_param("sssssssssssss", $userprivate, $agentprivate, $hisprivate, $start, $number, $playtime, $date, $hour, $username, $profile, $status, $wlamount, $type);
            $stmt -> execute();
        }
        echo json_encode([
            "responsecode"=>"200",
            "current"=>$sub,
            "usage"=>$hehe,
            "date"=>$date." ".$hour,
            "playtime"=>$playtime,
            "list"=>$data->value,
            "commission"=>$usercommission,
            "total"=>$totalpercentage
        ]);

        exit();
    }
    
    public function addplayhis($data, $conn) {
        $hehe = 0;
        $userprivate = $data->userprivate;
        $userinfo = $this->getuserinfo($data, $conn);
        $agentprivate = $this->getagentinfo($userinfo['panelcode'], $conn);
        $hisprivate = rand(0000, 9999)." ".rand(0000, 9999)." ".rand(0000, 9999)." ".rand(0000, 9999);
        $value = json_decode($data->value, true);
        $playtime = $data->playtime;
        $date = date('M d');
        $hour = date('h:i A');
        
        $username = $userinfo['username'];
        $profile = $userinfo['profile'];
        $balance = $userinfo['balance'];
        $usercommission = $userinfo['commesion'];
        $status = "pending";
        $wlamount = "false";
        $type = "two";
        
        foreach($value as $item) {
            $hehe = $item['start'] + $hehe;
        }
        if($hehe > $balance) {
            $this->sendErrorResponse("402");
        }
        $total_percentage = $usercommission * $hehe / 100;
        
        
        foreach($value as $item) {
            $start = $item['start'];
            $number = $item['value'];
            
            $position = $item['amount'] - $start;
            $colum = $item['colum'];
            
            $balance -= $start;
            $sub = $balance;
            
            $stmt = $conn->prepare("INSERT INTO playhis (userprivate, agentprivate, hisprivate, amount, number, playtime, date, hour, username, profile, status, wlamount, type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt -> bind_param("sssssssssssss", $userprivate, $agentprivate, $hisprivate, $start, $number, $playtime, $date, $hour, $username, $profile, $status, $wlamount, $type);
            $stmt -> execute();
            
            $stmt = $conn->prepare("INSERT INTO reqtwo (userprivate, agentprivate, hisprivate, amount, number, playtime, date, hour, username, profile, status, wlamount, type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt -> bind_param("sssssssssssss", $userprivate, $agentprivate, $hisprivate, $start, $number, $playtime, $date, $hour, $username, $profile, $status, $wlamount, $type);
            $stmt -> execute();
            
            $stmt = $conn->prepare("UPDATE user_limit SET $colum=? WHERE userprivate=?");
            $stmt -> bind_param("ss", $position, $userprivate);
            $stmt -> execute();
            
            $stmt = $conn->prepare("UPDATE userauth SET balance=? WHERE private=?");
            $stmt -> bind_param("ss", $sub, $userprivate);
            $stmt -> execute();
        }
        
        echo json_encode([
            "responsecode"=>"200",
            "current"=>$sub,
            "usage"=>$hehe,
            "date"=>$date." ".$hour,
            "playtime"=>$playtime,
            "list"=>$data->value,
            "commission"=>$usercommission,
            "total"=>$total_percentage
        ]);
        
        exit();
    }
    
    private function getuserinfo($data, $conn) {
        $userprivate = $data->userprivate;
        
        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt -> bind_param("s", $userprivate);
        $stmt -> execute();
        
        $res = $stmt -> get_result();
        $row = $res -> fetch_assoc();
        
        $panelcode = $row['panelcode'];
        $username = $row['username'];
        $profile = $row['profile'];
        return [
            "panelcode"=>$panelcode,
            "username"=>$username,
            "profile"=>$profile,
            "balance"=>$row['balance'],
            "commesion"=>$row['commesion']
        ];
    }
    
    private function getagentinfo($data, $conn) {
        $agentmobile = $data;
        
        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt -> bind_param("s", $agentmobile);
        $stmt -> execute();
        
        $res = $stmt -> get_result();
        $row = $res -> fetch_assoc();
        
        $agentprivate = $row['private'];
        return $agentprivate;
    }
    
}
?>