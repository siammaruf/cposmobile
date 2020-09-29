setTimeout(function () {
    if (typeof  rdata !== 'undefined'){
        if (rdata.status == 'true'){
            if (typeof _p !== 'undefined') {
                let socket = io("https://cposnotify.herokuapp.com");
                socket.emit('ePosNotify', _p);
            }
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Your Restaurant is not activated !',
                footer: '<a href="http://combopos.co.uk/">Why do I have this issue?</a>'
            })
        }
    }
},1000);