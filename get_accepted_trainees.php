<script>
    function loadAssignments() {
        fetch('accept_trainee.php') // Fetch from the updated accept_trainee.php
        .then(response => response.json())
        .then(data => {
            const appointmentsBody = document.getElementById('appointments-body');
            let html = '';

            data.forEach(assignment => {
                html += `
                    <tr>
                        <td>${assignment.name}</td>
                        <td>${assignment.phone}</td>
                        <td>${assignment.appointment}</td>
                        <td>${assignment.trainee}</td>
                    </tr>
                `;
            });

            appointmentsBody.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
    }

    window.onload = loadAssignments; // Call the function to load accepted trainees on page load
</script>