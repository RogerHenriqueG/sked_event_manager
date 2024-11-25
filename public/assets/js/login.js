$(document).ready(function () {
    $(".login").on('click', async () => {

        let form = new FormData($("#user_login")[0])

        const user = form.get('user');
        const pass = form.get('password');
        const senha = $("#senha").val();

        console.log(user, pass);

        if (!user || !pass) {
            fnMsgBad("Você deve fornecer usuário e senha válidos");
            return;
        }

        let res = await logar(form);
        res = await res.json();

        if (res.cod === 0) {
            fnMsgBad(res.message)
            return false;
        }

        if (res.cod === 1) {
            window.location.href = res.message;
        }

    });
});

async function logar(form)
{
    const response = await fetch('/login',{
        method:'post',
        body: form
    });

    return response;
}

function fnMsgBad(msg) {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: msg,
        timer: 50000,
        timerProgressBar: true,
    });
}