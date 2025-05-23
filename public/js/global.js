const app = document.getElementById('app');

const appendAlert = (message, type) => {
    const wrapper = document.createElement('div')
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `<div>${message}</div>`,
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('');

    app.prepend(wrapper);

    setTimeout(() => wrapper.remove(), 2500);
}

const responseData = data => {
    if (data.messages) {
        Object.entries(data.messages).forEach(([index, message]) => {
            appendAlert(message, data.error ? 'danger' : 'primary');
        })
    }

    if (data.message) {
        appendAlert(data.message, data.type ?? 'success');
    }

    if (data.reload) {
        setTimeout(refreshContent, 500);
    }
}


async function refreshContent() {
    const response = await fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const html = await response.text();

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newContent = doc.getElementById('app');

    if (newContent && app) {
        app.innerHTML = newContent.innerHTML;
    }
}
