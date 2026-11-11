document.addEventListener("DOMContentLoaded", function () {
    var input = document.getElementById("country");
    var result = document.getElementById("result");
    var countryButton = document.getElementById("lookup");
    var citiesButton = document.getElementById("lookup-cities");

    function showMessage(title, text, isError) { // Show a short error message.
        var box = document.createElement("div");
        box.className = "message" + (isError ? " error" : "");

        var heading = document.createElement("p");
        heading.className = "message-title";
        heading.textContent = title;

        var body = document.createElement("p");
        body.textContent = text;

        box.appendChild(heading);
        box.appendChild(body);

        result.innerHTML = "";
        result.appendChild(box);
    }

    function setLoading(isLoading) { // Disable the buttons while a request is running.
        countryButton.disabled = isLoading;
        citiesButton.disabled = isLoading;
        result.setAttribute("aria-busy", isLoading ? "true" : "false");
    }
    function lookup(type) { // Open an AJAX connection to world.php and print the response into #result.
        var params = new URLSearchParams();
        params.set("country", input.value.trim());
        if (type === "cities") {
            params.set("lookup", "cities");
        }

        setLoading(true);

        fetch("world.php?" + params.toString())
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Request failed with status " + response.status);
                }
                return response.text();
            })
            .then(function (html) {
                result.innerHTML = html;
            })
            .catch(function () {
                showMessage(
                    "Something went wrong",
                    "We could not reach the lookup service. Check that Apache and MySQL are running, then try again.",
                    true
                );
            })
            .then(function () {
                setLoading(false);
            });
    }

    countryButton.addEventListener("click", function () {
        lookup("country");
    });

    citiesButton.addEventListener("click", function () {
        lookup("cities");
    });


    input.addEventListener("keydown", function (event) { // Makes the key "Enter" usable for searches
        if (event.key === "Enter") {
            event.preventDefault();
            lookup("country");
        }
    });
});