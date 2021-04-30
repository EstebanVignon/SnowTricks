window.onload = function () {
    // Init data btn data attributes
    const btn = document.getElementById('load-more-comments-btn');
    const initialCommentsNbr = document.querySelectorAll("#comments-container > div").length;
    btn.dataset.currentCommentsNbr = initialCommentsNbr.toString();

    btn.addEventListener('click', function () {
        // Build query string
        let params = new URLSearchParams();
        params.append('currentComments', btn.dataset.currentCommentsNbr);

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
                const content = document.getElementById('comments-container');
                content.innerHTML = content.innerHTML + data.content;

                console.log(data.content.length)
                if (data.content.length <= 1) {
                    btn.innerText = "Plus d'autres commentaires";
                    btn.style.pointerEvents = "none";
                } else {
                    btn.innerText = "Voir Plus";
                    btn.style.pointerEvents = "auto";
                }
            })
            .catch(e => alert(e));

        // Update btn data attributes
        let newCommentsNbr = initialCommentsNbr + parseInt(btn.dataset.currentCommentsNbr);
        btn.dataset.currentCommentsNbr = newCommentsNbr.toString();
    });
}