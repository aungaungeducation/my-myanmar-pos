<?php
namespace Api\V1\Systemcontroller;
class Panelcontroller
{
    protected function sendErrorResponse($code)
    {
        echo json_encode([
            "responsecode" => $code
        ]);
        http_response_code($code);
        exit();
    }

    public function usergetthreetime($data, $conn)
    {
        $userprivate = $data->private;

        $user = $this->getuser($data, $conn);
        $agent = $this->getagent($user['panelcode'], $conn);
        $agentprivate = $agent['agentprivate'];

        $stmt = $conn->prepare("SELECT * FROM three_time WHERE agentprivate=? ");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $response = [
            ["time" => $row['one'], "status" => $row['one_status'], "value" => $row['value1']],
            ["time" => $row['two'], "status" => $row['two_status'], "value" => $row['value2']],
            ["time" => $row['three'], "status" => $row['three_status'], "value" => $row['value3']],
            ["time" => $row['four'], "status" => $row['four_status'], "value" => $row['value4']],
            ["time" => $row['five'], "status" => $row['five_status'], "value" => $row['value5']]
        ];

        echo json_encode($response);
    }

    public function userpush($data, $conn)
    {
        $private = $data->private;
        $pushdata = $data->pushdata;
    }

    public function usergetthree($data, $conn)
    {
        $userprivate = $data->private;
        $page = isset($data->page) ? (int) $data->page : 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $user = $this->getuser($data, $conn);
        $agent = $this->getagent($user['panelcode'], $conn);
        $agentprivate = $agent['agentprivate'];

        $stmt = $conn->prepare("SELECT number, value FROM user_three_limit WHERE agentprivate=? AND userprivate=? ORDER BY number ASC LIMIT ? OFFSET ?");
        $stmt->bind_param('ssii', $agentprivate, $userprivate, $limit, $offset);
        $stmt->execute();

        $res = $stmt->get_result();
        $array = [];
        while ($row = $res->fetch_assoc()) {
            $row['haha'] = "false";
            $array[] = $row;
        }

        echo json_encode($array);
    }


    public function getagentevent($data, $conn)
    {
        $userprivate = $data->userprivate;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $userprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $panelcode = $row['panelcode'];
        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $panelcode);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $agentprivate = $row['private'];
        $stmt = $conn->prepare("SELECT image, link FROM event WHERE agentprivate=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $array = [];
        while ($row = $res->fetch_assoc()) {
            array_push($array, $row);
        }
        echo json_encode($array);
        exit();
    }

    public function getagentservice($data, $conn)
    {
        $userprivate = $data->userprivate;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $userprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $panelcode = $row['panelcode'];
        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $panelcode);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $agentprivate = $row['private'];
        $stmt = $conn->prepare("SELECT img1, img2, img3, link1, link2, link3, mobile1, mobile2, email FROM service WHERE agentprivate=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        echo json_encode([
            "img1" => $row['img1'],
            "img2" => $row['img2'],
            "img3" => $row['img3'],
            "link1" => $row['link1'],
            "link2" => $row['link2'],
            "link3" => $row['link3'],
            "mobile1" => $row['mobile1'],
            "mobile2" => $row['mobile2'],
            "email" => $row['email']
        ]);
        exit();
    }

    public function getnumber($data, $conn)
    {
        $userprivate = $data->private;
        $users = $this->getuser($data, $conn);
        $agent = $this->getagent($users['panelcode'], $conn);
        $agentprivate = $agent['agentprivate'];

        $stmt = $conn->prepare("SELECT * FROM user_limit WHERE userprivate=? AND agentprivate=?");
        $stmt->bind_param("ss", $userprivate, $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $response = json_encode($row);
        $this->getdata($response, $agentprivate, $conn);

    }
    private function getdata($oop, $agentprivate, $conn)
    {

        $data = json_decode($oop);
        $stmt = $conn->prepare("SELECT * FROM clost_two WHERE agentprivate=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();
        $res = $stmt->get_result();
        $closeRow = $res->fetch_assoc();

        $numberWords = [
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

        $result = [];

        foreach ($numberWords as $index => $colname) {
            $result[] = [
                "colum" => $colname,
                "amount" => $data->$colname ?? 0,
                "value" => str_pad($index, 2, "0", STR_PAD_LEFT),
                "start" => "00",
                "status" => "false",
                "hehe" => $closeRow[$colname] ?? 0
            ];
        }

        echo json_encode($result);
        exit();
    }


    protected function getuser($data, $conn)
    {
        $userprivate = $data->private;

        $stmt = $conn->prepare("SELECT * FROM userauth WHERE private=?");
        $stmt->bind_param("s", $userprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        if ($res->num_rows <= 0) {
            $this->sendErrorResponse("401");
        }

        $user = $res->fetch_assoc();
        if (!$user['panelcode']) {
            $this->sendErrorResponse("402");
        }
        return [
            "panelcode" => $user['panelcode']
        ];
    }
    protected function getagent($data, $conn)
    {
        $agentmobile = $data;
        $stmt = $conn->prepare("SELECT * FROM agent WHERE agentmobile=?");
        $stmt->bind_param("s", $agentmobile);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        return [
            "agentprivate" => $row['private']
        ];

    }
    public function panelcontrol($data, $conn)
    {
        $users = $this->getuser($data, $conn);
        $agent = $this->getagent($users['panelcode'], $conn);
        $agentprivate = $agent['agentprivate'];

        $stmt = $conn->prepare("SELECT * FROM paneltime WHERE private=?");
        $stmt->bind_param("s", $agentprivate);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $this->datamanage($row, $conn);
    }
    protected function datamanage($data, $conn)
    {
        $timeMap = [
            "eight" => "08:00 AM",
            "eiththalf" => "08:30 AM",
            "nine" => "09:00 AM",
            "ninehalf" => "09:30 AM",
            "ten" => "10:00 AM",
            "tenhalf" => "10:30 AM",
            "eleven" => "11:00 AM",
            "elevenhalf" => "11:30 AM",
            "twelve" => "12:00 PM",
            "twelvehalf" => "12:30 PM",
            "one" => "01:00 PM",
            "onehalf" => "01:30 PM",
            "two" => "02:00 PM",
            "twohalf" => "02:30 PM",
            "three" => "03:00 PM",
            "threehalf" => "03:30 PM",
            "four" => "04:00 PM",
            "fourhalf" => "04:30 PM",
            "five" => "05:00 PM",
            "fivehalf" => "05:30 PM",
            "six" => "06:00 PM",
            "sixhalf" => "06:30 PM",
            "seven" => "07:00 PM",
            "sevenhalf" => "07:30 PM"
        ];

        $output = [];

        foreach ($timeMap as $field => $displayTime) {
            if (isset($data[$field]) && $data[$field] !== "false") {
                $output[] = [
                    "time" => $displayTime,
                    "value" => $data[$field]
                ];
            }
        }

        echo json_encode($output);
    }

}
?>