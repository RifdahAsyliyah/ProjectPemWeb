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
            this.textContent;

            alert(
                "Kategori dipilih: " +
                category
            );

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

            // nanti tinggal diarahkan
            // window.location.href =
            // "detail-wisata.php";

        }
    );
});


// ==========================
// PAGINATION ACTIVE
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

sortSelect.addEventListener(
    "change",
    function(){

        alert(
            "Urutkan berdasarkan: " +
            this.value
        );
    }
);


// ==========================
// CTA BUTTON
// ==========================

const ctaButton =
document.querySelector(
    ".cta-card button"
);

ctaButton.addEventListener(
    "click",
    function(){

        alert(
            "Mengarahkan ke halaman rekomendasi wisata"
        );

    }
);


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