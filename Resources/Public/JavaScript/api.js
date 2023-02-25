const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
window.addEventListener('DOMContentLoaded', function(e) {
    if (!document.getElementById('comment-submit')) {
        return;
    }

    document.getElementById('comment-submit').addEventListener('click', function() {
        e.preventDefault();
        var form = document.getElementById('comments');

        var xhr = new XMLHttpRequest();
        xhr.open("POST", yourUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(new URLSearchParams(new FormData(form)).toString());

        xhr.onreadystatechange = function () {
            if (this.readyState != 4) return;
        
            if (this.status == 200) {
                var data = JSON.parse(this.responseText);
        
                // we get the returned data
                console.log('We get the returned data', data);
            }
        
            // end of state change: it can be after some time (async)
            console.log('End of state change: it can be after some time (async)', this.status, this.message);
        };
    })
});
