<!DOCTYPE html>
<html>
<head>
    <title>User Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"><meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-3">Chat with Admin</h3>
    <div id="chat-box" class="border rounded p-3 bg-white" style="height:300px; overflow-y:auto;"></div>
    <div class="input-group mt-3">
        <input type="text" id="message" class="form-control" placeholder="Type a message">
        <button id="sendBtn" class="btn btn-primary">Send</button>
    </div>
</div>

<script>
let adminId = {{ $admin->id }};
let authId  = {{ Auth::id() }};

function loadMessages() {
    $.get("/chat/" + adminId, function(data) {
        let html = "";
        data.forEach(msg => {
            html += `<div><b>${msg.send_id == authId ? 'Me' : 'Admin'}:</b> ${msg.message}</div>`;
        });
        $("#chat-box").html(html);
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

$("#sendBtn").click(function() {
    $.post("{{ route('chat.send') }}", {
        _token: $("meta[name=csrf-token]").attr("content"),
        receiver_id: adminId,
        message: $("#message").val()
    }, function() {
        $("#message").val('');
        loadMessages();
    });
});

setInterval(loadMessages, 2000);
loadMessages();
</script>
</body>
</html>
