<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    #box {
      width: 200px;
      height: 200px;
      border: 1px solid black;
      background-color: rgba(255, 255, 255, 0);
      position: absolute;
      top: 300px;
      overflow: auto;
    }

    #placeholder {
      width: 200px;
      height: 200px;
      border: 1px solid black;
      color: gray;
      position: absolute;
      top: 300px;
      z-index: -3;
      overflow: hidden;
      resize: none;
    }
  </style>
  </head>
  <body>
    <h3>WELCOME BACK <?php echo($data['name']); ?></h3>
    <h2>Your current Room : <?php echo($data['room']); ?></h2> 
    <h2>Change Room : </h2>
    <form id="roomform" action="<?php echo(BASEURL)?>/" method="get">
      <input type="text" name="room" id="room">
      <input type="submit" value="Change Room"> 
    </form>
    <form id="form">
      <textarea id="box" onscroll="updatebox()" oninput="updatebox()" onresize="updatebox()"></textarea>
      <textarea id="placeholder" name="placeholder" disabled></textarea>
      <textarea id="box2" style="width:200px;height:200px; border: 1px solid black; position:absolute; top:600px;" disabled></textarea>
    </form>
    <button onclick="done()">FINISHED WRITING!</button>

  </body>
  <script>
    document.getElementById('roomform').addEventListener('submit', function(e) {
      e.preventDefault();
      var room = document.getElementById('room').value;
      window.location.href = room;
    });
    document.getElementById('placeholder').value = "<?php echo($data['text'])?>";
    var conn = new WebSocket('ws://<?php echo(getenv('SERVER_ADDR'))?>:8080?room=<?php echo $data['room']; ?>');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };
    
    conn.onmessage = function(e) {
      var decodedmsg = JSON.parse(e.data);
      if(decodedmsg.type == "special"){
        if(decodedmsg.content == "Startgame"){
          alert("Game Started!");
          return;
        }
        else if(decodedmsg.content == "Limit"){
          alert("Room Limit reached! Connection closed.");
          conn.close();
        }
        else if(decodedmsg.content == "disconnect"){
          alert("Someone disconnected!");
        }
      }
        console.log(decodedmsg.content);
        document.getElementById('box2').value = decodedmsg.content;
    };
    
    var box = document.getElementById('box');

    box.addEventListener('keyup', function(event) {
      if (conn.readyState === WebSocket.OPEN) {
        var msg = <?php echo(json_encode(['type' => 'normal', 'content' => '']))?>;
        msg.content = box.value;
        conn.send(JSON.stringify(msg));
    } else {
        console.log('WebSocket connection is not open.');
    }
    });

    setInterval(function() {
      if (conn.readyState === WebSocket.OPEN) {
        var msg = <?php echo(json_encode(['type' => 'normal', 'content' => '']))?>;
        msg.content = box.value;
        conn.send(JSON.stringify(msg));
    } else {
        console.log('WebSocket connection is not open.');
    }
    }, 2000);

    function updatebox(){
      var box = document.getElementById('box');
      var placeholder = document.getElementById('placeholder');

      placeholder.style.width = box.clientWidth + "px";
      placeholder.style.height = box.clientHeight + "px";
      placeholder.scrollTop = box.scrollTop;
    }

    function done(){
      var box = document.getElementById('box');
      var donemsg = <?php echo(json_encode(['type' => 'finish', 'content' => '']))?>;
      donemsg.content = box.value;
      conn.send(JSON.stringify(donemsg));
    }

  </script>
</html>