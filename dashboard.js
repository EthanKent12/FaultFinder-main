// backend to hold api key and display results of ai tests

const API_URL = ""; // needs api key
const API_KEY = "";

// fetch data from the API
async function fetchData() {
    try {
        const response = await fetch(API_URL, {
            headers: {
                "Authorization": 'Bearer ${API_KEY}', // include api key in request headers when available
                "Content-Type": "application/json",
            },
        });

        if (!response.ok) {
            throw new Error('HTTP error! Status: ${response.status}');
        }

        const data = await response.json(); // parse JSON response
        displayData(data); // display data
    }   catch (error) {
        console.error("Error fetching data:", error);
        document.getElementById("data-container").innerHTML = '<p>Error loading data: ${error.message}</p>';
    }
}

// display data on dashboard

function displayData(data) {
    const container = document.getElementById("data-container");
    container.innerHTML = ""; // clear previous content

    if (Array.isArray(data)) {
        data.forEach((item) => {
            const itemElement = document.createElement("div");
            itemElement.className = "data-item";
            itemElement.innerHTML = `
        <h3>${item.name || item.title}</h3>
        <p>${item.description || "No description available."}</p>
        <p><strong>ID:</strong> ${item.id}</p>
      `;
            container.appendChild(itemElement);
        });
    }
}

// fetch data when page loads
fetchData();