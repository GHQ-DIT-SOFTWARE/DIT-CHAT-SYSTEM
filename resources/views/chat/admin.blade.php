<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row">
        <div class="col-4">
            <h4>Users</h4>
            <ul id="user-list" class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item user-item" data-id="{{ $user->id }}">
                        {{ $user->name }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-8">
            <h4>Chat Window</h4>
            <div id="chat-box" class="border rounded p-3 bg-white" style="height:300px; overflow-y:auto;"></div>
            <div class="input-group mt-3">
                <input type="text" id="message" class="form-control" placeholder="Type a message">
                <button id="sendBtn" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let authId = {{ Auth::id() }};

$(".user-item").click(function() {
    currentUserId = $(this).data("id");
    loadMessages();
});

function loadMessages() {
    if (!currentUserId) return;
    $.get("/chat/" + currentUserId, function(data) {
        let html = "";
        data.forEach(msg => {
            html += `<div><b>${msg.send_id == authId ? 'Me' : 'User'}:</b> ${msg.message}</div>`;
        });
        $("#chat-box").html(html);
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

$("#sendBtn").click(function() {
    if (!currentUserId) return alert("Select a user first!");
    $.post("{{ route('chat.send') }}", {
        _token: $("meta[name=csrf-token]").attr("content"),
        receiver_id: currentUserId,
        message: $("#message").val()
    }, function() {
        $("#message").val('');
        loadMessages();
    });
});

setInterval(loadMessages, 2000);
</script>
</body>
</html>
