window.onload = function () {
    // Init data btn data attributes
    const btn = document.getElementById('load-more-tricks-btn');
    const initialTricksNbr = document.querySelectorAll("#tricks-container > div").length;
    btn.dataset.currentTricksNbr = initialTricksNbr.toString();

    btn.addEventListener('click', function () {
        // Build query string
        let params = new URLSearchParams();
        params.append('currentTricks', btn.dataset.currentTricksNbr);

        // Get Active URL
        const url = new URL(window.location.href);

        // load text
        btn.innerText = "chargement..."
        btn.style.pointerEvents = "none";

        //Ajax request
        fetch(url.pathname + "?" + params.toString() + "&ajax=1", {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('tricks-container');
                content.innerHTML = content.innerHTML + data.content;

                if (data.content.length === 0) {
                    btn.innerText = "Plus d'autres tricks";
                    btn.style.pointerEvents = "none";
                } else {
                    btn.innerText = "Voir Plus";
                    btn.style.pointerEvents = "auto";
                }
            })
            .catch(e => alert(e));

        // Update btn data attributes
        let newTricksNbr = initialTricksNbr + parseInt(btn.dataset.currentTricksNbr);
        btn.dataset.currentTricksNbr = newTricksNbr.toString();
    });
}
