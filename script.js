const today = new Date();
const dateSelect = document.getElementById('date-select');
const departmentSelect = document.getElementById('department-select');
const timeSlotsDiv = document.getElementById('time-slots');
const confirmButton = document.getElementById('confirm-button');
const appointmentNumberDiv = document.getElementById('appointment-number');
const appointmentIdSpan = document.getElementById('appointment-id');
const errorMessageDiv = document.getElementById('error-message');
const successMessageDiv = document.getElementById('success-message');

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

    const allTimeSlots = document.querySelectorAll('.time-slot');
    allTimeSlots.forEach(slot => {
        if (slot.classList.contains('unavailable')) {
            slot.style.backgroundColor = '#f0f0f0'; 
            slot.style.color = 'black'; 
            slot.style.cursor = 'not-allowed'; 
        } else {

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

confirmButton.addEventListener('click', () => {
    const selectedTime = document.querySelector('.time-slot.selected').textContent;
    const selectedDate = dateSelect.value;
    const selectedDepartment = departmentSelect.value;

    const selectedTime24 = convert12To24Format(selectedTime);

    const appointmentNumber = generateAppointmentNumber();

    const appointment = {
        id: appointmentNumber,
        date: selectedDate,
        time: selectedTime24,
        department: selectedDepartment,
        status: 'مؤكد'
    };

    saveAppointmentLocally(appointment);

    sendAppointmentToServer(appointment);
});

function saveAppointmentLocally(appointment) {
    const appointments = JSON.parse(localStorage.getItem('appointments')) || [];

    if (appointments.length < 3) {
        appointments.push(appointment);
        localStorage.setItem('appointments', JSON.stringify(appointments));
    } else {
        displayErrorMessage("يمكنك حجز ثلاث مواعيد فقط.");
    }
}

function sendAppointmentToServer(appointment) {
    fetch('php/save-appointment.php', {
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
            removeAppointmentLocally(appointment.id); 
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
        displayErrorMessage("الموعد محجوز مسبقا, يرجى اختيار موعد اخر");
        removeAppointmentLocally(appointment.id);
    });
}

function removeAppointmentLocally(appointmentId) {
    let appointments = JSON.parse(localStorage.getItem('appointments')) || [];
    appointments = appointments.filter(app => app.id !== appointmentId);
    localStorage.setItem('appointments', JSON.stringify(appointments));
}

function convert24To12Format(time24) {
    const [hour, minute] = time24.split(':');
    let hour12 = (hour % 12) || 12; 
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

function displayErrorMessage(message) {
    errorMessageDiv.textContent = message;
    errorMessageDiv.style.display = 'block';
    setTimeout(() => {
        errorMessageDiv.style.display = 'none';
    }, 5000);
}

function displaySuccessMessage(message) {
    successMessageDiv.textContent = message;
    successMessageDiv.style.display = 'block';
    setTimeout(() => {
        successMessageDiv.style.display = 'none';
    }, 5000);
}