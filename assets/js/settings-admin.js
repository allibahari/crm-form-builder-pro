document.addEventListener('DOMContentLoaded', function () {
    const testButton = document.getElementById('test-connection-btn');
    const resultDiv = document.getElementById('connection-result');
    const statusDisplay = document.getElementById('connection-status-display');
    const apiUrlField = document.getElementById('crm_api_url_field');

    if (!testButton || !resultDiv || !statusDisplay || !apiUrlField) return;

    testButton.addEventListener('click', function () {
        resultDiv.innerHTML = '<span class="spinner is-active" style="float: right; margin-top: 5px;"></span> در حال بررسی...';
        testButton.disabled = true;

        const data = new URLSearchParams();
        data.append('action', 'test_crm_connection');
        data.append('_ajax_nonce', crm_ajax_object.nonce);
        data.append('api_url', apiUrlField.value);

        fetch(crm_ajax_object.ajax_url, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                resultDiv.style.color = 'green';
                resultDiv.innerHTML = '✔ ' + response.data.message;
                statusDisplay.innerHTML = '<strong>وضعیت: <span style="color: green;">✔</span> متصل</strong><p>اتصال با موفقیت تایید شد. صفحه در حال بارگذاری مجدد است...</p>';
                statusDisplay.className = 'connection-status-box status-connected';
                setTimeout(() => window.location.reload(), 2000);
            } else {
                resultDiv.style.color = 'red';
                resultDiv.innerHTML = '✖ ' + (response.data.message || 'خطای ناشناخته.');
                statusDisplay.innerHTML = '<strong>وضعیت: <span style="color: red;">✖</span> قطع</strong><p>اتصال برقرار نیست.</p>';
                statusDisplay.className = 'connection-status-box status-disconnected';
            }
        })
        .catch(error => {
            resultDiv.style.color = 'red';
            resultDiv.innerHTML = 'خطای ناشناخته در ارتباط با سرور رخ داد.';
        })
        .finally(() => {
            testButton.disabled = false;
            const spinner = resultDiv.querySelector('.spinner');
            if (spinner) spinner.remove();
        });
    });
});