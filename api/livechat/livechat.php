<!-- Floating Chat Button -->
<div id="chat-widget">
    <button id="chat-btn">💬</button>
    <div id="chat-box" style="display: none;">
        <div id="chat-header">Live Chat</div>
        <div id="chat-messages"></div>
        <input type="text" id="chat-input" placeholder="Tulis pesan..." />
    </div>
</div>

<style>
    #chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: Arial, sans-serif;
    }

    #chat-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    #chat-box {
        display: none;
        width: 300px;
        height: 400px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    #chat-header {
        background-color: #007bff;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
    }

    #chat-messages {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        border-bottom: 1px solid #ddd;
    }

    #chat-input {
        border: none;
        padding: 10px;
        width: 100%;
        box-sizing: border-box;
        border-top: 1px solid #ddd;
    }

    /* Chat kiri/kanan */
    .message {
        margin-bottom: 10px;
        padding: 6px 10px;
        border-radius: 8px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .message.pelanggan {
        background-color: #DCF8C6;
        text-align: right;
        margin-left: auto;
    }

    .message.admin {
        background-color: #EAEAEA;
        text-align: left;
        margin-right: auto;
    }
</style>

<script>
    const chatBtn = document.getElementById('chat-btn');
    const chatBox = document.getElementById('chat-box');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');

    let lastTime = 0;

    // Toggle chatbox
    chatBtn.addEventListener('click', () => {
        chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
    });

    // Fungsi tampilkan chat
    const shownChats = new Set();

    function appendChat(msgText, sender, chatId) {
        if (chatId && shownChats.has(chatId)) return;
        if (chatId) shownChats.add(chatId);

        const msg = document.createElement('div');
        msg.textContent = msgText;
        msg.className = 'message ' + sender;
        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }


    // Load histori chat saat halaman dimuat
    function loadHistory() {
        fetch('livechat/get_chat.php')
            .then(res => res.json())
            .then(data => {
                data.chats.forEach(c => {
                    appendChat(c.chat, c.sender_type, c._id);
                    if (c.created_at > lastTime) lastTime = c.created_at;
                });
                lastTime = data.lastTime;
            });
    }

    // Kirim chat
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && chatInput.value.trim() !== '') {
            const msgText = chatInput.value;

            fetch('livechat/send_chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'chat=' + encodeURIComponent(msgText)
            }).then(res => res.json()).then(data => {
                if (data.status !== 'success') {
                    alert('Gagal mengirim chat: ' + data.message);
                }
            });

            chatInput.value = '';
        }
    });

    // polling chat baru dari server
    setInterval(() => {
        fetch('livechat/get_chat.php?lastTime=' + lastTime)
            .then(res => res.json())
            .then(data => {
                data.chats.forEach(c => {
                    // harus pakai chatId agar shownChats berfungsi
                    appendChat(c.chat, c.sender_type, c._id);

                    // update lastTime dengan chat terbaru
                    if (c.created_at > lastTime) {
                        lastTime = c.created_at;
                    }
                });
            });
    }, 500);



    loadHistory();
</script>