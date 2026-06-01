// ==========================
// CATEGORY ACTIVE
// ==========================

const categoryItems =
document.querySelectorAll(
    ".category-list li"
);

categoryItems.forEach(item => {

    item.addEventListener(
        "click",
        function(){

            categoryItems.forEach(li => {
                li.classList.remove(
                    "active"
                );
            });

            this.classList.add(
                "active"
            );

            const category =
            this.textContent
            .trim()
            .toLowerCase();

            // redirect otomatis
            switch(category){

                case "pantai":
                    window.location.href =
                    "pantai.html";
                    break;

                case "gunung":
                    window.location.href =
                    "gunung.html";
                    break;

                case "air terjun":
                    window.location.href =
                    "air-terjun.html";
                    break;

                case "budaya":
                    window.location.href =
                    "budaya.html";
                    break;

                case "pulau":
                    window.location.href =
                    "pulau.html";
                    break;

                case "kuliner":
                    window.location.href =
                    "kuliner.html";
                    break;

                case "adventure":
                    window.location.href =
                    "adventure.html";
                    break;
            }

        }
    );

});


// ==========================
// DETAIL BUTTON
// ==========================

const detailButtons =
document.querySelectorAll(
    ".detail-btn"
);

detailButtons.forEach(button => {

    button.addEventListener(
        "click",
        function(){

            const wisataName =
            this.parentElement
            .querySelector("h3")
            .textContent;

            alert(
                "Membuka detail wisata:\n" +
                wisataName
            );

            // nanti teman kamu bisa ubah ke PHP
            // contoh:
            // window.location.href =
            // "detail.php";

        }
    );

});


// ==========================
// PAGINATION
// ==========================

const paginationButtons =
document.querySelectorAll(
    ".pagination button"
);

paginationButtons.forEach(button => {

    button.addEventListener(
        "click",
        function(){

            paginationButtons
            .forEach(btn => {

                btn.classList.remove(
                    "active"
                );

            });

            if(
                this.textContent !== "‹" &&
                this.textContent !== "›"
            ){

                this.classList.add(
                    "active"
                );

            }

        }
    );

});


// ==========================
// SORT DROPDOWN
// ==========================

const sortSelect =
document.querySelector(
    ".sort-box select"
);

if(sortSelect){

    sortSelect.addEventListener(
        "change",
        function(){

            console.log(
                "Sorting:",
                this.value
            );

        }
    );

}


// ==========================
// CTA BUTTON
// ==========================

const ctaButton =
document.querySelector(
    ".cta-card button"
);

if(ctaButton){

    ctaButton.addEventListener(
        "click",
        function(){

            alert(
                "Menampilkan rekomendasi wisata terbaik!"
            );

        }
    );

}


// ==========================
// CARD HOVER EFFECT
// ==========================

const cards =
document.querySelectorAll(
    ".destination-card"
);

cards.forEach(card => {

    card.addEventListener(
        "mouseenter",
        function(){

            this.style.transform =
            "translateY(-8px)";
        }
    );

    card.addEventListener(
        "mouseleave",
        function(){

            this.style.transform =
            "translateY(0px)";
        }
    );

});