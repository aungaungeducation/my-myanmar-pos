<?php
namespace Api\V1\Messagecontroller;

class Message
{
    private function sendErrorResponse($code)
    {
        echo json_encode([
            "responsecode" => $code
        ]);
        http_response_code($code);
        exit();
    }

    public function gettwowinner($data, $conn)
    {
        $userprivate = $data->private;
        $userinfo = $this->getuser($data, $conn);

        $agentinfo = $this->getagent($userinfo['panelcode'], $conn);
        $agentprivate = $agentinfo['agentprivate'];

        $stmt = $conn->prepare("
        SELECT username, userprofile, usermobile, number, amount, wamount, slipid 
        FROM two_winner 
        WHERE agentprivate=? 
        ORDER BY CAST(amount AS UNSIGNED) DESC 
        LIMIT 100
    ");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $array = [];
        while ($row = $res->fetch_assoc()) {
            if (strlen($row['usermobile']) == 11) {
                $row['usermobile'] = str_repeat('*', 8) . substr($row['usermobile'], -3);
            }
            $array[] = $row;
        }

        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }



    private function userupdate($data, $conn)
    {
        $userprivate = $data->private;
        $noti = "false";

        $stmt = $conn->prepare("UPDATE userauth SET noti=? WHERE private=?");
        $stmt->bind_param("ss", $noti, $userprivate);
        $stmt->execute();
    }

    public function getmessage($data, $conn)
    {
        $users = $this->getuser($data, $conn);
        $agent = $this->getagent($users['panelcode'], $conn);
        $agentprivate = $agent['agentprivate'];


        $stmt = $conn->prepare("SELECT * FROM noti_message WHERE agentprivate=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $array = [];
        while ($row = $res->fetch_assoc()) {
            $row['agentprofile'] = $agent['agentprofile'];
            array_push($array, $row);
        }
        $this->userupdate($data, $conn);

        echo json_encode($array);
    }

    private function getuser($data, $conn)
    {
        $private = $data->private;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $private);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        return [
            "panelcode" => $row['panelcode']
        ];
    }

    private function getagent($data, $conn)
    {
        $agentmobile = $data;

        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $agentmobile);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        return [
            "agentprivate" => $row['private'],
            "agentprofile" => $row['profile']
        ];
    }
}
?>