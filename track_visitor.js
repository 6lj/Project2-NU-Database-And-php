document.addEventListener("DOMContentLoaded", function () {

    fetch('php/generate_visitor_id.php')
        .then(response => response.json())
        .then(data => {
            const visitorId = data.visitorId; 

            checkIfBanned(visitorId)
                .then(isBanned => {
                    if (isBanned) {

                        window.location.href = 'banned.html';
                    } else {

                        logVisitor(visitorId);

                        loadAppointments();
                    }
                })
                .catch(error => {
                    console.error("Error checking if banned: " + error);
                });
        })
        .catch(error => {
            console.error("Error generating visitor ID: " + error);
        });
});

function checkIfBanned(visitorId) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "php/check_banned.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                resolve(xhr.responseText === 'true');
            } else {
                reject(xhr.statusText);
            }
        };
        xhr.send("visitorId=" + encodeURIComponent(visitorId));
    });
}

function logVisitor(visitorId) {
    const currentPage = window.location.pathname.split("/").pop(); 
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/record_visitor.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
    xhr.send("loggedIn=" + isLoggedIn + "&visitorId=" + encodeURIComponent(visitorId) + "&currentPage=" + encodeURIComponent(currentPage));

    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Visitor logged successfully!");
        } else {
            console.error("Error logging visitor: " + xhr.statusText);
        }
    };
}

function loadAppointments() {
    const appointmentList = document.getElementById('appointment-list');
    appointmentList.innerHTML = ''; 

    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];

    if (appointments.length > 0) {
        appointments.forEach(appointment => {
            const item = document.createElement('div');
            item.className = 'appointment-item';
            item.innerHTML = `
                <h4>رقم الموعد: ${appointment.id || 'غير محدد'}</h4>
                <p>التاريخ: ${appointment.date || 'غير محدد'}</p>
                <p>الوقت: ${appointment.time || 'غير محدد'}</p>
                <p>القسم: ${appointment.department || 'غير محدد'}</p>
                <p>الحالة: ${appointment.status || 'غير محدد'}</p>
            `;
            appointmentList.appendChild(item);
        });
    } else {
        appointmentList.innerHTML = '<p>لا توجد مواعيد محجوزة.</p>';
    }
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    const name = cname + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkIfBanned(visitorId) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "php/check_banned.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                resolve(xhr.responseText === 'true');
            } else {
                reject(xhr.statusText);
            }
        };
        xhr.send("visitorId=" + encodeURIComponent(visitorId));
    });
}

function logVisitor(visitorId) {
    const currentPage = window.location.pathname.split("/").pop(); 
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/record_visitor.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
    xhr.send("loggedIn=" + isLoggedIn + "&visitorId=" + encodeURIComponent(visitorId) + "&currentPage=" + encodeURIComponent(currentPage));

    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Visitor logged successfully!");
        } else {
            console.error("Error logging visitor: " + xhr.statusText);
        }
    };
}

function updateDisplayedInfo(user) {
    document.getElementById('display-username').textContent = user.username;
    document.getElementById('display-email').textContent = user.email;
    document.getElementById('display-phone').textContent = user.phone;
}

function loadAppointments() {
    const appointmentList = document.getElementById('appointment-list');
    appointmentList.innerHTML = ''; 

    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];

    if (appointments.length > 0) {
        appointments.forEach(appointment => {
            const item = document.createElement('div');
            item.className = 'appointment-item';
            item.innerHTML = `
                <h4>رقم الموعد: ${appointment.id || 'غير محدد'}</h4>
                <p>التاريخ: ${appointment.date || 'غير محدد'}</p>
                <p>الوقت: ${appointment.time || 'غير محدد'}</p>
                <p>القسم: ${appointment.department || 'غير محدد'}</p>
                <p>الحالة: ${appointment.status || 'غير محدد'}</p>
            `;
            appointmentList.appendChild(item);
        });
    } else {
        appointmentList.innerHTML = '<p>لا توجد مواعيد محجوزة.</p>';
    }
}

function saveAppointmentsToDatabase() {
    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];

    const username = document.getElementById('display-username').textContent;
    const email = document.getElementById('display-email').textContent;
    const phone = document.getElementById('display-phone').textContent;

    if (appointments.length === 0) {
        alert("لا توجد مواعيد ليتم حفظها."); 
        return;
    }

    appointments.forEach(appointment => {
        const data = {
            visitorId: getCookie("visitorId"), 
            username: username,
            email: email,
            phone: phone,
            appointmentId: appointment.id,
            date: appointment.date,
            time: appointment.time,
            department: appointment.department,
            status: appointment.status,
            loggedIn: 1 
        };

        fetch('php/record_visitor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(responseData => {
            console.log(responseData);
            if (responseData.error) {
                alert(`Error: ${responseData.error}`);
            } else {
                alert('Appointment saved successfully!');

            }
        })
        .catch(error => {
            console.error('Error saving appointment:', error);
            alert('حدث خطأ أثناء حفظ الموعد.'); 
        });
    });
}