<?php


namespace App\Controllers;


use Core\Mvc\Controller;

class ContactController extends Controller
{
    public function indexAction()
    {
        // get request ip address
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        //country
        if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $country = $_SERVER['HTTP_CF_IPCOUNTRY'];
        }

        // get request created time
        $time = date("Y-m-d H:i:s");
        // create message body and send
        $text =
            "<b>received in:</b> " . PHP_EOL . " {$time}" . PHP_EOL .
            "<b>ip address:</b> " . PHP_EOL . " {$ip}" . PHP_EOL .
            "<b>country:</b> " . PHP_EOL . " {$country}" . PHP_EOL .
            "<b>email:</b> " . PHP_EOL . " {$this->request->getPost("email")}" . PHP_EOL .
            "<b>names:</b>" . PHP_EOL . "{$this->request->getPost("names")}" . PHP_EOL .
            "<b>text:</b>" . PHP_EOL . "{$this->request->getPost("text")}" . PHP_EOL;
        $preparedQuery = http_build_query(
            [
                'chat_id' => $this->config["bot"]["channel"],
                'text' => $text,
                'parse_mode' => 'HTML' // adding a markup style display
            ]
        );
        $file = file_get_contents("https://api.telegram.org/bot{$this->config["bot"]["token"]}/sendMessage?" . $preparedQuery);

        $this->response->setStatus("success");
        $this->response->setStatusCode(200);
        $this->response->setMessage("OK");
        return $this->response->json();
    }
}