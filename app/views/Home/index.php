<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <h3>Text : <?php echo($data['text']); ?></h3>
    <h2>Your current Room : <?php echo($data['room']); ?></h2> 
    <h2>Change Room : </h2>
    <form id="roomform" action="<?php echo(BASEURL)?>/" method="get">
      <input type="text" name="room" id="room">
      <input type="submit" value="Change Room"> 
    </form>
    <form id="form">
    <textarea id="box" style="width:200px;height:200px; border: 1px solid black; background-color: rgba(255,255,255,0); position:absolute; top:300px;"></textarea>
    <textarea id="placeholder" name="placeholder" style="width:200px;height:200px; border: 1px solid black; color:gray; position:absolute; top:300px; z-index:-3;" disabled></textarea>
    <textarea id="box2" style="width:200px;height:200px; border: 1px solid black; position:absolute; top:600px;" disabled></textarea>
    </form>
  </body>
  <script>
    document.getElementById('roomform').addEventListener('submit', function(e) {
      e.preventDefault();
      var room = document.getElementById('room').value;
      window.location.href = room;
    });
    //document.getElementById('placeholder').value = "<?php echo($data['text'])?>";
    var conn = new WebSocket('ws://<?php echo(getenv('SERVER_ADDR'))?>:8080?room=<?php echo $data['room']; ?>');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };
    
    conn.onmessage = function(e) {
        if(e.data === "Limit"){
          alert("Room Limit reached! Connection closed.");
          conn.close();
        }
        console.log(e.data);
        document.getElementById('box2').value = e.data;
    };
    
    var box = document.getElementById('box');

    box.addEventListener('keyup', function(event) {
      if (conn.readyState === WebSocket.OPEN) {
        conn.send(box.value);
    } else {
        console.log('WebSocket connection is not open.');
    }
    });

    setInterval(function() {
      if (conn.readyState === WebSocket.OPEN) {
        conn.send(box.value);
    } else {
        console.log('WebSocket connection is not open.');
    }
    }, 2000);

  </script>
</html>