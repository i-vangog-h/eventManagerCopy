const username = document.getElementById('username');
const email = document.getElementById('email');
const password1 = document.getElementById('password1');
const password2 = document.getElementById('password2');

document.addEventListener("DOMContentLoaded", 
    () =>
    {
        username.addEventListener('input', validateUsername);
        password1.addEventListener('input', validatePassword);
        password2.addEventListener('input', validatePassword);
    }
);

function validateUsername(){
    const username = document.getElementById('username').value;
    if(username.length < 3){
        document.getElementById('status-text-area').innerText = 'username must be at least 3 characters long';
    }
    else{
        document.getElementById('status-text-area').innerText = '';
    }
}

function validatePassword(){
    const password1 = document.getElementById('password1').value;
    const password2 = document.getElementById('password2').value;

    if(password1.length < 6){
        document.getElementById('status-text-area').innerText = 'password must be at least 6 characters long';
    }
    else if(password1.length > 20){
        document.getElementById('status-text-area').innerText = 'password must be at most 20 characters long';
    }
    else if(password1 !== password2){
        document.getElementById('status-text-area').innerText = 'passwords do not match';
    }
    else{
        document.getElementById('status-text-area').innerText = '';
    }
}