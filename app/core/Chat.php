<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    private $clients = [];
    private $rooms = [];
    protected $messageRateLimit = 5; //Per Seconds
    protected $lastMessageTime = [];

    public function onOpen(ConnectionInterface $conn) {
        $roomlimit = 2;

        // Store the new connection to send messages to later
        $this->clients[$conn->resourceId] = $conn;
        $roomkey = str_replace('room=', '', $conn->httpRequest->getUri()->getQuery());

        //The array must be made first for some reason idk
        
        if(!isset($this->rooms[$roomkey])){
            $this->rooms[$roomkey] = [];
        }
        if (count($this->rooms[$roomkey]) >= $roomlimit) {
            echo "Room Limit reached!";
            $conn->send("Limit");
            $conn->close();
        }
        else{
            array_push($this->rooms[$roomkey],$conn);
        }
    
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        //Rate Limit (Thansk GPT)
        $currentTime = microtime(true);
        $clientId = $from->resourceId;

        // Check if the client has exceeded the rate limit
        if ($this->isRateLimited($clientId, $currentTime)) {
            echo "Client $clientId exceeded rate limit\n";
            return;
        }

        $room = str_replace('room=', '', $from->httpRequest->getUri()->getQuery());

        $numRecv = count($this->rooms[$room]) - 1;

        if($numRecv == 0){
            echo "No one else in this room\n";
        }
        else{
            echo sprintf('Connection %d sending message "%s" in room %s to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $room, $numRecv, $numRecv == 1 ? '' : 's');

            // Update the last message time for the client
            $this->lastMessageTime[$clientId] = $currentTime;
            // Broadcast the message to other clients
            foreach($this->rooms[$room] as $client) {
                if ($from !== $client) {
                    $client->send($msg);
                }
            }
        }
    }

    private function isRateLimited($clientId, $currentTime) {
        if (!isset($this->lastMessageTime[$clientId])) {
            return false; // First message, not rate-limited
        }

        $timeSinceLastMessage = $currentTime - $this->lastMessageTime[$clientId];
        $messageRate = 1 / $this->messageRateLimit;

        return $timeSinceLastMessage < $messageRate;
    }

    public function onClose(ConnectionInterface $conn) {

        $roomid = str_replace('room=', '', $conn->httpRequest->getUri()->getQuery());
        //Thanks GPT, this basically check if the user is still on the room or nah
        if (in_array($conn, $this->rooms[$roomid])) {
            // The connection is closed, remove it, as we can no longer send it messages
            foreach ($this->rooms[$roomid] as $client) {
                $client->send("User Disconnected");
            }
        }

        unset($this->clients[$conn->resourceId]);
        $index = array_search($conn, $this->rooms[$roomid]);
        if ($index !== false) {
            unset($this->rooms[$roomid][$index]);
            $this->rooms[$roomid] = array_values($this->rooms[$roomid]); // Re-index the array
        }

        echo "Connection {$conn->resourceId} has disconnected!\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        // The connection is closed, remove it, as we can no longer send it messages

        $conn->close();
    }
    
}