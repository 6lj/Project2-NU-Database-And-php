const today = new Date();
const dateSelect = document.getElementById('date-select');
const departmentSelect = document.getElementById('department-select');
const timeSlotsDiv = document.getElementById('time-slots');
const confirmButton = document.getElementById('confirm-button');
const appointmentNumberDiv = document.getElementById('appointment-number');
const appointmentIdSpan = document.getElementById('appointment-id');
const errorMessageDiv = document.getElementById('error-message');
const successMessageDiv = document.getElementById('success-message');

// Populate date options
for (let i = 0; i < 7; i++) {
    let nextDate = new Date(today);
    nextDate.setDate(today.getDate() + i);
    const dayNames = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
    const dayName = dayNames[nextDate.getDay()];
    const option = document.createElement('option');
    option.value = nextDate.toISOString().split('T')[0];
    option.textContent = `${dayName} - ${nextDate.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })}`;
    dateSelect.appendChild(option);
}

// Event listeners for department and date selection
dateSelect.addEventListener('change', (event) => {
    const selectedDate = event.target.value;
    showTimeSlots(selectedDate, departmentSelect.value);
});

departmentSelect.addEventListener('change', (event) => {
    const selectedDate = dateSelect.value;
    showTimeSlots(selectedDate, event.target.value);
});

function showTimeSlots(date, department) {
    timeSlotsDiv.innerHTML = '';
    confirmButton.style.display = 'none';

    const timeSlots = [
        { time: '09:00', available: true },
        { time: '10:00', available: true },
        { time: '11:00', available: true },
        { time: '12:00', available: true },
        { time: '13:00', available: true },
        { time: '14:00', available: true },
        { time: '15:00', available: true },
        { time: '16:00', available: true },
        { time: '17:00', available: true },
        { time: '18:00', available: true }
    ];

    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
    let bookedTimes = appointments.filter(app => app.date === date && app.department === department).map(app => app.time);

    timeSlots.forEach(slot => {
        const timeSlotDiv = document.createElement('div');
        timeSlotDiv.className = 'time-slot ' + (bookedTimes.includes(slot.time) ? 'unavailable' : 'available');
        timeSlotDiv.textContent = convert24To12Format(slot.time);

        timeSlotsDiv.appendChild(timeSlotDiv);
    });

    // Update CSS properties of the elements
    const allTimeSlots = document.querySelectorAll('.time-slot');
    allTimeSlots.forEach(slot => {
        if (slot.classList.contains('unavailable')) {
            slot.style.backgroundColor = '#f0f0f0'; // Light Gray background
            slot.style.color = 'black'; // Black text
            slot.style.cursor = 'not-allowed'; // Not allowed cursor
        } else {
            // Make time slots clickable
            slot.addEventListener('click', () => {
                const allAvailableSlots = document.querySelectorAll('.time-slot.available');
                allAvailableSlots.forEach(s => s.classList.remove('selected'));
                slot.classList.add('selected');
                confirmButton.style.display = 'block';
            });
        }
    });
    timeSlotsDiv.style.display = 'grid';
}

// Confirm button click
confirmButton.addEventListener('click', () => {
    const selectedTime = document.querySelector('.time-slot.selected').textContent;
    const selectedDate = dateSelect.value;
    const selectedDepartment = departmentSelect.value;

    const selectedTime24 = convert12To24Format(selectedTime);

    // Generate appointment number
    const appointmentNumber = generateAppointmentNumber();

    // Create appointment object
    const appointment = {
        id: appointmentNumber,
        date: selectedDate,
        time: selectedTime24,
        department: selectedDepartment,
        status: 'مؤكد'
    };

    // Immediately save to local storage for immediate UI update
    saveAppointmentLocally(appointment);

    // Send appointment data to server via AJAX
    sendAppointmentToServer(appointment);
});

// Function to save appointment locally
function saveAppointmentLocally(appointment) {
    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
    
    // Check if the number of appointments is less than 3
    if (appointments.length < 3) {
        appointments.push(appointment);
        localStorage.setItem('appointments', JSON.stringify(appointments));
    } else {
        displayErrorMessage("يمكنك حجز ثلاث مواعيد فقط.");
    }
}

function sendAppointmentToServer(appointment) {
    fetch('save-appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(appointment)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            displayErrorMessage(data.error);
            removeAppointmentLocally(appointment.id); // Remove the appointment from localStorage
        } else {
            appointmentIdSpan.textContent = appointment.id;
            appointmentNumberDiv.style.display = 'block';
            displaySuccessMessage(data.message);

            setTimeout(() => {
                window.location.href = 'DEV/save.html';
            }, 0);
        }
    })
    .catch(error => {
        displayErrorMessage("حدث خطأ غير متوقع.");
        removeAppointmentLocally(appointment.id);
    });
}

function removeAppointmentLocally(appointmentId) {
    let appointments = JSON.parse(localStorage.getItem('appointments')) || [];
    appointments = appointments.filter(app => app.id !== appointmentId);
    localStorage.setItem('appointments', JSON.stringify(appointments));
}

// Utility functions for time conversion
function convert24To12Format(time24) {
    const [hour, minute] = time24.split(':');
    let hour12 = (hour % 12) || 12; // Convert to 12-hour format
    const ampm = hour < 12 ? 'ص' : 'م';
    return `${hour12}:${minute} ${ampm}`;
}

function convert12To24Format(time12) {
    const [time, ampm] = time12.split(' ');
    let [hour, minute] = time.split(':');
    hour = parseInt(hour);

    if (ampm === 'م' && hour !== 12) {
        hour += 12;
    } else if (ampm === 'ص' && hour === 12) {
        hour = 0;
    }

    return `${String(hour).padStart(2, '0')}:${minute}:00`;
}

function generateAppointmentNumber() {
    return 'APP-' + Math.floor(100000 + Math.random() * 900000);
}

// Display error message
function displayErrorMessage(message) {
    errorMessageDiv.textContent = message;
    errorMessageDiv.style.display = 'block';
    setTimeout(() => {
        errorMessageDiv.style.display = 'none';
    }, 5000);
}

// Display success message
function displaySuccessMessage(message) {
    successMessageDiv.textContent = message;
    successMessageDiv.style.display = 'block';
    setTimeout(() => {
        successMessageDiv.style.display = 'none';
    }, 5000);
}