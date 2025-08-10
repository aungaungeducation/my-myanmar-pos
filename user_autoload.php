<?php
require_once __DIR__ . '/Vendor/autoload.php';

use Api\V1\Usercontroller\User;
use Connection\Get\Database;
use Api\V1\Agentcontroller\Agent;
use Api\V1\Paymentcontroller\Payment;
use Api\V1\Systemcontroller\Panelcontroller;

class Main
{
    private $conn;
    private $user;
    private $agent;
    private $payment;

    private $panel;
    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
        $this->user = new User;
        $this->agent = new Agent;
        $this->payment = new Payment;
        $this->panel = new Panelcontroller;
    }
    private function sendErrorResponse($code)
    {
        echo json_encode([
            "responsecode" => $code
        ]);
        mysqli_close($this->conn);
        exit();
    }
    public function userrequest($data)
    {
        switch ($data->request) {
            case '11-11':
                $this->user->Userregister($data, $this->conn);
                break;
            case '88':
                $this->user->userauth($data, $this->conn);
                break;
            case '22-22':
                $this->agent->search($data, $this->conn);
                break;
            case '1212':
                $this->user->search($data, $this->conn);
                break;
            case '1314':
                $this->payment->cashin($data, $this->conn);
                break;
            case '1516':
                $this->payment->gethispending($data, $this->conn);
                break;
            case '1517':
                $this->payment->gethisapprove($data, $this->conn);
                break;
            case 'checkpayment':
                $this->payment->searchpayment($data, $this->conn);
                break;
            case '81':
                $this->payment->setpayment($data, $this->conn);
                break;
            case 'cashout':
                $this->payment->cashout($data, $this->conn);
                break;
            case 'panelcontrol':
                $this->panel->panelcontrol($data, $this->conn);
                break;
            case 'getnumber':
                $this->panel->getnumber($data, $this->conn);
                break;
            case 'updatepassword':
                $this->user->updatepassword($data, $this->conn);
                break;
            case 'updatecode':
                $this->user->updatecode($data, $this->conn);
                break;
            case 'userauthorized':
                $this->user->userauthorized($data, $this->conn);
                break;
            default:
                $this->sendErrorResponse("401");
                break;
        }
    }
}
$raw = file_get_contents('php://input');
if (empty($raw)) {
    http_response_code(404);
    echo json_encode([
        "status" => "404 not found",
        "Auth" => "Unauthorized not found"
    ]);
    http_response_code(404);
    exit();
}
$data = json_decode($raw);
$current = new Main();
$current->userrequest($data);


?>