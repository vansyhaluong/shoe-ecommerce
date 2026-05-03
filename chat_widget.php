<!-- Chat Widget Container -->
<div id="chat-widget" class="fixed bottom-8 right-8 z-[100] font-sans">
    <!-- Chat Button -->
    <button onclick="toggleChat()" class="w-16 h-16 bg-indigo-600 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300 group">
        <svg id="chat-icon" class="w-8 h-8 text-white group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg id="close-icon" class="w-8 h-8 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="absolute bottom-20 right-0 w-[380px] h-[520px] bg-white rounded-[2rem] shadow-2xl border border-slate-100 flex flex-col overflow-hidden hidden transform translate-y-10 opacity-0 transition-all duration-500 origin-bottom-right">
        <!-- Chat Header -->
        <div class="p-6 bg-indigo-600 text-white flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h4 class="font-black italic uppercase tracking-wider">Hỗ trợ trực tuyến</h4>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest">Đang hoạt động</span>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
            <!-- Bot Welcome -->
            <div class="flex gap-3 max-w-[80%]">
                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs">S</div>
                <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-slate-100">
                    <p class="text-sm text-dark font-medium italic">Chào bạn! ShoeStore có thể giúp gì cho bạn hôm nay?</p>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-white border-t border-slate-100">
            <form id="chat-form" class="flex items-center gap-2 bg-slate-50 rounded-2xl p-1 px-4 border border-slate-200 focus-within:border-indigo-600 transition-all">
                <input type="text" id="msg-input" placeholder="Nhập tin nhắn..." class="flex-1 bg-transparent py-3 text-sm font-bold text-dark focus:outline-none placeholder:text-slate-400">
                <button type="submit" class="text-indigo-600 hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let chatOpen = false;

function toggleChat() {
    const win = document.getElementById('chat-window');
    const cIcon = document.getElementById('chat-icon');
    const xIcon = document.getElementById('close-icon');
    
    chatOpen = !chatOpen;
    if (chatOpen) {
        win.classList.remove('hidden');
        setTimeout(() => {
            win.classList.remove('translate-y-10', 'opacity-0');
        }, 10);
        cIcon.classList.add('hidden');
        xIcon.classList.remove('hidden');
        loadMessages();
    } else {
        win.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => {
            win.classList.add('hidden');
        }, 500);
        cIcon.classList.remove('hidden');
        xIcon.classList.add('hidden');
    }
}

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('msg-input');
    const msg = input.value.trim();
    if (!msg) return;

    // Hiển thị tin nhắn ngay lập tức (Optimistic UI)
    appendMessage(msg, true);
    input.value = '';

    // Gửi lên server
    fetch('chat_action.php?action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(msg)
    });
});

function appendMessage(text, isMe) {
    const container = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.className = isMe ? 'flex justify-end' : 'flex gap-3 max-w-[80%]';
    
    if (isMe) {
        div.innerHTML = `
            <div class="bg-indigo-600 text-white p-4 rounded-2xl rounded-tr-none shadow-lg max-w-[80%]">
                <p class="text-sm font-bold italic">${text}</p>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs">S</div>
            <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-slate-100">
                <p class="text-sm text-dark font-medium italic">${text}</p>
            </div>
        `;
    }
    
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function loadMessages() {
    fetch('chat_action.php?action=fetch')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('chat-messages');
            // Xóa sạch trừ cái welcome đầu tiên
            const welcome = container.firstElementChild;
            container.innerHTML = '';
            container.appendChild(welcome);
            
            data.forEach(m => {
                appendMessage(m.message, m.is_from_admin == 0);
            });
        });
}

// Auto refresh chat mỗi 5 giây khi đang mở
setInterval(() => {
    if (chatOpen) loadMessages();
}, 5000);
</script>
