
document.getElementById("logout-button").addEventListener("click", function() {

    localStorage.removeItem('appointments');


    fetch("php/logout.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
           
            window.location.href = "login.html";
        } else {
            alert("حدث خطأ أثناء تسجيل الخروج. حاول مرة أخرى.");
        }
    })
    .catch(error => {
        console.error("خطأ في تسجيل الخروج:", error);
    });
});