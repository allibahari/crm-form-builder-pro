document.addEventListener('DOMContentLoaded', function () {
    const testButton = document.getElementById('test-connection-btn');
    const saveButton = document.getElementById('submit'); // The 'Save Settings' button
    const resultDiv = document.getElementById('connection-result');
    const statusDisplay = document.getElementById('connection-status-display');
    const apiUrlField = document.getElementById('crm_api_url_field');
    const licenseField = document.getElementById('crm_license_key_field');

    // === Loading effect for the "Save Settings" button ===
    if (saveButton) {
        // Find the form the save button belongs to
        const settingsForm = saveButton.closest('form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function() {
                // When the form is submitted, disable the button and show loading text
                saveButton.value = 'در حال ذخیره...';
                saveButton.disabled = true;
            });
        }
    }

    // === Loading effect for the "Test Connection" button (AJAX) ===
    if (!testButton || !resultDiv || !statusDisplay || !apiUrlField || !licenseField) return;

    const originalTestButtonText = testButton.innerHTML;

    testButton.addEventListener('click', function () {
        // Disable both buttons and show loading state
        testButton.innerHTML = '<span class="spinner is-active" style="float: right; margin-top: 5px; margin-left: 5px;"></span> در حال بررسی...';
        testButton.disabled = true;
        if (saveButton) saveButton.disabled = true;
        resultDiv.innerHTML = '';

        const data = new URLSearchParams();
        data.append('action', 'test_crm_connection');
        data.append('_ajax_nonce', crm_ajax_object.nonce);
        data.append('api_url', apiUrlField.value);
        data.append('license_key', licenseField.value);

        fetch(crm_ajax_object.ajax_url, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                resultDiv.style.color = 'green';
                resultDiv.innerHTML = '✔ ' + response.data.message;
                statusDisplay.innerHTML = '<strong>وضعیت: <span style="color: green;">✔</span> فعال و متصل</strong><p>اتصال با موفقیت تایید شد. صفحه در حال بارگذاری مجدد است...</p>';
                statusDisplay.className = 'connection-status-box status-connected';
                setTimeout(() => window.location.reload(), 2000);
            } else {
                resultDiv.style.color = 'red';
                resultDiv.innerHTML = '✖ ' + (response.data.message || 'خطای ناشناخته.');
                statusDisplay.innerHTML = '<strong>وضعیت: <span style="color: red;">✖</span> غیرفعال</strong><p>اتصال برقرار نیست.</p>';
                statusDisplay.className = 'connection-status-box status-disconnected';
            }
        })
        .catch(error => {
            resultDiv.style.color = 'red';
            resultDiv.innerHTML = 'خطای ناشناخته در ارتباط با سرور رخ داد.';
        })
        .finally(() => {
            // Re-enable buttons and restore original text
            testButton.innerHTML = originalTestButtonText;
            testButton.disabled = false;
            if (saveButton) saveButton.disabled = false;
            const spinner = resultDiv.querySelector('.spinner');
            if (spinner) spinner.remove();
        });
    });
});