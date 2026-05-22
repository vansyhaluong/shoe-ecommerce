<!-- Chat Widget Container -->
<div id="chat-widget" class="fixed bottom-8 right-8 z-[100] font-sans">
    <!-- Chat Button -->
    <button onclick="toggleChat()" class="w-16 h-16 bg-slate-950 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300 group border border-slate-800 relative">
        <svg id="chat-icon" class="w-7 h-7 text-white group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg id="close-icon" class="w-7 h-7 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="fixed sm:absolute bottom-24 sm:bottom-20 right-4 sm:right-0 w-[calc(100vw-2rem)] sm:w-[385px] h-[70vh] sm:h-[550px] bg-white rounded-[2rem] shadow-2xl border border-slate-100 flex flex-col overflow-hidden hidden transform translate-y-10 opacity-0 transition-all duration-500 origin-bottom-right z-[110]">
        <!-- Chat Header -->
        <div class="p-6 bg-slate-950 text-white flex items-center gap-4 border-b border-slate-800">
            <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/35 flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            </div>
            <div>
                <h4 class="font-black italic uppercase tracking-wider text-sm">Sneaker Assistant</h4>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Trợ lý AI trực tuyến</span>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
            <!-- Bot Welcome -->
            <div class="flex gap-3 max-w-[85%]">
                <div class="w-8 h-8 bg-slate-950 text-white rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs shadow-md shadow-indigo-500/10">🤖</div>
                <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-slate-100">
                    <p class="text-sm text-dark font-medium italic">Xin chào! Mình là **Sneaker Assistant**. Rất vui được hỗ trợ bạn tìm kiếm sneaker chất lượng cao, chọn size, kiểm tra giao hàng hay các dịch vụ vệ sinh giày và dây giày của ShoeStore nhé! 👟✨</p>
                </div>
            </div>
        </div>

        <!-- Quick Suggestion Buttons -->
        <div class="px-5 py-3 bg-white border-t border-slate-100/80 flex flex-wrap gap-2">
            <button onclick="sendQuickMessage('Giao hàng')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">🚚 Giao hàng</button>
            <button onclick="sendQuickMessage('Chọn size')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">👟 Chọn size</button>
            <button onclick="sendQuickMessage('Đơn hàng')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">📦 Đơn hàng</button>
            <button onclick="sendQuickMessage('Vệ sinh giày')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">✨ Vệ sinh giày</button>
            <button onclick="sendQuickMessage('Dây giày')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">🎗️ Dây giày</button>
            <button onclick="sendQuickMessage('Khuyến mãi')" class="px-3 py-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-full text-[11px] font-black text-slate-600 hover:text-indigo-600 transition-all select-none">🔥 Khuyến mãi</button>
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
let isWaitingForBot = false;

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

    submitUserMessage(msg);
    input.value = '';
});

// Gửi tin nhắn nhanh từ gợi ý
function sendQuickMessage(text) {
    submitUserMessage(text);
}

function submitUserMessage(msg) {
    if (isWaitingForBot) return;

    // Hiển thị tin nhắn ngay lập tức
    appendMessage(msg, true);
    isWaitingForBot = true;

    // Hiển thị typing indicator
    showTypingIndicator();

    // Gọi API chat_support.php
    fetch('chat_support.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(msg)
    })
    .then(res => res.json())
    .then(data => {
        // Tạo khoảng trễ giả lập 1s để bot trông thật hơn
        setTimeout(() => {
            hideTypingIndicator();
            isWaitingForBot = false;
            
            if (data.status === 'success') {
                appendMessage(data.reply, false);
            } else {
                appendMessage("Rất tiếc, mình gặp sự cố kết nối. Bạn thử lại nhé!", false);
            }
        }, 1000);
    })
    .catch(err => {
        setTimeout(() => {
            hideTypingIndicator();
            isWaitingForBot = false;
            appendMessage("Có lỗi xảy ra. Hãy kiểm tra kết nối mạng của bạn nhé!", false);
        }, 1000);
    });
}

function appendMessage(text, isMe) {
    const container = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.className = isMe ? 'flex justify-end animate-fade-in' : 'flex gap-3 max-w-[85%] animate-fade-in';
    
    // Format markdown cơ bản cho tin nhắn của bot (ví dụ **chữ đậm**)
    let formattedText = text;
    if (!isMe) {
        formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    }

    if (isMe) {
        div.innerHTML = `
            <div class="bg-indigo-600 text-white p-4 rounded-2xl rounded-tr-none shadow-md max-w-[85%]">
                <p class="text-sm font-bold italic">${formattedText}</p>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="w-8 h-8 bg-slate-950 text-white rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs shadow-md">🤖</div>
            <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-slate-100">
                <p class="text-sm text-dark font-medium italic">${formattedText}</p>
            </div>
        `;
    }
    
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function showTypingIndicator() {
    if (document.getElementById('typing-indicator')) return;

    const container = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.id = 'typing-indicator';
    div.className = 'flex gap-3 max-w-[85%] animate-fade-in';
    div.innerHTML = `
        <div class="w-8 h-8 bg-slate-950 text-white rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs shadow-md">🤖</div>
        <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 flex items-center gap-1.5">
            <span class="text-xs text-slate-400 font-bold italic mr-1">Sneaker Assistant đang trả lời</span>
            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
        </div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function hideTypingIndicator() {
    const el = document.getElementById('typing-indicator');
    if (el) {
        el.remove();
    }
}

function loadMessages() {
    fetch('chat_action.php?action=fetch')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('chat-messages');
            if (container.children.length > 1 && isWaitingForBot) return; // Đừng reload khi đang chờ bot trả lời
            
            // Giữ lại câu chào đầu tiên
            const welcome = container.firstElementChild;
            container.innerHTML = '';
            container.appendChild(welcome);
            
            data.forEach(m => {
                appendMessage(m.message, m.is_from_admin == 0);
            });
        });
}

// Auto refresh chat mỗi 5 giây khi đang mở và không bận đợi bot trả lời
setInterval(() => {
    if (chatOpen && !isWaitingForBot) loadMessages();
}, 5000);
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
