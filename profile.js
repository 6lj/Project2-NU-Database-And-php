document.addEventListener('DOMContentLoaded', function () {
    fetch('profile.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            const profileData = document.getElementById('profile-data');
            profileData.innerHTML = `
                <p><strong>اسم المستخدم:</strong> ${data.username}</p>
                <p><strong>البريد الإلكتروني:</strong> ${data.email}</p>
                <p><strong>رقم الجوال:</strong> ${data.phone}</p>
            `;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('profile-data').innerHTML = `<p>Error loading profile data: ${error}</p>`;
        });
});