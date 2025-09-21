<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Live Chat Konsumen</h3>
        </div>
    </div>
</div>

<section id="basic-vertical-layouts" class="flex-shrink-0">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body" style="display: flex; height: 500px;">
                        <!-- Admin Live Chat -->
                        <div style="display: flex; width: 100%; height: 100%;">
                            <!-- Panel Kiri: List Pelanggan -->
                            <div id="customer-list" style="width: 30%; border-right: 1px solid #ddd; overflow-y: auto;">
                                <!-- Daftar pelanggan akan dimuat di sini -->
                            </div>

                            <!-- Panel Kanan: Chat Box -->
                            <div style="flex:1; display: flex; flex-direction: column;">
                                <div id="chat-header" style="background:#007bff; color:white; padding:10px; font-weight:bold;">
                                    Pilih pelanggan untuk chat
                                </div>
                                <div id="chat-messages" style="flex:1; padding:10px; overflow-y:auto; background:#f9f9f9;">
                                    <!-- Chat akan muncul di sini -->
                                </div>
                                <input type="text" id="chat-input" placeholder="Tulis pesan..." style="border:none; padding:10px; border-top:1px solid #ddd;" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let selectedCustomer = null;
    let lastTime = 0;
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatHeader = document.getElementById('chat-header');
    const customerList = document.getElementById('customer-list');

    // Tambahkan chat ke UI
    function appendChat(msgText, sender) {
        const msg = document.createElement('div');
        msg.textContent = msgText;
        msg.style.marginBottom = '10px';
        msg.style.padding = '5px 10px';
        msg.style.borderRadius = '5px';

        if (sender === 'admin') {
            msg.style.textAlign = 'right';
            msg.style.backgroundColor = '#DCF8C6';
        } else {
            msg.style.textAlign = 'left';
            msg.style.backgroundColor = '#EAEAEA';
        }

        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Load daftar pelanggan
    // Load daftar pelanggan (dengan polling untuk update real-time)
    function loadCustomerList() {
        // func/livechat_admin/get_customers.php
        fetch('get_customers.php')
            .then(res => res.json())
            .then(data => {
                // Urutkan pelanggan berdasarkan last_time descending
                data.sort((a, b) => b.last_time - a.last_time);

                customerList.innerHTML = '';
                data.forEach(c => {
                    const div = document.createElement('div');
                    div.textContent = c.nama;
                    div.style.padding = '10px';
                    div.style.borderBottom = '1px solid #ddd';
                    div.style.cursor = 'pointer';
                    div.onclick = () => selectCustomer(c.id, c.nama);

                    // tandai jika customer terpilih
                    if (selectedCustomer === c.id) {
                        div.style.backgroundColor = '#e2e3ff';
                    }

                    customerList.appendChild(div);
                });
            });
    }

    // Polling daftar pelanggan setiap 2 detik
    setInterval(loadCustomerList, 2000);

    // Modifikasi appendChat agar chat terbaru muncul di atas
    function appendChat(msgText, sender) {
        const msg = document.createElement('div');
        msg.textContent = msgText;
        msg.style.marginBottom = '10px';
        msg.style.padding = '5px 10px';
        msg.style.borderRadius = '5px';

        if (sender === 'admin') {
            msg.style.textAlign = 'right';
            msg.style.backgroundColor = '#DCF8C6';
        } else {
            msg.style.textAlign = 'left';
            msg.style.backgroundColor = '#EAEAEA';
        }

        // Tambahkan chat di bawah (bukan di atas)
        chatMessages.appendChild(msg);

        // Scroll otomatis ke bawah
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Pilih pelanggan untuk chat
    function selectCustomer(id, nama) {
        selectedCustomer = id;
        chatHeader.textContent = nama;
        chatMessages.innerHTML = '';
        chatInput.disabled = false;
        lastTime = 0; // reset lastTime saat pilih customer baru
        fetchChat(); // load histori chat awal
    }

    // Ambil chat baru dari server
    function fetchChat() {
        if (!selectedCustomer) return;

        fetch('get_chat_admin.php?customer_id=' + encodeURIComponent(selectedCustomer) + '&lastTime=' + lastTime)
            .then(res => res.json())
            .then(data => {
                data.chats.forEach(c => {
                    // hanya append chat baru
                    if (c.created_at > lastTime) {
                        appendChat(c.chat, c.sender_type);
                    }
                });
                if (data.lastTime > lastTime) lastTime = data.lastTime;
            });
    }

    // Polling chat setiap 2 detik
    setInterval(fetchChat, 2000);

    // Kirim chat admin
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && chatInput.value.trim() !== '' && selectedCustomer) {
            const msgText = chatInput.value;

            fetch('send_chat_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'customer_id=' + selectedCustomer + '&chat=' + encodeURIComponent(msgText)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success') alert('Gagal mengirim chat: ' + data.message);
                });

            chatInput.value = '';
        }
    });

    // Load daftar pelanggan awal
    loadCustomerList();
</script>