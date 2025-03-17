document.addEventListener("DOMContentLoaded", function () {
    // Check if the visitor ID exists in localStorage
    let visitorId = localStorage.getItem("visitorId");

    // If it doesn't exist, create a new visitor ID
    if (!visitorId) {
        visitorId = 'visitor-' + Date.now(); // Generate a unique visitor ID based on the current timestamp
        localStorage.setItem("visitorId", visitorId);
    }

    // Check if the visitor is banned
    checkIfBanned(visitorId)
        .then(isBanned => {
            if (isBanned) {
                // Redirect to banned page if the user is banned
                window.location.href = 'banned.html';
            } else {
                // Proceed to log the visitor if not banned
                logVisitor(visitorId);
                // Load appointments after logging the visitor
                loadAppointments();
            }
        })
        .catch(error => {
            console.error("Error checking if banned: " + error);
        });
});

// Function to check if the visitor is banned
function checkIfBanned(visitorId) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "check_banned.php", true);
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

// Function to log the visitor
function logVisitor(visitorId) {
    const currentPage = window.location.pathname.split("/").pop(); // Get the file name
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "record_visitor.php", true);
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

// Function to display user information
function updateDisplayedInfo(user) {
    document.getElementById('display-username').textContent = user.username;
    document.getElementById('display-email').textContent = user.email;
    document.getElementById('display-phone').textContent = user.phone;
}

// Function to load appointments from localStorage
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

// Function to save appointments to the database via AJAX
function saveAppointmentsToDatabase() {
    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
    
    const username = document.getElementById('display-username').textContent;
    const email = document.getElementById('display-email').textContent;
    const phone = document.getElementById('display-phone').textContent;

    if (appointments.length === 0) {
        alert("لا توجد مواعيد ليتم حفظها."); // "No appointments to save."
        return;
    }

    appointments.forEach(appointment => {
        const data = {
            visitorId: localStorage.getItem("visitorId"), // Get the stored visitor ID
            username: username,
            email: email,
            phone: phone,
            appointmentId: appointment.id,
            date: appointment.date,
            time: appointment.time,
            department: appointment.department,
            status: appointment.status,
            loggedIn: 1 // Assuming the user is logged in
        };

        fetch('record_visitor.php', {
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
                // Optionally reload appointments or clear localStorage
            }
        })
        .catch(error => {
            console.error('Error saving appointment:', error);
            alert('حدث خطأ أثناء حفظ الموعد.'); // "An error occurred while saving the appointment."
        });
    });
}

