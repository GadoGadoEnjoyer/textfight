<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <form id="form">
        <textarea id="box" style="width:300px;height:300px; border: 1px solid black;"></textarea>
        <textarea id="box2" style="width:300px;height:300px; border: 1px solid black;" disabled></textarea>
    </form>
  </body>
  <script>
    var conn = new WebSocket('ws://<?php echo($_SERVER['SERVER_ADDR'])?>:8080?room=<?php echo $data; ?>');
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