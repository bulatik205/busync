async function showValidResult(type) {
    let result;

    if (type === 'getMe') {
        result = await getMe(BASE_URL, VALID_TOKEN);
    }

    document.body.innerHTML +=
        `
            <div class="result">
            <h3>${type} - API valid Response:</h3>
            <pre>${JSON.stringify(result, null, 2)}</pre>
            </div>
        `;
}

async function showInvalidResult(type) {
    let result;

    if (type === 'getMe') {
        result = await getMe(BASE_URL, INVALID_TOKEN);
    }

    document.body.innerHTML +=
        `
            <div class="result">
            <h3>${type} - API invalid Response:</h3>
            <pre>${JSON.stringify(result, null, 2)}</pre>
            </div>
        `;
}

async function showEmptyResult(type) {
    let result;

    if (type === 'getMe') {
        result = await getMe(BASE_URL, EMPTY_TOKEN);
    }

    document.body.innerHTML +=
        `
            <div class="result">
            <h3>${type} - API empty Response:</h3>
            <pre>${JSON.stringify(result, null, 2)}</pre>
            </div>
        `;
}