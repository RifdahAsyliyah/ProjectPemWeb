// ===============================
// PROFILE DROPDOWN
// ===============================
const profileBox = document.querySelector(".profile-box");

profileBox.addEventListener("click", () => {
    alert(
        "Menu profile akan dibuka.\n\n" +
        "Nanti bisa diarahkan ke:\n" +
        "- Profil Saya\n" +
        "- Favorit\n" +
        "- Logout"
    );
});


// ===============================
// SEARCH BUTTON
// ===============================
const searchButton =
document.querySelector(".search-box button");

searchButton.addEventListener("click", () => {

    const keyword =
    document.querySelector(
        ".search-input input"
    ).value;

    if(keyword.trim() === ""){

        alert(
            "Masukkan nama wisata terlebih dahulu."
        );

        return;
    }

    alert(
        `Mencari wisata: ${keyword}`
    );

});


// ===============================
// ENTER KEY SEARCH
// ===============================
const searchInput =
document.querySelector(
    ".search-input input"
);

searchInput.addEventListener(
    "keypress",
    function(event){

    if(event.key === "Enter"){

        searchButton.click();
    }
});


// ===============================
// FILTER KATEGORI
// ===============================
const categoryCards =
document.querySelectorAll(
    ".kategori-card"
);

categoryCards.forEach(card => {

    card.addEventListener(
        "click",
        function(){

        const category =
        this.innerText.trim();

        alert(
            `Kategori ${category} dipilih`
        );

    });
});


// ===============================
// DESTINATION BUTTON
// ===============================
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
            `Membuka detail wisata:\n${wisataName}`
        );

        // nanti tinggal diarahkan
        // window.location.href =
        // "detail-wisata.php";
    });
});


// ===============================
// SIDEBAR MENU
// ===============================
const menuItems =
document.querySelectorAll(
    ".sidebar-menu li"
);

menuItems.forEach(item => {

    item.addEventListener(
        "click",
        function(){

        menuItems.forEach(li => {
            li.classList.remove(
                "active"
            );
        });

        this.classList.add(
            "active"
        );
    });
});


// ===============================
// CTA BUTTON
// ===============================
const articleButton =
document.querySelector(
    ".article-btn"
);

if(articleButton){

    articleButton.addEventListener(
        "click",
        function(){

        alert(
            "Halaman artikel wisata akan dibuka."
        );
    });
}


// ===============================
// SIMPLE HOVER EFFECT
// ===============================
const destinationCards =
document.querySelectorAll(
    ".destination-card"
);

destinationCards.forEach(card => {

    card.addEventListener(
        "mouseenter",
        () => {

        card.style.transform =
        "translateY(-10px)";
    });

    card.addEventListener(
        "mouseleave",
        () => {

        card.style.transform =
        "translateY(0px)";
    });
});