document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            submitForm();
        }
    });
});

function validateForm() {
    // Add your form validation logic here
    return true;
}

function submitForm() {
    const form = document.getElementById('contact-form');
    const formData = new FormData(form);

    fetch('/submit_form.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert(result);
        form.reset();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    });
}