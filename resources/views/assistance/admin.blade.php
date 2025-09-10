@include('assistance.layout.header')


<!-- [ Main Content ] start -->
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

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card email-card">
                    <div class="card-header">
                        <div class="mail-header">
                            <div class="row align-items-center">
                                <!-- [ email-left section ] start -->
                                <div class="col-xl-2 col-md-3">
                                    <a href="index.html" class="b-brand">
                                        <div class="b-bg">G</div>
                                        <span class="b-title text-muted">Chat GAF Assist</span>
                                    </a>
                                </div>
                                <!-- [ email-left section ] end -->

                                <!-- [ email-right section ] start -->
                                {{-- <div class="col-xl-10 col-md-9">
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="inputGroupSelect01"><i class="feather icon-search"></i></label>
                                        </div>
                                        <select class="custom-select" id="inputGroupSelect01">
                                            <option selected>Search ...</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                    </div>
                                </div> --}}
                                <!-- [ email-right section ] end -->
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mail-body">
                            <div class="row">
                                <!-- [ users list ] start -->
                                <div class="col-xl-2 col-md-3">
                                    <ul class="mb-2 nav nav-tab flex-column nav-pills">
                                        @foreach($users as $user)
                                            <li class="nav-item mail-section">
                                                <a class="nav-link text-left user-item" href="#" data-id="{{ $user->id }}">
                                                    <span><i class="feather icon-user"></i> {{ $user->name }}</span>
                                                    <span class="float-right unread-badge">
                                                        @if($user->unread_count > 0)
                                                            <span class="badge bg-primary" style="color:white;">{{ $user->unread_count }}</span>
                                                        @endif
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                </div>
                                <!-- [ users list ] end -->

                                <!-- [ chat window ] start -->
                                <div class="col-xl-10 col-md-9">
                                    <div class="tab-content">
                                        <div class="mail-body-content">
                                            <!-- Chat Messages -->
                                            <div class="chat-window p-3" 
                                                 id="chat-messages"
                                                 style="height: 400px; overflow-y: auto; background: #f8f9fa; border-radius: 8px;">
                                                <!-- messages will load here -->
                                            </div>

                                            <!-- Message Input -->
                                            <form class="mt-3" method="POST" id="messageForm">
                                                @csrf
                                                <input type="hidden" id="receiver_id" name="receiver_id" value="">
                                                <input type="hidden" name="send_id" value="{{ Auth::id() }}">
                                                <textarea id="message" name="message" rows="1" placeholder="Type your message here..."
                                                          class="form-control mb-2"></textarea>
                                                          <button type="submit" id="sendBtn" class="btn btn-primary">Send</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ chat window ] end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>

<script>
let currentUserId = null;
let authId = {{ Auth::id() }};

// Select a user to chat with
$(".user-item").click(function(e) {
    e.preventDefault();
    currentUserId = $(this).data("id");
    
    // Update hidden input
    $("#receiver_id").val(currentUserId);

    loadMessages();
});


function refreshUnreadBadges() {
    $.get("{{ route('chat.unreadCounts') }}", function(users) {
        users.forEach(user => {
            let badgeWrapper = $('.user-item[data-id="'+user.id+'"] .unread-badge');
            if (user.unread_count > 0) {
                badgeWrapper.html('<span class="badge bg-primary" style="color:white;">'+user.unread_count+'</span>');
            } else {
                badgeWrapper.html(''); // clear badge if no unread messages
            }
        });
    });
}



// Load chat messages
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




// Send message
// Handle sending message via JS
$("#messageForm").submit(function(e) {
    e.preventDefault(); // prevent default form submission

    if (!currentUserId) {
        return alert("Select a user first!");
    }

    let message = $("#message").val().trim();
    if (!message) return; // don't send empty messages

    $.ajax({
        url: "{{ route('chat.send') }}",
        type: "POST",
        data: {
            receiver_id: currentUserId,
            message: message,
            _token: "{{ csrf_token() }}" // include CSRF token
        },
        success: function(response) {
            // Clear input
            $("#message").val('');

            // Optionally append message instantly
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

            // Scroll to bottom
            $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
        },
        error: function(xhr) {
            alert("Message could not be sent. Error: " + xhr.status);
        }
    });
});




// Auto-refresh every 2s
setInterval(function() {
    loadMessages();        // reload current chat
    refreshUnreadBadges(); // update all badges
}, 2000);

</script>


@include('assistance.layout.footer')
