<?php namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Pushbullet handler for Monolog
 */
class PushbulletHandler extends AbstractProcessingHandler
{
    /**
     * Pushbullet API URI
     */
    const API_URI = 'https://api.pushbullet.com/v2/';

    /**
     * Guzzle instance
     * @var GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Headers to be sent with each push
     * @var array
     */
    protected $headers;

    /**
     * List of devices to push to
     * @var array
     */
    protected $devices = array();

    public function __construct($accessToken = null, $level = Logger::DEBUG, $bubble = true)
    {
        if ($accessToken === null) {
            throw new Exception('You need to specify Pushbullet access token.');
        }

        $this->guzzle = new GuzzleClient(['base_uri' => self::API_URI]);
        $this->headers = array(
            'Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        );

        parent::__construct($level, $bubble);

        return $this;
    }

    /**
     * Add device(s) to the list of recipients
     * @param mixed $deviceIdens
     */
    public function addDevice($deviceIdens)
    {
        if (!is_array($deviceIdens)) $deviceIdens = array((string) $deviceIdens);

        foreach ($deviceIdens as $deviceIden) {
            if (!in_array($deviceIden, $this->devices)) {
                array_push($this->devices, $deviceIden);
            }
        }

        return $this;
    }

    public function write(array $record)
    {
        if (count($this->devices)) { // loop through and send to set devices if any present...
            foreach ($this->devices as $deviceIden) {
                $this->push($_SERVER['HTTP_HOST'], $record['message'], $deviceIden);
            }
        } else { // ...push to all devices otherwise
            $this->push($_SERVER['HTTP_HOST'], $record['message']);
        }
    }

    protected function push($title, $body, $deviceIden = null)
    {
        $payload = array('type' => 'note', 'title' => $title, 'body' => $body);

        if ($device !== null) {
            $payload['device_iden'] = $deviceIden;
        }

        $this->guzzle->post('pushes', array(
            'headers' => $this->headers,
            'json' => $payload
        ));
    }
}
