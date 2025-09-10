@include('assistance.layout.header')

<section class="pcoded-main-container">
    <div class="pcoded-content">

        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Chat</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Chat</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card email-card">
                    <div class="card-body">
                        <div class="mail-body">
                            <div class="row">
                                <div class="col-xl-12 col-md-9">
                                    <div class="mail-body-content">
                                        <!-- Chat Window -->
                                        <div id="chat-messages" class="chat-window p-3" 
                                             style="height: 400px; overflow-y: auto; background: #f8f9fa; border-radius: 8px;">
                                            <!-- Messages will be loaded here dynamically -->
                                        </div>

                                        <!-- Message Input -->
                                        <form id="userMessageForm" class="mt-3">
                                            @csrf
                                            <input type="hidden" id="receiver_id" name="receiver_id" value="{{ $receiver->id ?? '' }}">
                                            <input type="hidden" name="send_id" value="{{ Auth::id() }}">
                                            <textarea id="userMessage" name="message" rows="1" placeholder="Type your message here..."
                                                      class="form-control mb-2"></textarea>
                                            <div class="float-right mt-2">
                                                <button type="submit" id="sendBtnUser" class="btn btn-primary">Send</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let authId = {{ Auth::id() }};
let currentUserId = {{ $admin->id ?? 'null' }}; // The user you are chatting with

function loadMessages() {
    if (!currentUserId) return;
    $.get("/chat/" + currentUserId, function(data) {
        let html = "";
        data.forEach(msg => {
            let date = new Date(msg.created_at);
            let formattedDate = date.toLocaleString("en-GB", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });

            if (msg.send_id == authId) {
                html += `
                    <div class="chat-bubble chat-right text-right mb-3">
                        <div class="p-2 bg-primary text-white rounded-lg d-inline-block">
                            <p class="mb-0">${msg.message}</p>
                            <small class="text-light">${formattedDate}</small>
                        </div>
                    </div>`;
            } else {
                html += `
                    <div class="chat-bubble chat-left mb-3">
                        <div class="p-2 bg-light rounded-lg d-inline-block">
                            <p class="mb-0">${msg.message}</p>
                            <small class="text-muted">${formattedDate}</small>
                        </div>
                    </div>`;
            }
        });
        $("#chat-messages").html(html);
        $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
    });
}

// Send message via JS/AJAX
$("#userMessageForm").submit(function(e) {
    e.preventDefault();

    if (!currentUserId) return alert("Select a user first!");

    let message = $("#userMessage").val().trim();
    if (!message) return;

    $.ajax({
        url: "{{ route('chat.send') }}",
        type: "POST",
        data: {
            receiver_id: currentUserId,
            message: message,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            $("#userMessage").val('');

            let now = new Date();
            let formattedDate = now.toLocaleString("en-GB", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });

            $("#chat-messages").append(`
                <div class="chat-bubble chat-right text-right mb-3">
                    <div class="p-2 bg-primary text-white rounded-lg d-inline-block">
                        <p class="mb-0">${message}</p>
                        <small class="text-light">${formattedDate}</small>
                    </div>
                </div>
            `);
            $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
        },
        error: function(xhr) {
            alert("Message could not be sent. Error: " + xhr.status);
        }
    });
});

// Optional: auto-refresh messages
setInterval(loadMessages, 2000);
</script>

@include('assistance.layout.footer')
