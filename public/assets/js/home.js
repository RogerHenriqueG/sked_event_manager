$(document).ready(async function () {

    const events = await fetchEvents();

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['interaction', 'dayGrid'], // Adicione outros plugins se necessário
        initialView: 'dayGridMonth', // Padrão para visualização mensal
        defaultDate: '2024-11-24',
        editable: true,
        eventLimit: true, // Permite "mais" link quando muitos eventos
        events: events,
        eventClick: function (info) {
            updateShow(info.event)
        },
        
        
    });

    calendar.render(); 
    
    $(".delete").on('click', async () => {

        const id = $(".delete").val();

        const response = await fetch('/event/'+ id,{
            method:'delete',
        });
        if (!response.ok) throw new Error('Erro ao deletar evento.');
        const res = await response.json();

        console.log(res);

        if (res.status == 'success') {
            Swal.fire({
                icon: "success",
                title: "Sucesso",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
        }else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
            return false;
        }   
    });

    $(".update").on('click', async () => {

        let form = new FormData($(".event_update")[0])

        const id = $(".delete").val();
        form.append('start_datetime', $(".start_datetime").val());
        form.append('end_datetime', $(".end_datetime").val());

        const user = form.get('user');
        const pass = form.get('password');

        const response = await fetch('/update/'+ id,{
            method:'post',
            body: form,
        });
        if (!response.ok) throw new Error('Erro ao atualizar evento.');
        const res = await response.json();

        if (res.status == 'success') {
            Swal.fire({
                icon: "success",
                title: "Sucesso",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
        }else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
            return false;
        }   
    });

    $(".create").on('click', async () => {

        let form = new FormData($(".event_create")[0])

        // form.append('start_datetime', $(".start_datetime").val());
        // form.append('end_datetime', $(".end_datetime").val());

        const user = form.get('user');
        const pass = form.get('password');

        const response = await fetch('/create',{
            method:'post',
            body: form,
        });
        if (!response.ok) throw new Error('Erro ao criar evento.');
        const res = await response.json();

        if (res.status == 'success') {
            Swal.fire({
                icon: "success",
                title: "Sucesso",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
        }else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: res.message,
                timer: 50000,
                timerProgressBar: true,
            });
            return false;
        }   
    });
});

async function updateShow(event) {

    const response = await fetch('/event/'+ event.id);
    if (!response.ok) throw new Error('Erro ao buscar eventos.');
    const myEvent = await response.json();

    $('.title').html(myEvent['data'].title)
    $('.title').val(myEvent['data'].title)
    $('.description').val(myEvent['data'].description)
    $('.start_datetime').val(myEvent['data'].start_datetime)
    $('.end_datetime').val(myEvent['data'].end_datetime)
    $('.delete').val(event.id)

    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
    myModal.show();
}

async function fetchEvents() {
    try {
        const response = await fetch('/events');
        if (!response.ok) throw new Error('Erro ao buscar eventos.');
        const events = await response.json();

        const formattedEvents = [];

        events['data'].forEach(event => {
            formattedEvents.push({
                id: event.id,
                title: event.title,
                start: event.start_datetime,
                end: event.end_datetime || null
            });
        });

        return formattedEvents;
    } catch (error) {
        console.error('Erro ao carregar eventos:', error);
        return [];
    }
}
