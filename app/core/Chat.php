<?php
namespace MyApp; 
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    private $clients = [];
    private $rooms = [];
    protected $messageRateLimit = 10; //Per Seconds
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
            $conn->send(json_encode(['type' => 'special', 'content' => 'Limit']));
            $conn->close();
        }
        else{
            array_push($this->rooms[$roomkey],$conn);

            if(count($this->rooms[$roomkey]) == $roomlimit){
                $specialmsg = json_encode(['type' => 'special', 'content' => 'gameready']);
                foreach($this->rooms[$roomkey] as $client) {
                    $client->send($specialmsg);
                }
            }
        }
    
        echo "New connection! ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
       
        $clientId = $from->resourceId;
        $room = str_replace('room=', '', $from->httpRequest->getUri()->getQuery());
        $numRecv = count($this->rooms[$room]) - 1;
        //Rate Limit (Thansk GPT)
        $currentTime = microtime(true);

        $decodedmsg = json_decode($msg);

        if ($decodedmsg->type == "finish") {
            $gamingtext = "Listen to all the young children from the GDR (East Germany) Boys and girls, all friends of the USSR I want to speak and talk about the organization That should educate and raise our generation Here we're all so free (in the FDJ) Here we're all so German (in the FDJ)";
            $gamingtextlength = strlen($gamingtext);
            $decodedContent = $decodedmsg->content;
            
            // Check if the lengths are equal
            if (strlen($decodedContent) !== $gamingtextlength) {
                $this->lastMessageTime[$clientId] = $currentTime;
            
                foreach($this->rooms[$room] as $client) {
                    if ($from == $client) {
                        $client->send(json_encode(['type' => 'alert', 'content' => 'BELUM SELESAI']));
                    }
                }
            } else {
                $wrongletters = 0;
        
                for ($i = 0; $i < $gamingtextlength; $i++) {
                    if ($gamingtext[$i] !== $decodedContent[$i]) {
                        $wrongletters++;
                    }
                }
                // Check if the length of $decodedmsg->content is not zero before calculating accuracy
                $accuracy = ($gamingtextlength > 0) ? (1 - $wrongletters / $gamingtextlength) * 100 : 0;
                $this->lastMessageTime[$clientId] = $currentTime;
                echo "Accuracy: $accuracy%\n";
                foreach($this->rooms[$room] as $client) {
                    if ($from !== $client) {
                        $client->send(json_encode(['type' => 'done', 'content' => 'Accuracy: '.$accuracy.'%']));
                    }
                }
            }
        }
        
        if($decodedmsg->type == "close"){
            $from->close();
        }

        if($decodedmsg->type == "start"){
            $specialmsg = json_encode(['type' => 'special', 'content' => '']);
            foreach($this->rooms[$room] as $client) {
                $client->send($specialmsg);
            }
        }
        // Check if the client has exceeded the rate limit
        if ($this->isRateLimited($clientId, $currentTime)) {
            echo "Client $clientId exceeded rate limit\n";
            return;
        }


        if(!$numRecv == 0){
            echo sprintf('Connection %d sending message "%s" in room %s to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $room, $numRecv, $numRecv == 1 ? '' : 's');

            // Update the last message time for the client
            $this->lastMessageTime[$clientId] = $currentTime;
            // Broadcast the message to other clients
            foreach($this->rooms[$room] as $client) {
                if ($from !== $client) {
                    $client->send(json_encode(['type' => 'normal', 'content' => ''.$decodedmsg->content.'']));
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
                $client->send(json_encode(['type' => 'special', 'content' => 'disconnect']));
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