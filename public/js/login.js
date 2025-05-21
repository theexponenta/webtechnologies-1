

const INVALID_EMAIL_OR_PASSWORD = 3;
const INVALID_CAPTCHA = 4;


const ERROR_MESSAGES = {
    [INVALID_EMAIL_OR_PASSWORD]: "Неверный email или пароль",
    [INVALID_CAPTCHA]: "Неверная капча"
}


function submitLoginForm(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/login', {
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
        console.error('Ошибка:', error);
        alert('Произошла ошибка при авторизации');
    });
}


document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login_form');
    loginForm.addEventListener('submit', submitLoginForm)
});

