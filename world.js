document.addEventListener("DOMContentLoaded", function () {
    var input = document.getElementById("country");
    var result = document.getElementById("result");
    var lookupButton = document.getElementById("lookup");

    lookupButton.addEventListener("click", function () {
        var url = "world.php?country=" + encodeURIComponent(input.value.trim());

        fetch(url)
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Request failed: " + response.status);
                }
                return response.text();
            })
            .then(function (html) {
                result.innerHTML = html;
            })
            .catch(function () {
                result.innerHTML = "<p>Could not load results. Is Apache/MySQL running?</p>";
            });
    });
});