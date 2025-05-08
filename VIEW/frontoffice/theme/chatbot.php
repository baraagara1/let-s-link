<div id="chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; width: 350px; background: #00BCD4; color: white; border-radius: 10px 10px 0 0; font-family: sans-serif; z-index: 9999;">
    <div style="padding: 10px; font-weight: bold; display: flex; align-items: center;">
        🤖 Assistant Activités
    </div>
    <div id="chat-body" style="background: white; color: black; max-height: 400px; overflow-y: auto; padding: 10px;"></div>
    <form id="chat-form" style="display: flex; border-top: 1px solid #ccc;">
        <input type="text" id="chat-input" class="form-control" placeholder="Posez une question..." style="border: none; flex: 1; padding: 10px;">
        <button type="submit" class="btn btn-primary" style="border-radius: 0;">Envoyer</button>
    </form>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const chatBody = document.getElementById('chat-body');
    const userMsg = input.value.trim();
    if (!userMsg) return;

    chatBody.innerHTML += `<div style="text-align:right; margin:5px;"><strong>Vous :</strong> ${userMsg}</div>`;
    input.value = '';

    const response = await fetch('chatbot_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(userMsg)
    });
    const data = await response.json();
    if (data.reply) {
        chatBody.innerHTML += `<div style="text-align:left; margin:5px;"><strong>Bot :</strong> ${data.reply}</div>`;
        chatBody.scrollTop = chatBody.scrollHeight;
    }
});
</script>
