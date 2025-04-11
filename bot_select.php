<!-- THIS IS CURRENTLY NOT IN USE, WAITING FOR API FROM STRATUS -->
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select a Bot</title>
    <link rel="stylesheet" href="botselect.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchBots();
        });

 function fetchBots() {
            fetch("https://your-api-endpoint.com/bots") // Replace with your actual API endpoint
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                let botList = document.getElementById("bot-list");
                if (!botList) {
                    console.error("Element with id 'bot-list' not found");
                    return;
                }
                botList.innerHTML = "";

                data.forEach(bot => {
                    let botCard = document.createElement("div");
                    botCard.classList.add("bot-card");
                    botCard.textContent = `${bot.bot_name} - ${bot.status}`;
                    botCard.onclick = function() {
                        selectBot(bot.id);
                    };
                    botList.appendChild(botCard);
                });
            })
            .catch(error => {
                console.error("Error fetching bots:", error);
                let botList = document.getElementById("bot-list");
                if (botList) {
                    botList.innerHTML = "<p>Failed to load bots. Please try again later.</p>";
                }
            });
        }

        function selectBot(botId) {
            window.location.href = `logs.php?bot_id=${botId}`;
        }
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Stratus ADV</h2>
    <a href="#">DASHBOARD</a>
    <a href="#">BOTS</a>
    <a href="#">LOGOUT</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h2>Select a Bot</h2>
        <div id="bot-list" class="bot-grid"></div>
    </div>
</div>

</body>
</html>
