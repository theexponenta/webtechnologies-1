
const EMAIL_EXISTS = 1;
const INVALID_CAPTCHA = 4;

const ERROR_MESSAGES = {
    [EMAIL_EXISTS]: "Email уже зарегистрирован",
    [INVALID_CAPTCHA]: "Неверная капча"
}


function submitRegistrationForm(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/register', {
        method: 'POST',
        body: new URLSearchParams(formData),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.errorCode) {
            alert(ERROR_MESSAGES[data.errorCode]);
        } else {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        alert('Произошла ошибка при авторизации');
    });
}


document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('registration-form');
    registrationForm.addEventListener('submit', submitRegistrationForm);
});
