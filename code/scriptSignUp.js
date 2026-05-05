function login(){
    let nama = document.getElementById("inputNama").value;
    let output = document.getElementById("outputNama");
    let pesan = document.getElementById("pesan");
    let email = document.getElementById("inputEmail").value;
    let password = document.getElementById("inputPassword").value;
    let telp = document.getElementById("inputTelp").value;

    if(nama.trim() === ""){
        pesan.textContent = "Nama tidak boleh kosong!";
        pesan.style.color = "red";
    } else if(email.trim() === ""){
        pesan.textContent = "Email tidak boleh kosong!";
        pesan.style.color = "red";
    } else if(!validateEmail(email)){
        pesan.textContent = "Format email tidak valid!";
        pesan.style.color = "red";
    } else if(telp.trim() === ""){
        pesan.textContent = "Nomor telepon tidak boleh kosong!";
        pesan.style.color = "red";
    } else if (!validateTelp(telp)) {
        pesan.textContent = "Format nomor telepon tidak valid!";
        pesan.style.color = "red";
    } else if(password.trim() === ""){
        pesan.textContent = "Password tidak boleh kosong!";
        pesan.style.color = "red";
    } else {
        output.textContent = nama;
        pesan.textContent = "Halo, " + nama + "!";
        pesan.style.color = "purple";
    }
}
    

function validateEmail(email) {
    const re = /@/;
    return re.test(email);
}

function validateTelp(telp) {
    const re = /^(?:\+62|08)[0-9]{8,13}$/;
    return re.test(telp);
}

function ubahTema(){
    document.body.classList.toggle("dark");
}